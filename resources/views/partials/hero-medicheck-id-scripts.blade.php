{{-- Cek Sehat — scripts (Bahasa Indonesia + kiosk popup) --}}
<script>
    /* ══════════════ Infinite grid (mouse tracking + drift) ══════════════ */
    document.addEventListener('alpine:init', () => {
        Alpine.data('socialGrid', () => ({
            mouseX: -1000, mouseY: -1000,
            gridOffsetX: 0, gridOffsetY: 0,
            speedX: 0.5, speedY: 0.5,
            animationFrame: null,
            init() { this.animateGrid(); },
            destroy() { if (this.animationFrame) cancelAnimationFrame(this.animationFrame); },
            handleMouseMove(e) {
                const rect = this.$refs.container.getBoundingClientRect();
                this.mouseX = e.clientX - rect.left;
                this.mouseY = e.clientY - rect.top;
            },
            animateGrid() {
                this.gridOffsetX = (this.gridOffsetX + this.speedX) % 40;
                this.gridOffsetY = (this.gridOffsetY + this.speedY) % 40;
                this.animationFrame = requestAnimationFrame(() => this.animateGrid());
            }
        }));
    });

    /* ══════════════ Hero drug search ══════════════ */
    function heroSearchId() {
        return {
            query: '', results: [], open: false, loading: false, focusedIdx: -1,
            async fetch() {
                if (this.query.length < 2) { this.results = []; this.open = false; return; }
                this.loading = true;
                try { const r = await window.fetch(`/api/v1/search/instant?q=${encodeURIComponent(this.query)}`); const d = await r.json(); this.results = d.data || []; this.open = true; this.focusedIdx = -1; } catch (e) { this.results = []; }
                this.loading = false;
            },
            focusNext() { this.focusedIdx = Math.min(this.focusedIdx + 1, this.results.length - 1); },
            focusPrev() { this.focusedIdx = Math.max(this.focusedIdx - 1, -1); },
            go() {
                if (this.focusedIdx >= 0 && this.results[this.focusedIdx]) { const d = this.results[this.focusedIdx]; window.location.href = d.is_fda ? `/drugs/fda/${d.slug}` : `/drugs/${d.id}`; }
                else if (this.query) { window.location.href = `/search?q=${encodeURIComponent(this.query)}`; }
            }
        };
    }

    /* ══════════════ Pipeline steps (Bahasa Indonesia) ══════════════ */
    const PIPELINE_STEPS_ID = [
        { id: 'parse',    icon: '1', label: 'Membaca gejala & tingkat keparahan', detail: 'Mengurai teks · mengenali entitas klinis · menilai urgensi' },
        { id: 'analyze',  icon: '2', label: 'Mengenali pola kondisi', detail: 'Diagnosis banding · pemetaan · skor keyakinan per kondisi' },
        { id: 'drugs',    icon: '3', label: 'Mencocokkan obat & menghitung dosis', detail: 'Referensi formularium · obat bebas vs resep · penyesuaian dosis' },
        { id: 'interact', icon: '4', label: 'Memeriksa keamanan interaksi obat', detail: 'Pemindaian konflik · matriks kontraindikasi · peringkat alternatif' },
        { id: 'safety',   icon: '5', label: 'Validasi keamanan', detail: 'Penyaringan · pemeriksaan kewajaran klinis · penambahan disclaimer' },
        { id: 'summary',  icon: '6', label: 'Menyusun rencana pemulihan & gaya hidup', detail: 'Estimasi waktu · optimasi gaya hidup · penanda darurat' },
        { id: 'local',    icon: '7', label: 'Mencari fasilitas kesehatan terdekat', detail: 'Geolokasi IP · pemetaan fasilitas · ekstraksi kontak' },
        { id: 'done',     icon: '8', label: 'Analisis selesai — menampilkan hasil', detail: 'JSON tervalidasi · laporan siap' },
    ];

    /* ══════════════ Main component ══════════════ */
    function mediCheckHeroId() {
        return {
            activeTab: 'text',
            symptoms: '',
            recording: false,
            analyzing: false,
            showResults: false,
            showProcessing: false,
            result: null,
            error: '',
            transcription: '',
            liveTranscript: '',
            currentStep: -1,
            completedSteps: [],
            processingSteps: [],
            mediaRecorder: null,
            audioChunks: [],
            nearbyProviders: null,
            nearbyLoading: false,
            nearbyLocation: '',
            copyFeedback: '',

            /* ── Text Input Placeholder state ── */
            placeholderText: '',
            placeholderExamples: [
                'Contoh: "Saya batuk kering dan demam 38 derajat sejak kemarin..."',
                'Contoh: "Perut kanan bawah terasa sangat nyeri, mual, dan sempat muntah 2 kali..."',
                'Contoh: "Hidung tersumbat, bersin-bersin tiap pagi, ada riwayat asma ringan..."',
                'Contoh: "Kepala terasa berputar saat bangun tidur, telinga berdenging..."'
            ],
            currentPlaceholderIdx: 0,
            _placeholderTick: null,

            /* ── Journey (Infographic) state ── */
            journey: null,
            drawerOpen: false,
            drawerTitle: '',
            drawerData: null,

            /* ── Kiosk popup state ── */
            showKioskPopup: false,
            kioskDelay: 60,        // detik menunggu SETELAH output sebelum popup muncul pertama kali
            kioskDuration: 60,     // detik hitung mundur di dalam popup sebelum auto-reset
            kioskCountdown: 60,
            _kioskTick: null,
            _kioskSnooze: null,
            _kioskAppear: null,

            get steps() { return PIPELINE_STEPS_ID; },

            init() {
                // Restore state if returning from another page
                try {
                    const stored = localStorage.getItem('ceksehatCurrentResult');
                    if (stored) {
                        const parsed = JSON.parse(stored);
                        // Only restore if the result is less than 30 minutes old
                        const age = Date.now() - new Date(parsed.savedAt).getTime();
                        if (age < 30 * 60 * 1000) {
                            this.symptoms = parsed.symptoms || '';
                            this.transcription = parsed.symptoms || '';
                            this.result = parsed.result;
                            this.journey = parsed.result?.journey || null;
                            this.nearbyLocation = parsed.location || '';
                            this.nearbyProviders = parsed.nearbyProviders || null;
                            if (this.result) {
                                this.showResults = true;
                                this.applySubstitutions();
                                this.scheduleKiosk();
                                this.$nextTick(() => {
                                    setTimeout(() => {
                                        const el = document.getElementById('medicheck-output-page');
                                        if (el) {
                                            const y = el.getBoundingClientRect().top + window.scrollY - 20; // 20px padding top
                                            window.scrollTo({ top: y, behavior: 'auto' });
                                        }
                                    }, 100);
                                });
                            }
                        }
                    }
                } catch (e) {
                    // Ignore restore errors
                }
                this.initPlaceholder();
            },

            initPlaceholder() {
                this.placeholderText = this.placeholderExamples[0];
                this.typewriterPlaceholder();
            },

            typewriterPlaceholder() {
                if (this._placeholderTick) clearTimeout(this._placeholderTick);
                let currentExample = this.placeholderExamples[this.currentPlaceholderIdx];
                let isDeleting = false;
                let charIdx = 0;
                let typingSpeed = 70;

                const type = () => {
                    currentExample = this.placeholderExamples[this.currentPlaceholderIdx];
                    let currentText = '';
                    if (isDeleting) {
                        currentText = currentExample.substring(0, charIdx - 1);
                        charIdx--;
                        typingSpeed = 30;
                    } else {
                        currentText = currentExample.substring(0, charIdx + 1);
                        charIdx++;
                        typingSpeed = 50;
                    }

                    this.placeholderText = currentText;
                    
                    // Force DOM update directly to bypass Alpine reactivity quirks
                    let el = (this.$refs && this.$refs.symptomTextarea) 
                             ? this.$refs.symptomTextarea 
                             : document.querySelector('textarea[x-model="symptoms"]');
                             
                    if (el) el.setAttribute('placeholder', currentText);

                    if (!isDeleting && charIdx === currentExample.length) {
                        typingSpeed = 3500;
                        isDeleting = true;
                    } else if (isDeleting && charIdx === 0) {
                        isDeleting = false;
                        this.currentPlaceholderIdx = (this.currentPlaceholderIdx + 1) % this.placeholderExamples.length;
                        typingSpeed = 500;
                    }

                    this._placeholderTick = setTimeout(type, typingSpeed);
                };
                type();
            },

            /* ── Badge label helpers (localize AI output if it came back in English) ── */
            likelihoodLabel(v) {
                const m = { 'high': 'Tinggi', 'moderate': 'Sedang', 'medium': 'Sedang', 'low': 'Rendah' };
                return m[String(v || '').toLowerCase()] || v;
            },
            severityLabel(v) {
                const m = { 'avoid': 'Hindari', 'caution': 'Hati-hati', 'monitor': 'Pantau' };
                return m[String(v || '').toLowerCase()] || v;
            },

            /* ── Journey helpers ── */
            get hasJourney() {
                const j = this.journey;
                return !!(j && (j.zona_atas?.ai_empathy_summary
                    || j.zona_atas?.human_mapping_widget?.highlighted_areas?.length
                    || j.zona_tengah?.interactive_timeline_checklist?.length));
            },
            get energyScore() {
                const s = parseInt(this.journey?.zona_atas?.energy_score, 10);
                return isNaN(s) ? null : Math.max(0, Math.min(100, s));
            },
            energyColor() {
                const s = this.energyScore;
                if (s === null) return '#3EAEB1';
                if (s < 34) return '#EF4444';
                if (s < 67) return '#F59E0B';
                return '#10B981';
            },
            themeAccent() {
                const t = this.journey?.status_page_theme || '';
                if (t.includes('red') || t.includes('alert')) return '#EF4444';
                if (t.includes('yellow') || t.includes('warm')) return '#F59E0B';
                return '#3EAEB1';
            },
            alertLevelLabel(lvl) {
                const m = { 'CRITICAL': 'KRITIS', 'HIGH_ALERT': 'PERINGATAN TINGGI', 'MONITOR': 'PANTAU' };
                return m[lvl] || lvl;
            },
            toggleChecklist(idx) {
                const list = this.journey?.zona_tengah?.interactive_timeline_checklist;
                if (list && list[idx]) list[idx].is_done = !list[idx].is_done;
            },
            get checklistProgress() {
                const list = this.journey?.zona_tengah?.interactive_timeline_checklist || [];
                if (!list.length) return 0;
                return Math.round(list.filter(t => t.is_done).length / list.length * 100);
            },
            openDrawer(title, data) {
                this.drawerTitle = title;
                this.drawerData = data;
                this.drawerOpen = true;
            },
            closeDrawer() {
                this.drawerOpen = false;
            },
            getAreas() {
                const arr = this.journey?.zona_atas?.human_mapping_widget?.highlighted_areas || [];
                return arr.map(a => {
                    return {
                        part: a.body_part, 
                        ...a
                    };
                }).filter(a => this.bodyPos(a.part));
            },
            /* Map a body-part key to ellipse coords on SVG silhouette (viewBox 0 0 100 240) */
            bodyPos(part) {
                const map = {
                    /* === HEAD / FACE === */
                    head:             { cx: 50, cy: 17, rx: 14, ry: 15.5 },
                    face:             { cx: 50, cy: 15, rx: 10, ry: 11   },
                    eyes:             { cx: 50, cy: 13, rx:  9, ry:  4   },
                    ears:             { cx: 50, cy: 17, rx: 16, ry:  5   },
                    nose:             { cx: 50, cy: 17, rx:  4, ry:  5   },
                    mouth:            { cx: 50, cy: 21, rx:  5, ry:  3   },
                    /* === NECK / THROAT === */
                    neck:             { cx: 50, cy: 36, rx:  7, ry:  6   },
                    throat:           { cx: 50, cy: 36, rx:  7, ry:  6   },
                    /* === SHOULDERS === */
                    left_shoulder:    { cx: 29, cy: 53, rx: 10, ry:  8   },
                    right_shoulder:   { cx: 71, cy: 53, rx: 10, ry:  8   },
                    shoulders:        { cx: 50, cy: 53, rx: 28, ry:  8   },
                    /* === CHEST / UPPER TORSO === */
                    chest:            { cx: 50, cy: 78, rx: 26, ry: 18   },
                    heart:            { cx: 41, cy: 72, rx:  9, ry: 10   },
                    lungs:            { cx: 50, cy: 76, rx: 24, ry: 16   },
                    breast:           { cx: 50, cy: 78, rx: 24, ry: 16   },
                    /* === BACK === */
                    upper_back:       { cx: 50, cy: 78, rx: 26, ry: 18   },
                    lower_back:       { cx: 50, cy: 112,rx: 22, ry: 14   },
                    back:             { cx: 50, cy: 94, rx: 24, ry: 30   },
                    spine:            { cx: 50, cy: 90, rx:  5, ry: 34   },
                    /* === ABDOMEN === */
                    stomach:          { cx: 50, cy: 107,rx: 20, ry: 12   },
                    abdomen:          { cx: 50, cy: 113,rx: 22, ry: 16   },
                    liver:            { cx: 40, cy: 104,rx: 11, ry:  9   },
                    gallbladder:      { cx: 40, cy: 106,rx:  7, ry:  6   },
                    kidney:           { cx: 50, cy: 110,rx: 20, ry: 10   },
                    intestine:        { cx: 50, cy: 116,rx: 20, ry: 12   },
                    /* === PELVIS / HIPS === */
                    pelvis:           { cx: 50, cy: 138,rx: 24, ry: 13   },
                    groin:            { cx: 50, cy: 147,rx: 15, ry:  7   },
                    hip:              { cx: 50, cy: 140,rx: 26, ry: 12   },
                    left_hip:         { cx: 33, cy: 140,rx: 10, ry: 10   },
                    right_hip:        { cx: 67, cy: 140,rx: 10, ry: 10   },
                    /* === UPPER ARMS === */
                    left_arm:         { cx: 12, cy: 78,  rx:  7, ry: 20  },
                    right_arm:        { cx: 88, cy: 78,  rx:  7, ry: 20  },
                    arms:             { cx: 50, cy: 78,  rx: 44, ry: 20  },
                    /* === FOREARMS === */
                    left_forearm:     { cx: 13, cy: 118, rx:  6, ry: 18  },
                    right_forearm:    { cx: 87, cy: 118, rx:  6, ry: 18  },
                    forearms:         { cx: 50, cy: 118, rx: 44, ry: 18  },
                    /* === HANDS === */
                    left_hand:        { cx: 14, cy: 149, rx:  7, ry: 10  },
                    right_hand:       { cx: 86, cy: 149, rx:  7, ry: 10  },
                    hands:            { cx: 50, cy: 149, rx: 44, ry: 10  },
                    /* === THIGHS === */
                    left_thigh:       { cx: 35, cy: 169, rx: 11, ry: 20  },
                    right_thigh:      { cx: 65, cy: 169, rx: 11, ry: 20  },
                    thighs:           { cx: 50, cy: 169, rx: 30, ry: 20  },
                    /* === KNEES === */
                    left_knee:        { cx: 32, cy: 195, rx: 11, ry:  5  },
                    right_knee:       { cx: 68, cy: 195, rx: 11, ry:  5  },
                    knees:            { cx: 50, cy: 195, rx: 36, ry:  5  },
                    /* === LOWER LEGS / CALVES === */
                    left_leg:         { cx: 32, cy: 212, rx: 10, ry: 15  },
                    right_leg:        { cx: 68, cy: 212, rx: 10, ry: 15  },
                    legs:             { cx: 50, cy: 212, rx: 36, ry: 15  },
                    calf:             { cx: 50, cy: 212, rx: 36, ry: 15  },
                    /* === FEET === */
                    left_foot:        { cx: 32, cy: 234, rx: 13, ry:  5  },
                    right_foot:       { cx: 68, cy: 234, rx: 13, ry:  5  },
                    feet:             { cx: 50, cy: 234, rx: 38, ry:  5  },
                    ankle:            { cx: 50, cy: 230, rx: 36, ry:  4  },
                    /* === WHOLE BODY === */
                    whole_body:       { cx: 50, cy: 120, rx: 48, ry: 115 },
                    body:             { cx: 50, cy: 120, rx: 48, ry: 115 },
                };
                const key = String(part || '').toLowerCase().replace(/[\s-]/g, '_');
                return map[key] || { cx: 50, cy: 120, rx: 12, ry: 12 };
            },
            bodyPartLabel(part) {
                const m = {
                    /* Head */ head:'Kepala', face:'Wajah', eyes:'Mata', ears:'Telinga', nose:'Hidung', mouth:'Mulut',
                    /* Neck */ neck:'Leher', throat:'Tenggorokan',
                    /* Shoulders */ left_shoulder:'Bahu Kiri', right_shoulder:'Bahu Kanan', shoulders:'Bahu',
                    /* Chest */ chest:'Dada', heart:'Jantung', lungs:'Paru-paru', breast:'Dada',
                    /* Back */ upper_back:'Punggung Atas', lower_back:'Punggung Bawah', back:'Punggung', spine:'Tulang Belakang',
                    /* Abdomen */ stomach:'Lambung', abdomen:'Perut', liver:'Hati', gallbladder:'Kantung Empedu',
                    kidney:'Ginjal', intestine:'Usus',
                    /* Pelvis */ pelvis:'Panggul', groin:'Selangkangan', hip:'Pinggul', left_hip:'Pinggul Kiri', right_hip:'Pinggul Kanan',
                    /* Arms */ left_arm:'Lengan Atas Kiri', right_arm:'Lengan Atas Kanan', arms:'Lengan',
                    left_forearm:'Lengan Bawah Kiri', right_forearm:'Lengan Bawah Kanan', forearms:'Lengan Bawah',
                    left_hand:'Tangan Kiri', right_hand:'Tangan Kanan', hands:'Tangan',
                    /* Legs */ left_thigh:'Paha Kiri', right_thigh:'Paha Kanan', thighs:'Paha',
                    left_knee:'Lutut Kiri', right_knee:'Lutut Kanan', knees:'Lutut',
                    left_leg:'Betis Kiri', right_leg:'Betis Kanan', legs:'Kaki', calf:'Betis',
                    left_foot:'Kaki Kiri', right_foot:'Kaki Kanan', feet:'Kaki', ankle:'Pergelangan Kaki',
                    /* Whole */ whole_body:'Seluruh Tubuh', body:'Seluruh Tubuh',
                };
                const key = String(part || '').toLowerCase().replace(/[\s-]/g, '_');
                return m[key] || part || '—';
            },

            /* ── Voice recording ── */
            async toggleRecording() {
                if (this.analyzing) return;
                if (this.recording) { this.stopRecording(); } else { await this.startRecording(); }
            },

            async startRecording() {
                this.error = '';
                this.showResults = false;
                this.showProcessing = false;
                this.result = null;
                this.journey = null;
                this.transcription = '';
                this.liveTranscript = '';
                this.completedSteps = [];
                this.currentStep = -1;
                this.cancelKiosk();
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    this.audioChunks = [];
                    this._stream = stream;
                    const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus') ? 'audio/webm;codecs=opus'
                        : MediaRecorder.isTypeSupported('audio/webm') ? 'audio/webm' : 'audio/ogg';
                    this.mediaRecorder = new MediaRecorder(stream, { mimeType });
                    this.mediaRecorder.ondataavailable = (e) => { if (e.data.size > 0) this.audioChunks.push(e.data); };
                    this.mediaRecorder.onstop = () => {
                        stream.getTracks().forEach(t => t.stop());
                        this.stopWaveform();
                        this.processAudio();
                    };
                    this.mediaRecorder.start(200);
                    this.recording = true;
                    this.startWaveform(stream);
                } catch (e) {
                    this.error = 'Akses mikrofon ditolak. Izinkan mikrofon atau ketik gejala Anda di bawah.';
                }
            },

            startWaveform(stream) {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const src = ctx.createMediaStreamSource(stream);
                    const analyser = ctx.createAnalyser();
                    analyser.fftSize = 64;
                    src.connect(analyser);
                    this._audioCtx = ctx;
                    this._waveData = new Uint8Array(analyser.frequencyBinCount);
                    const canvas = this.$refs.waveCanvas;
                    if (!canvas) return;
                    const cctx = canvas.getContext('2d');
                    const W = canvas.width, H = canvas.height;
                    const bars = analyser.frequencyBinCount;
                    const barW = Math.floor(W / bars) - 1;
                    const draw = () => {
                        if (!this.recording) return;
                        this._rafId = requestAnimationFrame(draw);
                        analyser.getByteFrequencyData(this._waveData);
                        cctx.clearRect(0, 0, W, H);
                        this._waveData.forEach((val, i) => {
                            const barH = Math.max(3, (val / 255) * H);
                            const hue = 245 - (i / bars) * 55; // indigo → sky
                            cctx.fillStyle = `hsla(${hue}, 75%, 58%, 0.9)`;
                            cctx.beginPath();
                            cctx.roundRect(i * (barW + 1), (H - barH) / 2, barW, barH, 2);
                            cctx.fill();
                        });
                    };
                    draw();
                } catch (e) { /* graceful */ }
            },

            stopWaveform() {
                if (this._rafId) cancelAnimationFrame(this._rafId);
                if (this._audioCtx) { try { this._audioCtx.close(); } catch (e) { } }
                this._rafId = null; this._audioCtx = null;
                const canvas = this.$refs.waveCanvas;
                if (canvas) canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            },

            stopRecording() {
                if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') { this.mediaRecorder.stop(); }
                this.recording = false;
            },

            async processAudio() {
                if (this.audioChunks.length === 0) return;
                this.analyzing = true;
                this.showProcessing = true;
                this.completedSteps = [];
                this.currentStep = 0;
                const blob = new Blob(this.audioChunks, { type: this.mediaRecorder?.mimeType || 'audio/webm' });
                const formData = new FormData();
                formData.append('audio', blob, `recording.${this.getAudioExt()}`);
                formData.append('lang', 'auto');
                formData.append('mode', 'journey');
                await this.runAnalysis(formData);
            },

            getAudioExt() {
                const mime = this.mediaRecorder?.mimeType || '';
                if (mime.includes('ogg')) return 'ogg';
                if (mime.includes('mp4')) return 'mp4';
                return 'webm';
            },

            /* ── Text analysis ── */
            async analyzeText() {
                if (!this.symptoms.trim() || this.analyzing) return;
                this.error = '';
                this.showResults = false;
                this.showProcessing = true;
                this.result = null;
                this.journey = null;
                this.transcription = '';
                this.completedSteps = [];
                this.currentStep = 0;
                this.analyzing = true;
                this.cancelKiosk();
                const formData = new FormData();
                formData.append('symptoms', this.symptoms.trim());
                formData.append('lang', 'auto');
                formData.append('mode', 'journey');
                await this.runAnalysis(formData);
            },

            autoResizeTextarea() {
                const textarea = this.$refs.symptomTextarea;
                if (!textarea) return;
                textarea.style.height = 'auto';
                textarea.style.height = Math.min(textarea.scrollHeight, 240) + 'px';
            },

            handleTextareaEnter(event) {
                if (event.shiftKey) return;
                event.preventDefault();
                this.analyzeText();
            },

            /* ── Core analysis runner ── */
            async runAnalysis(formData) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                if (window.cookieConsentAccepted && window.cookieConsentAccepted()) {
                    try {
                        const locRes = await fetch('https://ipapi.co/json/');
                        if (locRes.ok) {
                            const locData = await locRes.json();
                            if (locData && locData.city) {
                                this.nearbyLocation = `${locData.city}, ${locData.region}, ${locData.country_name}`;
                            }
                        }
                    } catch (e) { /* ignore */ }
                }

                this.processingSteps = this.steps;
                const stepTimer = this.animateSteps();

                try {
                    const res = await fetch('/medicheck/analyze', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData
                    });
                    const data = await res.json();

                    clearInterval(stepTimer);
                    await this.finishSteps();

                    if (!res.ok) {
                        this.error = (typeof data.error === 'string' ? data.error : 'Analisis gagal. Silakan coba lagi.');
                        this.showProcessing = false;
                    } else {
                        this.result = data.data;
                        this.journey = data.data?.journey || null;
                        this.transcription = data.data?.symptoms || this.liveTranscript || '';

                        await this.sleep(600);
                        this.showProcessing = false;
                        this.showResults = true;
                        this.applySubstitutions();
                        this.scheduleKiosk();

                        let historyId = null;
                        if (window.cookieConsentAccepted && window.cookieConsentAccepted()) {
                            this.saveToStorage();
                            historyId = this.saveToHistory(data.data);
                        }

                        this.$nextTick(() => {
                            setTimeout(() => {
                                const el = document.getElementById('medicheck-output-page');
                                if (el) {
                                    const y = el.getBoundingClientRect().top + window.scrollY - 20; // 20px padding top
                                    window.scrollTo({ top: y, behavior: 'smooth' });
                                }
                            }, 100);
                        });

                        if (this.nearbyLocation && historyId) {
                            this.fetchNearbyProviders(
                                historyId, this.nearbyLocation,
                                data.data?.symptoms || '',
                                (data.data?.step1?.conditions || []).map(c => c.name).join(', ')
                            );
                        }
                    }
                } catch (e) {
                    clearInterval(stepTimer);
                    this.error = 'Kesalahan jaringan. Silakan coba lagi.';
                    this.showProcessing = false;
                } finally {
                    this.analyzing = false;
                }
            },

            /* ── Kiosk popup logic ──
               Alur: output muncul → tunggu kioskDelay (60s) → popup muncul dengan
               hitung mundur kioskDuration (60s) → jika tak ada aksi, reset halaman.
               "Tunggu" = sembunyikan, jadwalkan lagi 60s. "Cukup" = reset sekarang. */
            scheduleKiosk() {
                this.cancelKiosk();
                // Popup BELUM tampil — baru muncul setelah kioskDelay detik.
                this._kioskAppear = setTimeout(() => {
                    if (this.showResults) this.startKioskCountdown();
                }, this.kioskDelay * 1000);
            },

            startKioskCountdown() {
                this.clearKioskTimers();
                this.kioskCountdown = this.kioskDuration;
                this.showKioskPopup = true;
                this._kioskTick = setInterval(() => {
                    this.kioskCountdown--;
                    if (this.kioskCountdown <= 0) {
                        this.resetKiosk();
                    }
                }, 1000);
            },

            snoozeKiosk() {
                // Sembunyikan popup, beri 1 menit lagi, lalu tampilkan kembali dengan hitung mundur baru.
                this.cancelKiosk();
                this._kioskSnooze = setTimeout(() => {
                    if (this.showResults) this.startKioskCountdown();
                }, this.kioskDelay * 1000);
            },

            resetKiosk() {
                this.cancelKiosk();
                localStorage.removeItem('ceksehatCurrentResult'); // Clear state on manual reset
                window.location.reload();
            },

            clearKioskTimers() {
                if (this._kioskTick) { clearInterval(this._kioskTick); this._kioskTick = null; }
                if (this._kioskSnooze) { clearTimeout(this._kioskSnooze); this._kioskSnooze = null; }
                if (this._kioskAppear) { clearTimeout(this._kioskAppear); this._kioskAppear = null; }
            },

            cancelKiosk() {
                this.clearKioskTimers();
                this.showKioskPopup = false;
            },

            /* ── Local storage history ── */
            saveToHistory(resultData) {
                try {
                    let history = JSON.parse(localStorage.getItem('ceksehatHistory') || '[]');
                    const newRecord = {
                        id: 'cs_' + Date.now(),
                        date: new Date().toISOString(),
                        preview: resultData.symptoms?.substring(0, 80) + (resultData.symptoms?.length > 80 ? '...' : ''),
                        conditions: (resultData.step1?.conditions || []).map(c => c.name).slice(0, 2),
                        nearbyLocation: this.nearbyLocation || null,
                        nearbyProviders: null,
                        result: resultData
                    };
                    history.unshift(newRecord);
                    if (history.length > 10) history = history.slice(0, 10);
                    localStorage.setItem('ceksehatHistory', JSON.stringify(history));
                    return newRecord.id;
                } catch (e) { return null; }
            },

            patchHistoryNearby(recordId, nearbyData) {
                if (!recordId) return;
                try {
                    let history = JSON.parse(localStorage.getItem('ceksehatHistory') || '[]');
                    const idx = history.findIndex(h => h.id === recordId);
                    if (idx !== -1) {
                        history[idx].nearbyProviders = nearbyData;
                        localStorage.setItem('ceksehatHistory', JSON.stringify(history));
                    }
                } catch (e) { /* ignore */ }
            },

            /* ── Step animation ── */
            animateSteps() {
                const totalSteps = this.steps.length - 1;
                this.currentStep = 0;
                const delays = [1200, 3500, 7000, 14000, 20000, 25000];
                delays.forEach((delay, i) => {
                    setTimeout(() => {
                        if (i < totalSteps && this.analyzing) {
                            this.completedSteps.push(this.steps[i].id);
                            this.currentStep = i + 1;
                        }
                    }, delay);
                });
                return setInterval(() => { }, 99999);
            },

            async finishSteps() {
                for (let i = 0; i < this.steps.length; i++) {
                    if (!this.completedSteps.includes(this.steps[i].id)) {
                        this.completedSteps.push(this.steps[i].id);
                        this.currentStep = i;
                        await this.sleep(120);
                    }
                }
                this.currentStep = this.steps.length;
                await this.sleep(400);
            },

            sleep(ms) { return new Promise(r => setTimeout(r, ms)); },

            /* ── Nearby providers ── */
            async fetchNearbyProviders(historyId, location, symptoms, conditions) {
                this.nearbyLoading = true;
                this.nearbyProviders = null;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                try {
                    const body = new FormData();
                    body.append('location', location);
                    if (symptoms) body.append('symptoms', symptoms.substring(0, 300));
                    if (conditions) body.append('conditions', conditions);
                    const res = await fetch('/medicheck/nearby', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body
                    });
                    const data = await res.json();
                    this.nearbyProviders = data;
                    this.patchHistoryNearby(historyId, data);
                } catch (e) {
                    const q = encodeURIComponent('rumah sakit klinik dekat ' + location);
                    this.nearbyProviders = { success: false, providers: [], maps_search_url: `https://www.google.com/maps/search/${q}` };
                } finally {
                    this.nearbyLoading = false;
                }
            },

            /* ── Drug auto-swap ── */
            applySubstitutions() {
                this.$nextTick(() => {
                    const interactions = this.result?.step3?.interactions || [];
                    interactions.forEach(inter => {
                        if ((inter.severity !== 'Avoid' && inter.severity !== 'Hindari') || !inter.substitute) return;
                        [inter.drug_a, inter.drug_b].forEach(drugName => {
                            const card = document.querySelector(`[data-drug-name="${drugName}"]`);
                            if (!card) return;
                            const label = card.querySelector('.drug-name-label');
                            if (label && !label.classList.contains('line-through')) {
                                label.classList.add('line-through', 'text-red-400');
                                const badge = document.createElement('span');
                                badge.className = 'text-[10px] bg-warm/15 text-warm px-1.5 py-0.5 rounded font-medium ml-2';
                                badge.textContent = '🔄 Diganti otomatis';
                                label.parentElement.appendChild(badge);
                                const sub = document.createElement('p');
                                sub.className = 'text-xs text-health mt-1 font-medium';
                                sub.textContent = `→ Diganti dengan: ${inter.substitute}`;
                                card.appendChild(sub);
                            }
                        });
                    });
                });
            },

            /* ── Save current result to localStorage ── */
            saveToStorage() {
                if (!this.result) return;
                try {
                    const payload = {
                        id: 'cs_' + Date.now(),
                        savedAt: new Date().toISOString(),
                        symptoms: this.transcription || this.symptoms || '',
                        location: this.nearbyLocation || null,
                        nearbyProviders: this.nearbyProviders || null,
                        result: this.result
                    };
                    localStorage.setItem('ceksehatCurrentResult', JSON.stringify(payload));
                } catch (e) { /* ignore */ }
            },

            /* ── Plain-text report ── */
            buildReport() {
                const r = this.result;
                if (!r) return '';
                const watermark = '\nDibuat oleh Pharmasis Cek Sehat\nhttps://pharmasis.id\n';
                let text = 'LAPORAN CEK SEHAT\n';
                text += `Pharmasis Cek Sehat AI\n`;
                text += `Tanggal: ${new Date().toLocaleString('id-ID')}\n`;
                text += `Gejala: ${this.transcription || this.symptoms || 'N/A'}\n\n`;

                if (r.step1?.conditions?.length) {
                    text += 'KEMUNGKINAN DIAGNOSIS\n';
                    r.step1.conditions.forEach(c => { text += `[${c.likelihood}] ${c.name}\n  ${c.description}\n\n`; });
                    if (r.step1.summary) text += `Ringkasan: ${r.step1.summary}\n\n`;
                }
                if (r.step2?.drugs?.length) {
                    text += 'REKOMENDASI OBAT\n';
                    r.step2.drugs.forEach(d => {
                        text += `${d.name} (${d.generic})\n`;
                        text += `  Golongan: ${d.drug_class || 'N/A'} | ${d.otc ? 'Obat Bebas' : 'Perlu Resep'}\n`;
                        text += `  Dosis: ${d.dose || 'N/A'} | Frekuensi: ${d.frequency || 'N/A'}\n`;
                        text += `  Kegunaan: ${d.purpose || 'N/A'}\n`;
                        if (d.mechanism) text += `  Cara Kerja: ${d.mechanism}\n`;
                        if (d.how_to_take) text += `  Cara Pakai: ${d.how_to_take}\n`;
                        if (d.side_effects?.length) text += `  Efek Samping: ${d.side_effects.join(', ')}\n`;
                        if (d.contraindications?.length) text += `  Kontraindikasi: ${d.contraindications.join(', ')}\n`;
                        text += '\n';
                    });
                    if (r.step2.pharmacist_notes) text += `Catatan Apoteker: ${r.step2.pharmacist_notes}\n\n`;
                }
                if (r.step3?.interactions?.length) {
                    text += 'INTERAKSI OBAT\n';
                    r.step3.interactions.forEach(i => {
                        text += `[${i.severity}] ${i.drug_a} + ${i.drug_b}\n  Efek: ${i.effect}\n`;
                        if (i.substitute) text += `  Alternatif: ${i.substitute}\n`;
                        text += '\n';
                    });
                }
                if (r.step4) {
                    text += 'RENCANA PEMULIHAN\n';
                    if (r.step4.recovery_timeline) text += `Perkiraan Waktu: ${r.step4.recovery_timeline}\n`;
                    if (r.step4.lifestyle_tips?.length) text += `Gaya Hidup: ${r.step4.lifestyle_tips.join('; ')}\n`;
                    if (r.step4.diet_recommendations?.length) text += `Makanan: ${r.step4.diet_recommendations.join('; ')}\n`;
                    if (r.step4.warning_signs?.length) text += `Tanda Bahaya: ${r.step4.warning_signs.join('; ')}\n`;
                    if (r.step4.follow_up) text += `Tindak Lanjut: ${r.step4.follow_up}\n`;
                    if (r.step4.disclaimer) text += `Disclaimer: ${r.step4.disclaimer}\n`;
                    text += '\n';
                }
                text += watermark;
                return text;
            },

            async copyResult() {
                const text = this.buildReport();
                if (!text) return;
                try {
                    await navigator.clipboard.writeText(text);
                    this.copyFeedback = 'Tersalin!';
                    setTimeout(() => this.copyFeedback = '', 2000);
                } catch (e) {
                    const ta = document.createElement('textarea');
                    ta.value = text; document.body.appendChild(ta); ta.select();
                    document.execCommand('copy'); document.body.removeChild(ta);
                    this.copyFeedback = 'Tersalin!';
                    setTimeout(() => this.copyFeedback = '', 2000);
                }
            },

            async shareResult() {
                const text = this.buildReport();
                if (!text) return;
                const shareData = { title: 'Laporan Cek Sehat', text: text };
                if (navigator.canShare && navigator.canShare(shareData)) {
                    try { await navigator.share(shareData); }
                    catch (e) {
                        if (e.name !== 'AbortError') {
                            await navigator.clipboard.writeText(text);
                            this.copyFeedback = 'Dibagikan!';
                            setTimeout(() => this.copyFeedback = '', 2000);
                        }
                    }
                } else {
                    await navigator.clipboard.writeText(text);
                    this.copyFeedback = 'Tersalin untuk dibagikan!';
                    setTimeout(() => this.copyFeedback = '', 2000);
                }
            },

            /* ── Build print HTML ── */
            buildPrintHTML() {
                const r = this.result;
                if (!r) return '';
                const dateStr = new Date().toLocaleString('id-ID');
                const symptoms = this.transcription || this.symptoms || 'N/A';
                const logoUrl = window.location.origin + '/images/icon.png';
                let html = '';
                html += '<div style="display:flex;align-items:center;gap:12px;border-bottom:2px solid #0d9488;padding-bottom:1rem;margin-bottom:2rem;">';
                html += '<img src="' + logoUrl + '" alt="Pharmasis" style="width:40px;height:40px;object-fit:contain;border-radius:8px;" />';
                html += '<div>';
                html += '<h1 style="font-size:1.3rem;font-weight:800;color:#0d4f52;margin:0;font-family:system-ui,sans-serif;">Pharmasis Cek Sehat</h1>';
                html += '<p style="font-size:0.7rem;color:#64748b;margin:0.2rem 0 0;">Laporan Analisis Gejala &middot; ' + dateStr + '</p>';
                html += '</div>';
                html += '</div>';
                html += '<div style="margin-bottom:1.5rem;background:#f0fdf9;border:1px solid #99f6e4;border-radius:0.5rem;padding:1rem;">';
                html += '<p style="font-size:0.7rem;font-weight:700;color:#0d9488;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 0.5rem;">Gejala yang disampaikan</p>';
                html += '<p style="font-size:0.9rem;color:#134e4a;margin:0;">' + this._esc(symptoms) + '</p>';
                html += '</div>';

                if (r.step1?.conditions?.length) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #0d9488;padding-left:0.5rem;">Kemungkinan Diagnosis</h2>';
                    r.step1.conditions.forEach(c => {
                        html += '<div style="margin-bottom:0.75rem;border:1px solid #e2e8f0;border-radius:0.5rem;padding:0.75rem;background:#fff;">';
                        html += '<p style="font-size:0.85rem;font-weight:700;color:#1e293b;margin:0 0 0.25rem;">[' + this._esc(c.likelihood) + '] ' + this._esc(c.name) + '</p>';
                        html += '<p style="font-size:0.8rem;color:#475569;margin:0;">' + this._esc(c.description) + '</p>';
                        html += '</div>';
                    });
                    if (r.step1.summary) html += '<p style="font-size:0.8rem;color:#64748b;font-style:italic;margin:0.5rem 0 0;">' + this._esc(r.step1.summary) + '</p>';
                }
                if (r.step2?.drugs?.length) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #0d9488;padding-left:0.5rem;">Rekomendasi Obat</h2>';
                    r.step2.drugs.forEach(d => {
                        html += '<div style="margin-bottom:1rem;border:1px solid #e2e8f0;border-radius:0.5rem;padding:1rem;background:#fff;page-break-inside:avoid;">';
                        html += '<p style="font-size:0.9rem;font-weight:700;color:#0f172a;margin:0 0 0.5rem;">' + this._esc(d.name) + ' <span style="font-size:0.7rem;font-weight:500;color:#64748b;">(' + this._esc(d.generic || '') + ')</span></p>';
                        html += '<table style="width:100%;border-collapse:collapse;font-size:0.8rem;margin-bottom:0.5rem;">';
                        if (d.dose) html += '<tr><td style="padding:0.25rem 0;color:#64748b;width:30%;">Dosis</td><td style="padding:0.25rem 0;color:#334155;font-weight:600;">' + this._esc(d.dose) + '</td></tr>';
                        if (d.frequency) html += '<tr><td style="padding:0.25rem 0;color:#64748b;">Frekuensi</td><td style="padding:0.25rem 0;color:#334155;font-weight:600;">' + this._esc(d.frequency) + '</td></tr>';
                        if (d.duration) html += '<tr><td style="padding:0.25rem 0;color:#64748b;">Durasi</td><td style="padding:0.25rem 0;color:#334155;font-weight:600;">' + this._esc(d.duration) + '</td></tr>';
                        html += '</table>';
                        if (d.purpose) html += '<div style="background:#eff6ff;border-radius:0.375rem;padding:0.5rem;margin-bottom:0.5rem;"><p style="font-size:0.65rem;font-weight:700;color:#0ea5e9;text-transform:uppercase;margin:0 0 0.25rem;">Kegunaan</p><p style="font-size:0.8rem;color:#1e3a8a;margin:0;">' + this._esc(d.purpose) + '</p></div>';
                        if (d.how_to_take) html += '<div style="background:#ecfdf5;border-radius:0.375rem;padding:0.5rem;margin-bottom:0.5rem;"><p style="font-size:0.65rem;font-weight:700;color:#059669;text-transform:uppercase;margin:0 0 0.25rem;">Cara Pakai</p><p style="font-size:0.8rem;color:#064e3b;margin:0;">' + this._esc(d.how_to_take) + '</p></div>';
                        if (d.side_effects?.length) html += '<p style="font-size:0.75rem;color:#b45309;margin:0;"><strong>Efek Samping:</strong> ' + d.side_effects.map(s => this._esc(s)).join(', ') + '</p>';
                        if (d.contraindications?.length) html += '<p style="font-size:0.75rem;color:#dc2626;margin:0.25rem 0 0;"><strong>Kontraindikasi:</strong> ' + d.contraindications.map(s => this._esc(s)).join(', ') + '</p>';
                        html += '</div>';
                    });
                    if (r.step2.pharmacist_notes) html += '<div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;"><p style="font-size:0.7rem;font-weight:700;color:#b45309;text-transform:uppercase;margin:0 0 0.25rem;">Catatan Apoteker</p><p style="font-size:0.8rem;color:#78350f;margin:0;">' + this._esc(r.step2.pharmacist_notes) + '</p></div>';
                }
                if (r.step3?.interactions?.length) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #dc2626;padding-left:0.5rem;">Interaksi Obat</h2>';
                    r.step3.interactions.forEach(i => {
                        html += '<div style="margin-bottom:0.5rem;border:1px solid #e2e8f0;border-radius:0.5rem;padding:0.75rem;background:#fff;">';
                        html += '<p style="font-size:0.85rem;font-weight:600;color:#1e293b;margin:0 0 0.25rem;">[' + this._esc(i.severity) + '] ' + this._esc(i.drug_a) + ' + ' + this._esc(i.drug_b) + '</p>';
                        html += '<p style="font-size:0.8rem;color:#475569;margin:0;">' + this._esc(i.effect) + '</p>';
                        if (i.substitute) html += '<p style="font-size:0.8rem;color:#059669;margin:0.25rem 0 0;">Alternatif: ' + this._esc(i.substitute) + '</p>';
                        html += '</div>';
                    });
                }
                if (r.step4) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #0d9488;padding-left:0.5rem;">Rencana Pemulihan</h2>';
                    if (r.step4.recovery_timeline) html += '<p style="font-size:0.85rem;color:#134e4a;margin:0 0 0.5rem;"><strong>Perkiraan Waktu:</strong> ' + this._esc(r.step4.recovery_timeline) + '</p>';
                    if (r.step4.warning_signs?.length) {
                        html += '<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:0.5rem;padding:0.75rem;margin-bottom:0.75rem;">';
                        html += '<p style="font-size:0.7rem;font-weight:700;color:#dc2626;text-transform:uppercase;margin:0 0 0.5rem;">Segera ke Dokter Bila</p>';
                        r.step4.warning_signs.forEach(w => html += '<p style="font-size:0.8rem;color:#991b1b;margin:0 0 0.25rem;">' + this._esc(w) + '</p>');
                        html += '</div>';
                    }
                    if (r.step4.disclaimer) html += '<p style="font-size:0.75rem;color:#94a3b8;font-style:italic;margin:1rem 0 0;border-top:1px solid #e2e8f0;padding-top:0.75rem;">' + this._esc(r.step4.disclaimer) + '</p>';
                }
                html += '<div style="margin-top:2rem;padding-top:1rem;border-top:1px solid #e2e8f0;display:flex;align-items:center;gap:8px;">';
                html += '<img src="' + logoUrl + '" alt="Pharmasis" style="width:22px;height:22px;object-fit:contain;border-radius:4px;" />';
                html += '<div><p style="font-size:0.75rem;color:#0d9488;font-weight:700;margin:0;">Pharmasis Cek Sehat</p>';
                html += '<p style="font-size:0.65rem;color:#94a3b8;margin:0;">Laporan ini hanya untuk edukasi. Konsultasikan dengan tenaga kesehatan profesional.</p></div>';
                html += '</div>';
                return html;
            },

            _esc(str) {
                if (!str) return '';
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            },

            savePdf() {
                const html = this.buildPrintHTML();
                if (!html) return;
                const printWin = window.open('', '_blank');
                printWin.document.write(`
                    <!DOCTYPE html><html><head><title>Laporan Cek Sehat</title><meta charset="utf-8">
                    <style>
                        @page { margin: 1.5cm; size: A4; }
                        body { margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: #334155; background: #fff; line-height: 1.5; }
                        .watermark { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
                        .watermark > div { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%) rotate(-30deg); opacity: 0.06; font-size: 5rem; font-weight: 800; color: #4f46e5; white-space: nowrap; letter-spacing: 0.1em; }
                        .content { position: relative; z-index: 1; }
                        @media print { .no-print { display: none !important; } }
                    </style></head>
                    <body>
                        <div class="watermark"><div>Cek Sehat</div></div>
                        <div class="content" style="padding: 1.5cm;">${html}</div>
                        <div class="no-print" style="text-align:center;padding: 2rem;">
                            <button onclick="window.print()" style="padding: 0.6rem 2rem; border-radius: 999px; border: none; background: linear-gradient(135deg,#4f46e5,#4338ca); color: #fff; font-weight: 700; font-size: 0.9rem; cursor: pointer;">Cetak / Simpan sebagai PDF</button>
                        </div>
                    </body></html>
                `);
                printWin.document.close();
                printWin.focus();
                setTimeout(() => printWin.print(), 400);
            },

            /* ── Clinical PDF (mode dokter, ICD-10 & bahasa medis murni) ── */
            exportClinicalPdf() {
                const r = this.result;
                const cx = this.journey?.zona_bawah?.clinical_pdf_export;
                if (!r) return;
                const dateStr = new Date().toLocaleString('id-ID');
                const logoUrl = window.location.origin + '/images/icon.png';
                let html = '';
                // Professional header with real logo
                html += '<div style="display:flex;align-items:center;gap:14px;border-bottom:2px solid #0d4f52;padding-bottom:0.85rem;margin-bottom:1.5rem;">';
                html += '<img src="' + logoUrl + '" alt="Pharmasis" style="width:44px;height:44px;object-fit:contain;border-radius:10px;" />';
                html += '<div style="flex:1;">';
                html += '<h1 style="font-size:1.2rem;font-weight:800;color:#0d4f52;margin:0;">Pharmasis Cek Sehat</h1>';
                html += '<p style="font-size:0.68rem;color:#64748b;margin:0.2rem 0 0;">Ringkasan Klinis &middot; ' + dateStr + '</p>';
                html += '</div>';
                html += '<div style="text-align:right;">';
                html += '<span style="font-size:0.6rem;font-weight:700;color:#0d9488;background:#f0fdf9;border:1px solid #99f6e4;padding:0.2rem 0.6rem;border-radius:99px;text-transform:uppercase;letter-spacing:0.05em;">Mode Klinis</span>';
                html += '</div>';
                html += '</div>';
                html += '<p style="font-size:0.85rem;margin:0 0 0.75rem;"><strong>Keluhan (anamnesis):</strong> ' + this._esc(this.transcription || this.symptoms || 'N/A') + '</p>';
                if (cx?.suspected_icd10) html += '<p style="font-size:0.9rem;margin:0 0 0.5rem;"><strong>Dugaan ICD-10:</strong> <code style="background:#f1f5f9;padding:0.1rem 0.4rem;border-radius:0.25rem;">' + this._esc(cx.suspected_icd10) + '</code></p>';
                if (cx?.clinical_notes) html += '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:0.5rem;padding:0.85rem;margin-bottom:1rem;"><p style="font-size:0.7rem;font-weight:700;color:#0d4f52;text-transform:uppercase;margin:0 0 0.35rem;">Catatan Klinis</p><p style="font-size:0.85rem;color:#334155;margin:0;">' + this._esc(cx.clinical_notes) + '</p></div>';
                // Diagnosis banding
                if (r.step1?.conditions?.length) {
                    html += '<h2 style="font-size:0.95rem;color:#0f172a;border-left:3px solid #0d4f52;padding-left:0.5rem;margin:1rem 0 0.5rem;">Diagnosis Banding</h2>';
                    r.step1.conditions.forEach(c => {
                        html += '<p style="font-size:0.82rem;margin:0 0 0.35rem;"><strong>[' + this._esc(c.likelihood) + ']</strong> ' + this._esc(c.name) + ': ' + this._esc(c.description) + '</p>';
                    });
                }
                // Regimen
                if (r.step2?.drugs?.length) {
                    html += '<h2 style="font-size:0.95rem;color:#0f172a;border-left:3px solid #0d4f52;padding-left:0.5rem;margin:1rem 0 0.5rem;">Regimen Farmakologi</h2>';
                    r.step2.drugs.forEach(d => {
                        html += '<p style="font-size:0.82rem;margin:0 0 0.35rem;"><strong>' + this._esc(d.name) + '</strong> (' + this._esc(d.generic || '') + ') &mdash; ' + this._esc(d.dose || '') + ', ' + this._esc(d.frequency || '') + '. ' + this._esc(d.drug_class || '') + '. ' + (d.otc ? 'OTC' : 'Rx') + '.';
                        if (d.contraindications?.length) html += ' <em>KI: ' + d.contraindications.map(s => this._esc(s)).join(', ') + '.</em>';
                        html += '</p>';
                    });
                }
                // Interaksi
                if (r.step3?.interactions?.length) {
                    html += '<h2 style="font-size:0.95rem;color:#0f172a;border-left:3px solid #dc2626;padding-left:0.5rem;margin:1rem 0 0.5rem;">Interaksi Obat</h2>';
                    r.step3.interactions.forEach(i => {
                        html += '<p style="font-size:0.82rem;margin:0 0 0.35rem;color:#991b1b;"><strong>[' + this._esc(i.severity) + ']</strong> ' + this._esc(i.drug_a) + ' + ' + this._esc(i.drug_b) + ': ' + this._esc(i.effect) + (i.substitute ? ' (Alternatif: ' + this._esc(i.substitute) + ')' : '') + '</p>';
                    });
                }
                // Footer
                html += '<div style="margin-top:1.5rem;padding-top:0.6rem;border-top:1px solid #e2e8f0;display:flex;align-items:center;gap:8px;">';
                html += '<img src="' + logoUrl + '" alt="Pharmasis" style="width:20px;height:20px;object-fit:contain;border-radius:4px;" />';
                html += '<p style="font-size:0.7rem;color:#94a3b8;font-style:italic;margin:0;">Dokumen edukasi non-diagnostik. Tidak menggantikan pemeriksaan dan penilaian klinis langsung oleh tenaga medis berlisensi.</p>';
                html += '</div>';

                const w = window.open('', '_blank');
                w.document.write('<!DOCTYPE html><html><head><title>Ringkasan Klinis Pharmasis</title><meta charset="utf-8"><style>@page{margin:1.5cm;size:A4;}body{font-family:Georgia,\'Times New Roman\',serif;color:#1e293b;line-height:1.55;padding:1.5cm;} .no-print{text-align:center;padding:1.5rem;} @media print{.no-print{display:none!important;}}</style></head><body>' + html + '<div class="no-print"><button onclick="window.print()" style="padding:0.6rem 2rem;border-radius:999px;border:none;background:#0d4f52;color:#fff;font-weight:700;cursor:pointer;">Cetak / Simpan PDF Klinis</button></div></body></html>');
                w.document.close(); w.focus();
                setTimeout(() => w.print(), 400);
            },

            /* ── Init / cleanup ── */
            init() {
                this.$nextTick(() => { this.autoResizeTextarea(); });
            },
            destroy() {
                this.cancelKiosk();
                this.stopWaveform();
            }
        };
    }
</script>
