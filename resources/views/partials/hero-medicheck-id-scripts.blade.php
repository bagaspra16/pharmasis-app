{{-- MediCheck — scripts (screening flow + kiosk) --}}
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

    /* ══════════════ Concluding steps ══════════════ */
    const CONCLUDE_STEPS_ID = [
        { id: 'read',    icon: '1', label: 'Summarizing your answers', detail: 'Building the screening transcript · recognizing clinical entities' },
        { id: 'reason',  icon: '2', label: 'Forming a likely condition', detail: 'Differential diagnosis · clinical reasoning' },
        { id: 'plan',    icon: '3', label: 'Building recommendations', detail: 'Medicines / care · safety · next steps' },
        { id: 'done',    icon: '4', label: 'Conclusion ready to display', detail: 'Validated JSON · report tailored to your role' },
    ];

    /* ══════════════ Main component ══════════════ */
    function mediCheckHeroId() {
        return {
            /* ── Input tabs / first prompt ── */
            activeTab: 'text',
            symptoms: '',
            firstPrompt: '',
            recording: false,
            error: '',

            /* ── Screening flow ──
               phase: 'intake' → 'questions' → 'concluding' → 'result' */
            phase: 'intake',
            screening: false,      // loading questions
            analyzing: false,      // loading conclusion
            questions: [],
            currentQ: 0,
            answers: {},           // { [questionId]: { chips: [], text: '' } }
            role: null,            // 'doctor' | 'patient'
            result: null,          // full server payload
            conclusion: null,      // result.conclusion (branch-specific shape)

            /* ── Concluding step animation ── */
            currentStep: -1,
            completedSteps: [],

            /* ── Voice ── */
            mediaRecorder: null,
            audioChunks: [],

            /* ── Nearby providers ── */
            nearbyProviders: null,
            nearbyLoading: false,
            nearbyLocation: '',

            /* ── Report actions ── */
            copyFeedback: '',

            /* ── Drawer (drug detail) ── */
            drawerOpen: false,
            drawerTitle: '',
            drawerData: null,

            /* ── Kiosk popup ── */
            showKioskPopup: false,
            kioskDelay: 60,
            kioskDuration: 60,
            kioskCountdown: 60,
            _kioskTick: null,
            _kioskSnooze: null,
            _kioskAppear: null,

            /* ── Placeholder typewriter ── */
            placeholderExamples: [
                'Example: "I\'ve had a dry cough and a 38 degree fever since yesterday..."',
                'Example: "My lower right abdomen hurts badly, I feel nauseous, and I vomited twice..."',
                'Example: "Blocked nose, sneezing every morning, with a history of mild asthma..."',
                'Example: "My head spins when I get out of bed and my ears are ringing..."'
            ],
            currentPlaceholderIdx: 0,
            _placeholderTick: null,

            get concludeSteps() { return CONCLUDE_STEPS_ID; },

            get isDoctor() { return this.role === 'doctor'; },
            get roleQuestion() { return this.questions.find(q => q.is_role); },
            get currentQuestion() { return this.questions[this.currentQ] || null; },
            get isLastQuestion() { return this.currentQ >= this.questions.length - 1; },
            get progressPct() {
                if (!this.questions.length) return 0;
                return Math.round(((this.currentQ) / this.questions.length) * 100);
            },

            init() {
                try {
                    const stored = localStorage.getItem('ceksehatCurrentResult');
                    if (stored) {
                        const parsed = JSON.parse(stored);
                        const age = Date.now() - new Date(parsed.savedAt).getTime();
                        if (age < 30 * 60 * 1000 && parsed.result) {
                            this.firstPrompt = parsed.firstPrompt || '';
                            this.role = parsed.result.role || null;
                            this.result = parsed.result;
                            this.conclusion = parsed.result.conclusion || null;
                            this.nearbyLocation = parsed.location || '';
                            this.nearbyProviders = parsed.nearbyProviders || null;
                            this.phase = 'result';
                            this.scheduleKiosk();
                            this.$nextTick(() => setTimeout(() => this.scrollToResult('auto'), 100));
                        }
                    }
                } catch (e) { /* ignore restore errors */ }
                this.initPlaceholder();
            },

            /* ── Placeholder typewriter ── */
            initPlaceholder() { this.typewriterPlaceholder(); },
            typewriterPlaceholder() {
                if (this._placeholderTick) clearTimeout(this._placeholderTick);
                let isDeleting = false, charIdx = 0, typingSpeed = 70;
                const type = () => {
                    const example = this.placeholderExamples[this.currentPlaceholderIdx];
                    let text = '';
                    if (isDeleting) { text = example.substring(0, charIdx - 1); charIdx--; typingSpeed = 30; }
                    else { text = example.substring(0, charIdx + 1); charIdx++; typingSpeed = 50; }
                    const el = (this.$refs && this.$refs.symptomTextarea)
                        ? this.$refs.symptomTextarea
                        : document.querySelector('textarea[x-model="symptoms"]');
                    if (el) el.setAttribute('placeholder', text);
                    if (!isDeleting && charIdx === example.length) { typingSpeed = 3500; isDeleting = true; }
                    else if (isDeleting && charIdx === 0) {
                        isDeleting = false;
                        this.currentPlaceholderIdx = (this.currentPlaceholderIdx + 1) % this.placeholderExamples.length;
                        typingSpeed = 500;
                    }
                    this._placeholderTick = setTimeout(type, typingSpeed);
                };
                type();
            },

            autoResizeTextarea() {
                const t = this.$refs.symptomTextarea;
                if (!t) return;
                t.style.height = 'auto';
                t.style.height = Math.min(t.scrollHeight, 240) + 'px';
            },
            handleTextareaEnter(event) {
                if (event.shiftKey) return;
                event.preventDefault();
                this.startScreening();
            },

            /* ══════════════ Phase 1 → 2: generate screening questions ══════════════ */
            async startScreening() {
                const prompt = (this.symptoms || '').trim();
                if (!prompt || this.screening) return;
                this.error = '';
                this.firstPrompt = prompt;
                this.screening = true;
                this.cancelKiosk();

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                try {
                    const body = new FormData();
                    body.append('symptoms', prompt);
                    body.append('lang', 'auto');
                    const res = await fetch('/medicheck/screen', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body
                    });
                    const data = await res.json();
                    if (!res.ok || !data.success) {
                        let errMsg = 'Failed to prepare questions. Please try again.';
                        if (typeof data.error === 'string') errMsg = data.error;
                        else if (typeof data.message === 'string') errMsg = data.message;
                        else if (typeof data.error === 'object') errMsg = Object.values(data.error).flat()[0] || errMsg;
                        this.error = errMsg;
                    } else {
                        this.questions = (data.questions || []).map(q => {
                            this.answers[q.id] = { chips: [], text: '' };
                            return q;
                        });
                        this.currentQ = 0;
                        this.phase = 'questions';
                        this.$nextTick(() => setTimeout(() => this.scrollToEl('screening-panel', 'smooth'), 80));
                    }
                } catch (e) {
                    this.error = 'Network error. Please try again.';
                } finally {
                    this.screening = false;
                }
            },

            /* ══════════════ Phase 2: question stepper ══════════════ */
            toggleChip(qId, choice) {
                const q = this.questions.find(x => x.id === qId);
                const a = this.answers[qId] || (this.answers[qId] = { chips: [], text: '' });
                if (q && q.allow_multiple) {
                    const i = a.chips.indexOf(choice);
                    if (i >= 0) a.chips.splice(i, 1); else a.chips.push(choice);
                } else {
                    a.chips = a.chips[0] === choice ? [] : [choice];
                }
            },
            chipSelected(qId, choice) {
                return (this.answers[qId]?.chips || []).includes(choice);
            },
            currentAnswered() {
                const q = this.currentQuestion;
                if (!q) return false;
                const a = this.answers[q.id] || { chips: [], text: '' };
                return a.chips.length > 0 || (a.text || '').trim().length > 0;
            },
            nextQ() {
                if (!this.isLastQuestion) {
                    this.currentQ++;
                    this.$nextTick(() => this.scrollToEl('screening-panel', 'smooth'));
                }
            },
            prevQ() {
                if (this.currentQ > 0) this.currentQ--;
            },

            /* Role selection lives on the final question → triggers conclusion */
            selectRole(choice, index) {
                const roleQ = this.roleQuestion;
                if (roleQ) this.answers[roleQ.id] = { chips: [choice], text: '' };
                this.role = index === 0 ? 'doctor' : 'patient';
                this.runConclude();
            },

            /* ══════════════ Phase 3 → 4: conclude ══════════════ */
            buildQaPayload() {
                return this.questions
                    .filter(q => !q.is_role)
                    .map(q => {
                        const a = this.answers[q.id] || { chips: [], text: '' };
                        const parts = [];
                        if (a.chips.length) parts.push(a.chips.join(', '));
                        if ((a.text || '').trim()) parts.push(a.text.trim());
                        return { question: q.question, answer: parts.join(' — ') || '(not answered)' };
                    });
            },

            async runConclude() {
                if (this.analyzing) return;
                this.error = '';
                this.analyzing = true;
                this.phase = 'concluding';
                this.completedSteps = [];
                this.currentStep = 0;
                this.$nextTick(() => setTimeout(() => this.scrollToEl('screening-panel', 'smooth'), 80));

                // Fetch geolocation for the patient "nearby facilities" section.
                if (window.cookieConsentAccepted && window.cookieConsentAccepted()) {
                    try {
                        const locRes = await fetch('https://ipapi.co/json/');
                        if (locRes.ok) {
                            const l = await locRes.json();
                            if (l && l.city) this.nearbyLocation = `${l.city}, ${l.region}, ${l.country_name}`;
                        }
                    } catch (e) { /* ignore */ }
                }

                const stepTimer = this.animateSteps();
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                try {
                    const body = new FormData();
                    body.append('symptoms', this.firstPrompt);
                    body.append('role', this.role);
                    body.append('lang', 'auto');
                    this.buildQaPayload().forEach((pair, i) => {
                        body.append(`qa[${i}][question]`, pair.question);
                        body.append(`qa[${i}][answer]`, pair.answer);
                    });

                    const res = await fetch('/medicheck/conclude', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body
                    });
                    const data = await res.json();

                    clearInterval(stepTimer);
                    await this.finishSteps();

                    if (!res.ok || !data.success) {
                        let errMsg = 'Failed to build the conclusion. Please try again.';
                        if (typeof data.error === 'string') errMsg = data.error;
                        else if (typeof data.message === 'string') errMsg = data.message;
                        else if (typeof data.error === 'object') errMsg = Object.values(data.error).flat()[0] || errMsg;
                        this.error = errMsg;
                        this.phase = 'questions';
                    } else {
                        this.result = data.data;
                        this.conclusion = data.data?.conclusion || null;
                        await this.sleep(500);
                        this.phase = 'result';
                        this.scheduleKiosk();

                        let historyId = null;
                        if (window.cookieConsentAccepted && window.cookieConsentAccepted()) {
                            this.saveToStorage();
                            historyId = this.saveToHistory(data.data);
                        }

                        this.$nextTick(() => setTimeout(() => this.scrollToResult('smooth'), 800));

                        // Patient branch → find nearby healthcare facilities.
                        if (this.role === 'patient' && this.nearbyLocation) {
                            const cond = this.conclusion?.assumed_condition?.name || '';
                            this.fetchNearbyProviders(historyId, this.nearbyLocation, this.firstPrompt, cond);
                        }
                    }
                } catch (e) {
                    clearInterval(stepTimer);
                    this.error = 'Network error. Please try again.';
                    this.phase = 'questions';
                } finally {
                    this.analyzing = false;
                }
            },

            animateSteps() {
                this.currentStep = 0;
                const total = this.concludeSteps.length - 1;
                const delays = [900, 3500, 8000];
                delays.forEach((delay, i) => {
                    setTimeout(() => {
                        if (i < total && this.analyzing) {
                            this.completedSteps.push(this.concludeSteps[i].id);
                            this.currentStep = i + 1;
                        }
                    }, delay);
                });
                return setInterval(() => { }, 99999);
            },
            async finishSteps() {
                for (let i = 0; i < this.concludeSteps.length; i++) {
                    if (!this.completedSteps.includes(this.concludeSteps[i].id)) {
                        this.completedSteps.push(this.concludeSteps[i].id);
                        this.currentStep = i;
                        await this.sleep(120);
                    }
                }
                this.currentStep = this.concludeSteps.length;
                await this.sleep(300);
            },
            sleep(ms) { return new Promise(r => setTimeout(r, ms)); },

            /* ══════════════ Voice recording (feeds first prompt) ══════════════ */
            async toggleRecording() {
                if (this.screening) return;
                if (this.recording) this.stopRecording(); else await this.startRecording();
            },
            async startRecording() {
                this.error = '';
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    this.audioChunks = [];
                    const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus') ? 'audio/webm;codecs=opus'
                        : MediaRecorder.isTypeSupported('audio/webm') ? 'audio/webm' : 'audio/ogg';
                    this.mediaRecorder = new MediaRecorder(stream, { mimeType });
                    this.mediaRecorder.ondataavailable = (e) => { if (e.data.size > 0) this.audioChunks.push(e.data); };
                    this.mediaRecorder.onstop = () => {
                        stream.getTracks().forEach(t => t.stop());
                        this.stopWaveform();
                        this.transcribeAudio();
                    };
                    this.mediaRecorder.start(200);
                    this.recording = true;
                    this.startWaveform(stream);
                } catch (e) {
                    this.error = 'Microphone access denied. Please allow the microphone or type your symptoms below.';
                }
            },
            stopRecording() {
                if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') this.mediaRecorder.stop();
                this.recording = false;
            },
            getAudioExt() {
                const mime = this.mediaRecorder?.mimeType || '';
                if (mime.includes('ogg')) return 'ogg';
                if (mime.includes('mp4')) return 'mp4';
                return 'webm';
            },
            /* Voice → text via Whisper (POST /medicheck/transcribe), then auto-start screening. */
            async transcribeAudio() {
                if (this.audioChunks.length === 0) return;
                this.screening = true;
                this.error = '';
                const blob = new Blob(this.audioChunks, { type: this.mediaRecorder?.mimeType || 'audio/webm' });
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                try {
                    const body = new FormData();
                    body.append('audio', blob, `recording.${this.getAudioExt()}`);
                    body.append('lang', 'auto');
                    const res = await fetch('/medicheck/transcribe', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body
                    });
                    const data = await res.json();
                    if (res.ok && data.text) {
                        this.symptoms = data.text;
                        this.$nextTick(() => this.autoResizeTextarea());
                        this.screening = false;
                        this.activeTab = 'text';
                        await this.startScreening();
                        return;
                    } else {
                        this.error = (typeof data.error === 'string' ? data.error : 'Failed to transcribe audio. Please type your symptoms instead.');
                    }
                } catch (e) {
                    this.error = 'Failed to transcribe audio. Please type your symptoms instead.';
                } finally {
                    this.screening = false;
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
                            const hue = 180 - (i / bars) * 20;
                            cctx.fillStyle = `hsla(${hue}, 55%, 48%, 0.9)`;
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

            /* ══════════════ Drawer (drug detail — doctor branch) ══════════════ */
            openDrawer(title, data) { this.drawerTitle = title; this.drawerData = data; this.drawerOpen = true; },
            closeDrawer() { this.drawerOpen = false; },

            /* The AI mirrors the user's input language, so normalize its badge
               labels back to English to keep the chrome consistent. */
            severityLabel(v) {
                const m = { 'hindari': 'Avoid', 'hati-hati': 'Caution', 'pantau': 'Monitor' };
                const k = String(v || '').toLowerCase();
                return m[k] || v;
            },
            likelihoodLabel(v) {
                const m = { 'tinggi': 'High', 'sedang': 'Moderate', 'rendah': 'Low' };
                const k = String(v || '').toLowerCase();
                return m[k] || v;
            },

            /* ══════════════ Nearby providers ══════════════ */
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
                    const q = encodeURIComponent('hospitals clinics near ' + location);
                    this.nearbyProviders = { success: false, providers: [], maps_search_url: `https://www.google.com/maps/search/${q}` };
                } finally {
                    this.nearbyLoading = false;
                }
            },

            /* ══════════════ Scroll helpers ══════════════ */
            scrollToEl(id, behavior) {
                const el = document.getElementById(id);
                if (el) {
                    const y = el.getBoundingClientRect().top + window.scrollY - 20;
                    window.scrollTo({ top: y, behavior: behavior || 'smooth' });
                }
            },
            scrollToResult(behavior) {
                const el = document.getElementById('medicheck-output-page');
                if (!el) return;
                el.scrollIntoView({ behavior: behavior || 'smooth', block: 'start' });
                // Focus the element so screen readers and keyboard nav follow along
                if (!el.hasAttribute('tabindex')) el.setAttribute('tabindex', '-1');
                el.focus({ preventScroll: true });
            },

            /* ══════════════ Kiosk popup ══════════════ */
            scheduleKiosk() {
                this.cancelKiosk();
                this._kioskAppear = setTimeout(() => {
                    if (this.phase === 'result') this.startKioskCountdown();
                }, this.kioskDelay * 1000);
            },
            startKioskCountdown() {
                this.clearKioskTimers();
                this.kioskCountdown = this.kioskDuration;
                this.showKioskPopup = true;
                this._kioskTick = setInterval(() => {
                    this.kioskCountdown--;
                    if (this.kioskCountdown <= 0) this.resetKiosk();
                }, 1000);
            },
            snoozeKiosk() {
                this.cancelKiosk();
                this._kioskSnooze = setTimeout(() => {
                    if (this.phase === 'result') this.startKioskCountdown();
                }, this.kioskDelay * 1000);
            },
            resetKiosk() {
                this.cancelKiosk();
                localStorage.removeItem('ceksehatCurrentResult');
                window.location.reload();
            },
            clearKioskTimers() {
                if (this._kioskTick) { clearInterval(this._kioskTick); this._kioskTick = null; }
                if (this._kioskSnooze) { clearTimeout(this._kioskSnooze); this._kioskSnooze = null; }
                if (this._kioskAppear) { clearTimeout(this._kioskAppear); this._kioskAppear = null; }
            },
            cancelKiosk() { this.clearKioskTimers(); this.showKioskPopup = false; },

            /* ══════════════ localStorage ══════════════ */
            saveToStorage() {
                if (!this.result) return;
                try {
                    localStorage.setItem('ceksehatCurrentResult', JSON.stringify({
                        id: 'cs_' + Date.now(),
                        savedAt: new Date().toISOString(),
                        firstPrompt: this.firstPrompt || '',
                        location: this.nearbyLocation || null,
                        nearbyProviders: this.nearbyProviders || null,
                        result: this.result
                    }));
                } catch (e) { /* ignore */ }
            },
            saveToHistory(resultData) {
                try {
                    let history = JSON.parse(localStorage.getItem('ceksehatHistory') || '[]');
                    const c = resultData.conclusion || {};
                    const conds = resultData.role === 'doctor'
                        ? (c.differential || []).map(d => d.name).slice(0, 2)
                        : [c.assumed_condition?.name].filter(Boolean);
                    const rec = {
                        id: 'cs_' + Date.now(),
                        date: new Date().toISOString(),
                        preview: (resultData.symptoms || '').substring(0, 80) + ((resultData.symptoms || '').length > 80 ? '...' : ''),
                        conditions: conds,
                        role: resultData.role,
                        nearbyLocation: this.nearbyLocation || null,
                        nearbyProviders: null,
                        result: resultData
                    };
                    history.unshift(rec);
                    if (history.length > 10) history = history.slice(0, 10);
                    localStorage.setItem('ceksehatHistory', JSON.stringify(history));
                    return rec.id;
                } catch (e) { return null; }
            },
            patchHistoryNearby(recordId, nearbyData) {
                if (!recordId) return;
                try {
                    let history = JSON.parse(localStorage.getItem('ceksehatHistory') || '[]');
                    const idx = history.findIndex(h => h.id === recordId);
                    if (idx !== -1) { history[idx].nearbyProviders = nearbyData; localStorage.setItem('ceksehatHistory', JSON.stringify(history)); }
                } catch (e) { /* ignore */ }
            },

            /* ══════════════ Plain-text report ══════════════ */
            buildReport() {
                const r = this.result, c = this.conclusion;
                if (!r || !c) return '';
                const watermark = '\nGenerated by Pharmasis MediCheck\nhttps://pharmasis.id\n';
                let t = 'MEDICHECK REPORT\n';
                t += `Pharmasis MediCheck AI\n`;
                t += `Date: ${new Date().toLocaleString('en-US')}\n`;
                t += `Role: ${r.role === 'doctor' ? 'Healthcare professional / Doctor' : 'Patient / General public'}\n`;
                t += `Initial symptoms: ${r.symptoms || 'N/A'}\n\n`;

                if ((r.qa || []).length) {
                    t += 'SCREENING RESULTS\n';
                    r.qa.forEach(p => { t += `- ${p.question}\n  ${p.answer}\n`; });
                    t += '\n';
                }

                if (r.role === 'doctor') {
                    if (c.differential?.length) {
                        t += 'DIFFERENTIAL DIAGNOSIS\n';
                        c.differential.forEach(d => { t += `[${this.likelihoodLabel(d.likelihood)}] ${d.name}\n  ${d.rationale}\n`; });
                        t += '\n';
                    }
                    if (c.drugs?.length) {
                        t += 'PHARMACOLOGIC REGIMEN\n';
                        c.drugs.forEach(d => {
                            t += `${d.name} (${d.generic || ''}) — ${d.dose || ''}, ${d.frequency || ''}, ${d.duration || ''}\n`;
                            if (d.drug_class) t += `  Class: ${d.drug_class}\n`;
                            if (d.mechanism) t += `  MoA: ${d.mechanism}\n`;
                            if (d.contraindications?.length) t += `  Contraindications: ${d.contraindications.join(', ')}\n`;
                            if (d.notes) t += `  Notes: ${d.notes}\n`;
                        });
                        t += '\n';
                    }
                    if (c.interactions?.length) {
                        t += 'DRUG INTERACTIONS\n';
                        c.interactions.forEach(i => { t += `[${this.severityLabel(i.severity)}] ${i.drug_a} + ${i.drug_b}: ${i.effect}\n  Management: ${i.management || '-'}\n`; });
                        t += '\n';
                    }
                    if (c.management?.length) { t += 'MANAGEMENT\n'; c.management.forEach(m => t += `- ${m}\n`); t += '\n'; }
                    if (c.red_flags?.length) { t += 'RED FLAGS\n'; c.red_flags.forEach(m => t += `- ${m}\n`); t += '\n'; }
                    if (c.icd10) t += `Suspected ICD-10: ${c.icd10}\n`;
                    if (c.clinical_summary) t += `Clinical Summary: ${c.clinical_summary}\n`;
                } else {
                    if (c.reassurance) t += `${c.reassurance}\n\n`;
                    if (c.assumed_condition?.name) t += `KEMUNGKINAN KONDISI: ${c.assumed_condition.name}\n${c.assumed_condition.plain_explanation || ''}\n\n`;
                    if (c.self_care?.length) { t += 'PENANGANAN AWAL\n'; c.self_care.forEach(s => t += `- ${s}\n`); t += '\n'; }
                    if (c.lifestyle?.length) { t += 'PENGATURAN POLA HIDUP\n'; c.lifestyle.forEach(s => t += `- ${s}\n`); t += '\n'; }
                    if (c.when_to_seek_care) t += `KAPAN HARUS KE FASKES: ${c.when_to_seek_care}\n`;
                    if (c.which_doctor) t += `TEMUI DOKTER: ${c.which_doctor}\n`;
                    if (c.what_to_ask_doctor?.length) { t += 'YANG PERLU DITANYAKAN:\n'; c.what_to_ask_doctor.forEach(s => t += `- ${s}\n`); }
                    if (c.disclaimer) t += `\n${c.disclaimer}\n`;
                }
                t += watermark;
                return t;
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
                const shareData = { title: 'Laporan Cek Sehat', text };
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

            _esc(str) {
                if (!str) return '';
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            },

            /* ══════════════ PDF (adapts to role) ══════════════ */
            savePdf() {
                const r = this.result, c = this.conclusion;
                if (!r || !c) return;
                const dateStr = new Date().toLocaleString('id-ID');
                const logoUrl = window.location.origin + '/images/icon.png';
                const isDoctor = r.role === 'doctor';
                const accent = isDoctor ? '#0d4f52' : '#0d9488';
                let html = '';

                html += `<div style="display:flex;align-items:center;gap:14px;border-bottom:2px solid ${accent};padding-bottom:0.85rem;margin-bottom:1.5rem;">`;
                html += '<img src="' + logoUrl + '" alt="Pharmasis" style="width:44px;height:44px;object-fit:contain;border-radius:10px;" />';
                html += '<div style="flex:1;"><h1 style="font-size:1.2rem;font-weight:800;color:' + accent + ';margin:0;">Pharmasis Cek Sehat</h1>';
                html += '<p style="font-size:0.68rem;color:#64748b;margin:0.2rem 0 0;">' + (isDoctor ? 'Ringkasan Klinis' : 'Laporan Kesehatan') + ' &middot; ' + dateStr + '</p></div>';
                html += '<span style="font-size:0.6rem;font-weight:700;color:' + accent + ';background:#f0fdf9;border:1px solid #99f6e4;padding:0.2rem 0.6rem;border-radius:99px;text-transform:uppercase;">' + (isDoctor ? 'Mode Klinis' : 'Mode Pasien') + '</span>';
                html += '</div>';

                html += '<p style="font-size:0.85rem;margin:0 0 0.75rem;"><strong>Keluhan awal:</strong> ' + this._esc(r.symptoms || 'N/A') + '</p>';
                if ((r.qa || []).length) {
                    html += '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;">';
                    html += '<p style="font-size:0.7rem;font-weight:700;color:' + accent + ';text-transform:uppercase;margin:0 0 0.4rem;">Hasil Screening</p>';
                    r.qa.forEach(p => { html += '<p style="font-size:0.78rem;margin:0 0 0.35rem;"><strong>' + this._esc(p.question) + '</strong><br>' + this._esc(p.answer) + '</p>'; });
                    html += '</div>';
                }

                if (isDoctor) {
                    if (c.differential?.length) {
                        html += '<h2 style="font-size:0.95rem;color:#0f172a;border-left:3px solid ' + accent + ';padding-left:0.5rem;margin:1rem 0 0.5rem;">Diagnosis Banding</h2>';
                        c.differential.forEach(d => html += '<p style="font-size:0.82rem;margin:0 0 0.35rem;"><strong>[' + this._esc(d.likelihood) + ']</strong> ' + this._esc(d.name) + ': ' + this._esc(d.rationale) + '</p>');
                    }
                    if (c.drugs?.length) {
                        html += '<h2 style="font-size:0.95rem;color:#0f172a;border-left:3px solid ' + accent + ';padding-left:0.5rem;margin:1rem 0 0.5rem;">Regimen Farmakologi</h2>';
                        c.drugs.forEach(d => {
                            html += '<p style="font-size:0.82rem;margin:0 0 0.35rem;"><strong>' + this._esc(d.name) + '</strong> (' + this._esc(d.generic || '') + ') &mdash; ' + this._esc(d.dose || '') + ', ' + this._esc(d.frequency || '') + ', ' + this._esc(d.duration || '') + '. ' + this._esc(d.drug_class || '') + '.';
                            if (d.contraindications?.length) html += ' <em>KI: ' + d.contraindications.map(s => this._esc(s)).join(', ') + '.</em>';
                            html += '</p>';
                        });
                    }
                    if (c.interactions?.length) {
                        html += '<h2 style="font-size:0.95rem;color:#0f172a;border-left:3px solid #dc2626;padding-left:0.5rem;margin:1rem 0 0.5rem;">Interaksi Obat</h2>';
                        c.interactions.forEach(i => html += '<p style="font-size:0.82rem;margin:0 0 0.35rem;color:#991b1b;"><strong>[' + this._esc(i.severity) + ']</strong> ' + this._esc(i.drug_a) + ' + ' + this._esc(i.drug_b) + ': ' + this._esc(i.effect) + (i.management ? ' — ' + this._esc(i.management) : '') + '</p>');
                    }
                    if (c.management?.length) { html += '<h2 style="font-size:0.95rem;color:#0f172a;border-left:3px solid ' + accent + ';padding-left:0.5rem;margin:1rem 0 0.5rem;">Penatalaksanaan</h2>'; c.management.forEach(m => html += '<p style="font-size:0.82rem;margin:0 0 0.25rem;">• ' + this._esc(m) + '</p>'); }
                    if (c.red_flags?.length) { html += '<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:0.5rem;padding:0.75rem;margin:0.75rem 0;"><p style="font-size:0.7rem;font-weight:700;color:#dc2626;text-transform:uppercase;margin:0 0 0.4rem;">Red Flags</p>'; c.red_flags.forEach(m => html += '<p style="font-size:0.8rem;color:#991b1b;margin:0 0 0.25rem;">' + this._esc(m) + '</p>'); html += '</div>'; }
                    if (c.icd10) html += '<p style="font-size:0.85rem;margin:0.5rem 0;"><strong>Dugaan ICD-10:</strong> <code style="background:#f1f5f9;padding:0.1rem 0.4rem;border-radius:0.25rem;">' + this._esc(c.icd10) + '</code></p>';
                    if (c.clinical_summary) html += '<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:0.5rem;padding:0.85rem;margin-top:0.75rem;"><p style="font-size:0.7rem;font-weight:700;color:' + accent + ';text-transform:uppercase;margin:0 0 0.35rem;">Ringkasan Klinis</p><p style="font-size:0.85rem;color:#334155;margin:0;">' + this._esc(c.clinical_summary) + '</p></div>';
                } else {
                    if (c.reassurance) html += '<div style="background:#f0fdfa;border:1px solid #99f6e4;border-radius:0.5rem;padding:0.85rem;margin-bottom:1rem;"><p style="font-size:0.9rem;color:#134e4a;margin:0;">' + this._esc(c.reassurance) + '</p></div>';
                    if (c.assumed_condition?.name) {
                        html += '<h2 style="font-size:1rem;color:#0f172a;border-left:3px solid ' + accent + ';padding-left:0.5rem;margin:1rem 0 0.5rem;">Kemungkinan Kondisi: ' + this._esc(c.assumed_condition.name) + '</h2>';
                        html += '<p style="font-size:0.85rem;color:#475569;margin:0 0 0.75rem;">' + this._esc(c.assumed_condition.plain_explanation || '') + '</p>';
                    }
                    const list = (title, items, color) => {
                        if (!items?.length) return;
                        html += '<h3 style="font-size:0.85rem;color:' + (color || '#0f172a') + ';margin:0.75rem 0 0.35rem;">' + title + '</h3>';
                        items.forEach(s => html += '<p style="font-size:0.82rem;color:#475569;margin:0 0 0.25rem;">• ' + this._esc(s) + '</p>');
                    };
                    list('Penanganan Awal', c.self_care);
                    list('Pengaturan Pola Hidup', c.lifestyle);
                    if (c.when_to_seek_care) html += '<div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:0.5rem;padding:0.75rem;margin:0.75rem 0;"><p style="font-size:0.7rem;font-weight:700;color:#b45309;text-transform:uppercase;margin:0 0 0.3rem;">Kapan Harus ke Faskes</p><p style="font-size:0.82rem;color:#78350f;margin:0;">' + this._esc(c.when_to_seek_care) + '</p></div>';
                    if (c.which_doctor) html += '<p style="font-size:0.85rem;margin:0.5rem 0;"><strong>Temui dokter:</strong> ' + this._esc(c.which_doctor) + '</p>';
                    list('Yang Perlu Ditanyakan ke Dokter', c.what_to_ask_doctor);
                    if (c.disclaimer) html += '<p style="font-size:0.75rem;color:#94a3b8;font-style:italic;margin:1rem 0 0;border-top:1px solid #e2e8f0;padding-top:0.75rem;">' + this._esc(c.disclaimer) + '</p>';
                }

                html += '<div style="margin-top:1.5rem;padding-top:0.6rem;border-top:1px solid #e2e8f0;display:flex;align-items:center;gap:8px;">';
                html += '<img src="' + logoUrl + '" alt="Pharmasis" style="width:20px;height:20px;object-fit:contain;border-radius:4px;" />';
                html += '<p style="font-size:0.7rem;color:#94a3b8;font-style:italic;margin:0;">Dokumen edukasi non-diagnostik. Tidak menggantikan pemeriksaan dan penilaian klinis langsung oleh tenaga medis berlisensi.</p></div>';

                const font = isDoctor ? "Georgia,'Times New Roman',serif" : "'Segoe UI',system-ui,sans-serif";
                const w = window.open('', '_blank');
                w.document.write('<!DOCTYPE html><html><head><title>Laporan Cek Sehat</title><meta charset="utf-8"><style>@page{margin:1.5cm;size:A4;}body{font-family:' + font + ';color:#1e293b;line-height:1.55;padding:1.5cm;} .no-print{text-align:center;padding:1.5rem;} @media print{.no-print{display:none!important;}}</style></head><body>' + html + '<div class="no-print"><button onclick="window.print()" style="padding:0.6rem 2rem;border-radius:999px;border:none;background:' + accent + ';color:#fff;font-weight:700;cursor:pointer;">Cetak / Simpan PDF</button></div></body></html>');
                w.document.close(); w.focus();
                setTimeout(() => w.print(), 400);
            },

            /* ── cleanup ── */
            destroy() {
                this.cancelKiosk();
                this.stopWaveform();
                if (this._placeholderTick) clearTimeout(this._placeholderTick);
            }
        };
    }
</script>
