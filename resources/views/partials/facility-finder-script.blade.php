{{-- Component for partials/facility-finder. Include it outside any <template>, where scripts do not run. --}}
<script>
    // Positions and results live only in this component: nothing is written to browser storage.
    function facilityFinder(preset) {
        const dictionaries = {!! \Illuminate\Support\Js::from(['en' => trans('facilities', [], 'en'), 'id' => trans('facilities', [], 'id')]) !!};
        const language = () => (window.PharmasisI18n ? window.PharmasisI18n.getLanguage() : 'en') === 'id' ? 'id' : 'en';

        return {
            lang: language(), category: preset, radius: {{ (int) config('facilities.default_radius_km') }},
            query: '', places: [], point: null, results: [], meta: null, loading: false, error: '',
            sort: 'distance', onlyPhone: false, onlyHours: false,
            knownNumber: null, fallbackNumber: @js(config('triage.default_emergency_number')), numberCountry: null,

            init() {
                window.addEventListener('pharmasis:languageChanged', () => this.lang = language());
            },
            get l() { return dictionaries[this.lang]; },
            get number() { return this.knownNumber || this.fallbackNumber; },
            get mapsSearch() {
                const near = this.point ? `${this.point.lat},${this.point.lon}` : this.query;
                return 'https://www.google.com/maps/search/' + encodeURIComponent('hospital clinic near ' + near);
            },
            fill(text, values) {
                return Object.entries(values).reduce((out, [k, v]) => out.replace(':' + k, v), text);
            },
            shown() {
                const list = this.results.filter(f => (!this.onlyPhone || f.phone) && (!this.onlyHours || f.opening_hours));
                // The server order (nearest first; listed emergency departments first) is kept.
                return this.sort === 'name' ? [...list].sort((a, b) => a.name.localeCompare(b.name)) : list;
            },
            async post(url, fields) {
                const body = new FormData();
                Object.entries(fields).forEach(([k, v]) => body.append(k, v));
                const res = await fetch(url, {
                    method: 'POST', body,
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json' },
                });
                return res.json();
            },
            useMyLocation() {
                this.error = '';
                this.places = [];
                if (!navigator.geolocation || !window.isSecureContext) { this.error = 'no_geolocation'; return; }
                this.loading = true;
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        const metres = pos.coords.accuracy || 0;
                        // Three decimals is about 100 metres: enough to rank facilities.
                        this.point = {
                            lat: pos.coords.latitude.toFixed(3), lon: pos.coords.longitude.toFixed(3), label: '', country: '',
                            rough: metres > 1000 ? 'device' : null, km: Math.round(metres / 1000),
                        };
                        this.search();
                    },
                    () => { this.loading = false; this.error = 'denied'; },
                    { enableHighAccuracy: false, timeout: 10000, maximumAge: 60000 }
                );
            },
            async findPlace() {
                this.error = '';
                this.places = [];
                if (this.query.trim().length < 3) { this.error = 'short'; return; }
                this.loading = true;
                try {
                    const data = await this.post('/facilities/geocode', { query: this.query.trim(), lang: this.lang });
                    if (!data.success) this.error = data.error === 'busy' ? 'busy' : 'unavailable';
                    else if (!data.places.length) this.error = 'no_place';
                    // Several matches: the user chooses rather than the app guessing.
                    else if (data.places.length > 1) this.places = data.places;
                    else { this.usePlace(data.places[0]); return; }
                } catch (e) { this.error = 'unavailable'; }
                this.loading = false;
                if (this.error || this.places.length) { this.meta = null; this.results = []; this.point = null; }
            },
            usePlace(p) {
                this.places = [];
                this.point = { lat: p.lat, lon: p.lon, label: p.label, country: p.country || '', rough: p.wide ? 'place' : null };
                this.search();
            },
            rerun() {
                if (this.point) this.search();
            },
            async search() {
                this.loading = true;
                this.error = '';
                this.meta = null;
                this.results = [];
                const request = this.request = (this.request || 0) + 1;
                try {
                    const fields = { lat: this.point.lat, lon: this.point.lon, radius: this.radius, category: this.category };
                    if (this.point.country) fields.country = this.point.country;
                    const data = await this.post('/facilities/search', fields);
                    // A newer search was started while this one was running.
                    if (request !== this.request) return;
                    if (data.emergency) {
                        this.numberCountry = data.emergency.country;
                        this.fallbackNumber = data.emergency.number;
                    }
                    if (!data.success) this.error = 'unavailable';
                    else { this.results = data.results; this.meta = data.meta; }
                } catch (e) {
                    if (request !== this.request) return;
                    this.error = 'unavailable';
                }
                this.loading = false;
            },
        };
    }
</script>
