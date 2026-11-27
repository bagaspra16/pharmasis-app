{{-- Facility search. $category: preset type. $numberExpr: optional Alpine expression giving the
     emergency number already known by the caller. All record text is bound with x-text. --}}
@if (config('facilities.enabled'))
<div x-data="facilityFinder(@js($category ?? 'care'))" @isset($numberExpr) x-init="knownNumber = {{ $numberExpr }}" @endisset
    class="rounded-3xl bg-white border border-slate-200 text-left p-5 space-y-4">
    <div>
        <h3 class="text-lg font-bold text-slate-900" x-text="l.title"></h3>
        <p class="text-sm text-slate-600" x-text="l.intro"></p>
    </div>

    <div class="rounded-xl bg-red-50 border border-red-300 px-3 py-3 space-y-2" x-show="category === 'emergency'">
        <p class="text-sm font-bold text-red-900" x-text="l.emergency_title"></p>
        <a :href="'tel:' + number" class="inline-block rounded-full bg-red-700 px-6 py-3 text-base font-bold text-white"
            x-text="fill(l.call, { number })"></a>
        <p class="text-xs text-red-900" x-show="!knownNumber && !numberCountry" x-text="l.number_default"></p>
    </div>

    <div class="flex flex-wrap gap-3">
        <label class="flex flex-col gap-1 text-xs font-semibold text-slate-700">
            <span x-text="l.category"></span>
            <select x-model="category" @change="rerun()" class="rounded-lg border-slate-300 border text-sm px-2 py-2">
                <template x-for="(label, key) in l.categories" :key="key">
                    <option :value="key" :selected="key === category" x-text="label"></option>
                </template>
            </select>
        </label>
        <label class="flex flex-col gap-1 text-xs font-semibold text-slate-700">
            <span x-text="l.radius"></span>
            <select x-model.number="radius" @change="rerun()" class="rounded-lg border-slate-300 border text-sm px-2 py-2">
                @foreach (config('facilities.radius_km') as $km)
                    <option value="{{ $km }}" @selected($km === config('facilities.default_radius_km'))>{{ $km }} km</option>
                @endforeach
            </select>
        </label>
    </div>

    <div class="space-y-2">
        {{-- Location is only requested when this button is pressed. --}}
        <button type="button" @click="useMyLocation()" :disabled="loading"
            class="w-full sm:w-auto rounded-full bg-teal-700 px-5 py-3 text-sm font-bold text-white disabled:opacity-50"
            x-text="l.use_location"></button>
        <p class="text-xs text-slate-500" x-text="l.or"></p>
        <form @submit.prevent="findPlace()" class="flex flex-col sm:flex-row gap-2">
            <input type="text" x-model="query" maxlength="200" :placeholder="l.manual" :aria-label="l.manual" autocomplete="street-address"
                class="flex-1 min-w-0 rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <button type="submit" :disabled="loading" class="rounded-full border border-teal-700 px-5 py-2 text-sm font-bold text-teal-800 disabled:opacity-50"
                x-text="l.find_place"></button>
        </form>
        <p class="text-[11px] text-slate-500" x-text="l.privacy"></p>
    </div>

    <p x-show="error" role="alert" class="rounded-xl bg-amber-50 border border-amber-300 px-3 py-2 text-sm text-amber-900">
        <span x-text="error ? l.errors[error] : ''"></span>
        <a x-show="error === 'unavailable'" :href="mapsSearch" target="_blank" rel="noopener noreferrer" class="underline font-semibold ml-1" x-text="l.maps_fallback"></a>
    </p>

    <div x-show="places.length" class="space-y-2">
        <h4 class="text-sm font-bold text-slate-900" x-text="l.choose_place"></h4>
        <template x-for="p in places" :key="p.lat + ',' + p.lon">
            <button type="button" @click="usePlace(p)" class="block w-full text-left rounded-xl border border-slate-300 px-3 py-2 text-sm break-words" x-text="p.label"></button>
        </template>
    </div>

    <p x-show="point" class="text-xs text-slate-700 break-words">
        <strong x-text="l.around + ':'"></strong> <span x-text="point ? (point.label || l.my_location) : ''"></span>
        <span x-show="point?.rough" class="block text-amber-800" x-text="point?.rough === 'device' ? fill(l.rough_device, { km: point.km }) : l.rough_place"></span>
    </p>

    <p x-show="loading" role="status" class="text-sm text-slate-600" x-text="l.searching"></p>

    <template x-if="meta && !loading">
        <div class="space-y-3">
            <p class="text-sm font-semibold text-slate-900" x-text="l.confirm"></p>
            <p x-show="meta.old_copy" class="text-xs text-amber-800" x-text="l.old_copy"></p>
            <p x-show="meta.truncated" class="text-xs text-amber-800" x-text="l.truncated"></p>

            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-700" x-show="results.length">
                <label class="flex items-center gap-1">
                    <span x-text="l.sort"></span>
                    <select x-model="sort" class="rounded-lg border border-slate-300 px-2 py-1">
                        <option value="distance" x-text="l.sort_distance"></option>
                        <option value="name" x-text="l.sort_name"></option>
                    </select>
                </label>
                <label class="flex items-center gap-1 py-2"><input type="checkbox" x-model="onlyPhone"><span x-text="l.only_phone"></span></label>
                <label class="flex items-center gap-1 py-2"><input type="checkbox" x-model="onlyHours"><span x-text="l.only_hours"></span></label>
            </div>

            <p class="text-xs text-slate-500" role="status" x-show="results.length" x-text="fill(l.count, { shown: shown().length, total: meta.total })"></p>
            <p class="text-sm text-slate-700" x-show="!results.length" x-text="l.none"></p>

            <ul class="space-y-3">
                <template x-for="f in shown()" :key="f.id">
                    <li class="rounded-2xl border border-slate-200 p-4 space-y-1 text-sm text-slate-700 break-words">
                        <h4 class="font-bold text-slate-900" x-text="f.name"></h4>
                        <p class="flex flex-wrap gap-1 text-xs">
                            <span class="rounded bg-slate-100 px-1.5 font-semibold" x-text="l.categories[f.category]"></span>
                            <span class="rounded px-1.5 font-semibold" :class="f.status === 'community' || f.status === 'unverified' ? 'bg-amber-100 text-amber-900' : 'bg-emerald-100 text-emerald-900'" x-text="l.status[f.status]"></span>
                            <span x-show="f.stale" class="rounded bg-amber-100 text-amber-900 px-1.5" x-text="l.stale"></span>
                        </p>
                        <p x-show="f.category === 'hospital'" class="text-xs font-semibold" :class="f.emergency === 'yes' ? 'text-slate-900' : 'text-amber-800'"
                            x-text="f.emergency === 'yes' ? l.emergency_yes : l.emergency_unknown"></p>
                        <p class="text-xs" x-text="fill(l.distance, { km: f.distance_km < 0.1 ? '0.1' : f.distance_km.toFixed(1) })"></p>
                        <p><span class="font-semibold" x-text="l.address + ': '"></span><span x-text="f.address || l.not_listed"></span></p>
                        <p><span class="font-semibold" x-text="l.phone + ': '"></span><span x-text="f.phone || l.not_listed"></span></p>
                        <p><span class="font-semibold" x-text="l.hours + ': '"></span><span x-text="f.opening_hours || l.not_listed"></span></p>
                        <p x-show="f.services.length"><span class="font-semibold" x-text="l.services + ': '"></span><span x-text="f.services.join(', ')"></span></p>
                        <p class="text-xs text-slate-500">
                            <span x-text="f.checked_on ? fill(l.checked_on, { date: f.checked_on }) : l.never_checked"></span>
                            <span x-show="f.source_name === 'OpenStreetMap' && f.updated_on" x-text="' · ' + fill(l.updated_on, { date: f.updated_on })"></span>
                        </p>
                        <p class="flex flex-wrap gap-2 pt-1">
                            <a :href="'https://www.google.com/maps/dir/?api=1&destination=' + f.lat + ',' + f.lon" target="_blank" rel="noopener noreferrer"
                                class="rounded-full bg-teal-700 px-4 py-2 text-xs font-bold text-white" x-text="l.directions"></a>
                            <template x-if="f.phone"><a :href="'tel:' + f.phone.replace(/[^\d+]/g, '')" class="rounded-full border border-teal-700 px-4 py-2 text-xs font-bold text-teal-800" x-text="fill(l.call, { number: f.phone })"></a></template>
                            <template x-if="f.website"><a :href="f.website" target="_blank" rel="noopener noreferrer" class="rounded-full border border-slate-300 px-4 py-2 text-xs font-bold" x-text="l.website"></a></template>
                            <template x-if="f.source_url"><a :href="f.source_url" target="_blank" rel="noopener noreferrer" class="px-2 py-2 text-xs underline text-slate-600" x-text="l.source"></a></template>
                        </p>
                    </li>
                </template>
            </ul>

            <details class="text-xs text-slate-600">
                <summary class="cursor-pointer font-semibold py-2" x-text="l.status_help"></summary>
                <template x-for="(meaning, key) in l.status_meaning" :key="key">
                    <p><strong x-text="l.status[key] + ': '"></strong><span x-text="meaning"></span></p>
                </template>
            </details>
            <p class="text-[11px] text-slate-500">
                <span x-text="fill(l.retrieved, { date: meta.retrieved_on })"></span>
                <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer" class="underline" x-text="l.attribution"></a>
            </p>
        </div>
    </template>
</div>

@endif
