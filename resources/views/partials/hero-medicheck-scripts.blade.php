{{-- MediCheck AI Hero scripts --}}
<script>
    /* ══════════════════════════════════════════════════════════
       Infinite Grid Component
       ══════════════════════════════════════════════════════════ */
    document.addEventListener('alpine:init', () => {
        Alpine.data('infiniteGrid', () => ({
            mouseX: -1000,
            mouseY: -1000,
            gridOffsetX: 0,
            gridOffsetY: 0,
            speedX: 0.5,
            speedY: 0.5,
            animationFrame: null,

            init() {
                this.animateGrid();
            },
            destroy() {
                if (this.animationFrame) cancelAnimationFrame(this.animationFrame);
            },
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

    /* ══════════════════════════════════════════════════════════
       Language Detection — simple heuristic word matching
       ══════════════════════════════════════════════════════════ */
    function detectLang(text) {
        const idWords = ['saya', 'aku', 'sakit', 'kepala', 'demam', 'batuk', 'pilek', 'nyeri', 'perut',
            'mual', 'pusing', 'badan', 'tenggorokan', 'hidung', 'mata', 'telinga', 'tangan', 'kaki',
            'sudah', 'masih', 'sejak', 'hari', 'minggu', 'bulan', 'sangat', 'sekali', 'tidak', 'bisa',
            'dan', 'atau', 'dengan', 'untuk', 'ini', 'itu', 'ada', 'dari', 'ke', 'di', 'yang', 'juga',
            'ingin', 'mau', 'terasa', 'rasanya', 'lagi', 'seperti'];
        const words = text.toLowerCase().split(/\s+/);
        const idCount = words.filter(w => idWords.includes(w)).length;
        return idCount >= 2 ? 'id' : 'en';
    }

    /* ══════════════════════════════════════════════════════════
       Agentic processing steps (EN + ID)
       ══════════════════════════════════════════════════════════ */
    const PIPELINE_STEPS = [
        { id: 'parse', icon: '1', label: 'Parsing symptom context & severity markers', detail: 'Tokenizing input · extracting clinical entities · scoring urgency signals' },
        { id: 'analyze', icon: '2', label: 'Clinical pattern recognition', detail: 'Differential diagnosis · ICD-10 mapping · confidence scoring per condition' },
        { id: 'drugs', icon: '3', label: 'Drug matching & dosage calculation', detail: 'Formulary cross-reference · OTC vs Rx · weight-adjusted dosage' },
        { id: 'interact', icon: '4', label: 'Drug interaction safety check', detail: 'Cytochrome P450 conflict scan · contraindication matrix · substitute ranking' },
        { id: 'safety', icon: '5', label: 'Safety guard validation', detail: 'Hallucination filtering · clinical plausibility check · disclaimer injection' },
        { id: 'summary', icon: '6', label: 'Generating recovery plan & lifestyle report', detail: 'Timeline estimation · lifestyle optimization · emergency flag generation' },
        { id: 'local', icon: '7', label: 'Locating nearby healthcare providers', detail: 'IP geo-location · facility mapping · contact extraction' },
        { id: 'done', icon: '8', label: 'Analysis complete — rendering results', detail: 'Structured JSON validated · auto-swap applied · report ready' },
    ];

    /* ══════════════════════════════════════════════════════════
       Main Alpine component
       ══════════════════════════════════════════════════════════ */
    function mediCheckHero() {
        return {
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
            speechRecognition: null,
            nearbyProviders: null,
            nearbyLoading: false,
            nearbyLocation: '',
            copyFeedback: '',

            get steps() {
                return PIPELINE_STEPS;
            },

            /* ── Voice recording ─────────────────────────────────────── */
            async toggleRecording() {
                if (this.analyzing) return;
                if (this.recording) {
                    this.stopRecording();
                } else {
                    await this.startRecording();
                }
            },

            async startRecording() {
                this.error = '';
                this.showResults = false;
                this.showProcessing = false;
                this.result = null;
                this.transcription = '';
                this.liveTranscript = '';
                this.completedSteps = [];
                this.currentStep = -1;
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    this.audioChunks = [];
                    this._stream = stream;

                    // Choose best supported format
                    const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
                        ? 'audio/webm;codecs=opus'
                        : MediaRecorder.isTypeSupported('audio/webm')
                            ? 'audio/webm'
                            : 'audio/ogg';

                    this.mediaRecorder = new MediaRecorder(stream, { mimeType });
                    this.mediaRecorder.ondataavailable = (e) => {
                        if (e.data.size > 0) this.audioChunks.push(e.data);
                    };
                    this.mediaRecorder.onstop = () => {
                        stream.getTracks().forEach(t => t.stop());
                        this.stopWaveform();
                        this.processAudio();
                    };
                    this.mediaRecorder.start(200);
                    this.recording = true;

                    // Start waveform visualizer (language-agnostic audio feedback)
                    this.startWaveform(stream);

                } catch (e) {
                    this.error = 'Microphone access denied. Please allow microphone or type your symptoms below.';
                }
            },

            /* ── Waveform visualizer (Web Audio API) ───────────────────── */
            startWaveform(stream) {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const src = ctx.createMediaStreamSource(stream);
                    const analyser = ctx.createAnalyser();
                    analyser.fftSize = 64;
                    src.connect(analyser);
                    this._audioCtx = ctx;
                    this._analyser = analyser;
                    this._waveData = new Uint8Array(analyser.frequencyBinCount);

                    const canvas = this.$refs.waveCanvas;
                    if (!canvas) return;
                    const cctx = canvas.getContext('2d');
                    const W = canvas.width;
                    const H = canvas.height;
                    const bars = analyser.frequencyBinCount;
                    const barW = Math.floor(W / bars) - 1;

                    const draw = () => {
                        if (!this.recording) return;
                        this._rafId = requestAnimationFrame(draw);
                        analyser.getByteFrequencyData(this._waveData);
                        cctx.clearRect(0, 0, W, H);
                        this._waveData.forEach((val, i) => {
                            const barH = Math.max(3, (val / 255) * H);
                            const hue = 174 + (i / bars) * 30; // teal gradient
                            cctx.fillStyle = `hsla(${hue}, 65%, 45%, 0.85)`;
                            cctx.beginPath();
                            cctx.roundRect(
                                i * (barW + 1),
                                (H - barH) / 2,
                                barW,
                                barH,
                                2
                            );
                            cctx.fill();
                        });
                    };
                    draw();
                } catch (e) { /* graceful fallback */ }
            },

            stopWaveform() {
                if (this._rafId) cancelAnimationFrame(this._rafId);
                if (this._audioCtx) { try { this._audioCtx.close(); } catch (e) { } }
                this._rafId = null; this._audioCtx = null; this._analyser = null;
                // Clear canvas
                const canvas = this.$refs.waveCanvas;
                if (canvas) canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            },

            stopRecording() {
                if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                    this.mediaRecorder.stop();
                }
                this.recording = false;
            },

            async processAudio() {
                if (this.audioChunks.length === 0) return;

                this.analyzing = true;
                this.showProcessing = true;
                this.completedSteps = [];
                this.currentStep = 0;

                // Send raw audio to Whisper — it is fully multilingual,
                // no language hint needed. Whisper returns text in whatever
                // language was spoken. The AI pipeline then reads that and
                // responds in the same language automatically.
                const blob = new Blob(this.audioChunks, { type: this.mediaRecorder?.mimeType || 'audio/webm' });
                const formData = new FormData();
                formData.append('audio', blob, `recording.${this.getAudioExt()}`);
                formData.append('lang', 'auto');

                await this.runAnalysis(formData);
            },

            getAudioExt() {
                const mime = this.mediaRecorder?.mimeType || '';
                if (mime.includes('ogg')) return 'ogg';
                if (mime.includes('mp4')) return 'mp4';
                return 'webm';
            },

            /* ── Text analysis ───────────────────────────────────────── */
            async analyzeText() {
                if (!this.symptoms.trim() || this.analyzing) return;

                // No pre-detection — send 'auto', let the AI decide from context
                this.error = '';
                this.showResults = false;
                this.showProcessing = true;
                this.result = null;
                this.transcription = '';
                this.completedSteps = [];
                this.currentStep = 0;
                this.analyzing = true;

                const formData = new FormData();
                formData.append('symptoms', this.symptoms.trim());
                formData.append('lang', 'auto');

                await this.runAnalysis(formData);
            },

            /* ── Auto-resize textarea ───────────────────────────────── */
            autoResizeTextarea() {
                const textarea = this.$refs.symptomTextarea;
                if (!textarea) return;

                textarea.style.height = 'auto';
                const newHeight = Math.min(textarea.scrollHeight, 240);
                textarea.style.height = newHeight + 'px';
            },

            /* ── Handle textarea Enter key ──────────────────────────── */
            handleTextareaEnter(event) {
                if (event.shiftKey) {
                    // Allow new line with Shift+Enter
                    return;
                }

                // Prevent default and submit with Enter
                event.preventDefault();
                this.analyzeText();
            },

            /* ── Core analysis runner ────────────────────────────────── */
            async runAnalysis(formData) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                // Fetch IP Location for hospital suggestions (requires cookie consent)
                if (window.cookieConsentAccepted && window.cookieConsentAccepted()) {
                    try {
                        const locRes = await fetch('https://ipapi.co/json/');
                        if (locRes.ok) {
                            const locData = await locRes.json();
                            if (locData && locData.city) {
                                this.nearbyLocation = `${locData.city}, ${locData.region}, ${locData.country_name}`;
                            }
                        }
                    } catch (e) {
                        console.log("Could not fetch IP location:", e);
                    }
                }

                // Animate steps while waiting for the real response
                this.processingSteps = this.steps;
                const stepTimer = this.animateSteps();

                try {
                    const res = await fetch('/medicheck/analyze', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: formData
                    });
                    const data = await res.json();

                    // Finish all steps
                    clearInterval(stepTimer);
                    await this.finishSteps();

                    if (!res.ok) {
                        this.error = data.error || 'Analysis failed. Please try again.';
                        this.showProcessing = false;
                    } else {
                        this.result = data.data;
                        this.transcription = data.data?.symptoms || this.liveTranscript || '';
                        // Override lang from actual result
                        if (data.data?.lang) this.lang = data.data.lang;

                        await this.sleep(600);
                        this.showProcessing = false;
                        this.showResults = true;
                        this.applySubstitutions();

                        // Save full result to dedicated localStorage (requires cookie consent)
                        let historyId = null;
                        if (window.cookieConsentAccepted && window.cookieConsentAccepted()) {
                            this.saveToStorage();
                            historyId = this.saveToHistory(data.data);
                        }

                        this.$nextTick(() => {
                            document.getElementById('medicheck-results')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        });

                        // Async: load nearby providers WITHOUT blocking the main result
                        if (this.nearbyLocation && historyId) {
                            this.fetchNearbyProviders(
                                historyId,
                                this.nearbyLocation,
                                data.data?.symptoms || '',
                                (data.data?.step1?.conditions || []).map(c => c.name).join(', ')
                            );
                        }
                    }
                } catch (e) {
                    clearInterval(stepTimer);
                    this.error = 'Network error. Please try again.';
                    this.showProcessing = false;
                } finally {
                    this.analyzing = false;
                }
            },

            /* ── Local Storage History ─────────────────────────── */
            saveToHistory(resultData) {
                try {
                    let history = JSON.parse(localStorage.getItem('medicheckHistory') || '[]');
                    const newRecord = {
                        id: 'mc_' + Date.now(),
                        date: new Date().toISOString(),
                        preview: resultData.symptoms?.substring(0, 80) + (resultData.symptoms?.length > 80 ? '...' : ''),
                        conditions: (resultData.step1?.conditions || []).map(c => c.name).slice(0, 2),
                        nearbyLocation: this.nearbyLocation || null,
                        nearbyProviders: null,
                        result: resultData
                    };
                    history.unshift(newRecord);
                    if (history.length > 10) history = history.slice(0, 10);
                    localStorage.setItem('medicheckHistory', JSON.stringify(history));
                    window.dispatchEvent(new CustomEvent('medicheck-history-updated'));
                    return newRecord.id;
                } catch (e) {
                    console.error('Could not save history to localStorage', e);
                    return null;
                }
            },

            patchHistoryNearby(recordId, nearbyData) {
                if (!recordId) return;
                try {
                    let history = JSON.parse(localStorage.getItem('medicheckHistory') || '[]');
                    const idx = history.findIndex(h => h.id === recordId);
                    if (idx !== -1) {
                        history[idx].nearbyProviders = nearbyData;
                        localStorage.setItem('medicheckHistory', JSON.stringify(history));
                    }
                } catch (e) {
                    console.warn('Could not patch history with nearby data', e);
                }
            },

            /* ── Step animation ──────────────────────────────────────── */
            animateSteps() {
                const totalSteps = this.steps.length - 1; // last step is 'done', triggered after
                let idx = 0;
                this.currentStep = 0;

                // Step timing: spread across ~80% of expected wait time (30s total)
                const delays = [1200, 3500, 7000, 14000, 20000, 25000];


                delays.forEach((delay, i) => {
                    setTimeout(() => {
                        if (i < totalSteps && this.analyzing) {
                            this.completedSteps.push(this.steps[i].id);
                            this.currentStep = i + 1;
                        }
                    }, delay);
                });

                // Return a dummy interval (real control is via setTimeout)
                return setInterval(() => { }, 99999);
            },

            async finishSteps() {
                // Mark all steps complete quickly
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

            sleep(ms) {
                return new Promise(r => setTimeout(r, ms));
            },

            /* ── Async nearby providers loader ───────────────────────── */
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
                    console.warn('Nearby providers fetch failed:', e);
                    const q = encodeURIComponent('hospital clinic near ' + location);
                    this.nearbyProviders = {
                        success: false,
                        providers: [],
                        maps_search_url: `https://www.google.com/maps/search/${q}`
                    };
                } finally {
                    this.nearbyLoading = false;
                }
            },
            /* ── Drug auto-swap (Feature 5) ─────────────────────────── */
            applySubstitutions() {
                this.$nextTick(() => {
                    const interactions = this.result?.step3?.interactions || [];
                    interactions.forEach(inter => {
                        if (inter.severity !== 'Avoid' || !inter.substitute) return;
                        [inter.drug_a, inter.drug_b].forEach(drugName => {
                            const card = document.querySelector(`[data-drug-name="${drugName}"]`);
                            if (!card) return;
                            const label = card.querySelector('.drug-name-label');
                            if (label && !label.classList.contains('line-through')) {
                                label.classList.add('line-through', 'text-red-400');
                                const badge = document.createElement('span');
                                badge.className = 'text-[10px] bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded font-medium ml-2';
                                badge.textContent = '🔄 Auto-swapped';
                                label.parentElement.appendChild(badge);
                                const sub = document.createElement('p');
                                sub.className = 'text-xs text-emerald-600 mt-1 font-medium';
                                sub.textContent = `→ Replaced with: ${inter.substitute}`;
                                card.appendChild(sub);
                            }
                        });
                    });
                });
            },

            /* ── Save current result to dedicated localStorage key ── */
            saveToStorage() {
                if (!this.result) return;
                try {
                    const payload = {
                        id: 'mc_' + Date.now(),
                        savedAt: new Date().toISOString(),
                        symptoms: this.transcription || this.symptoms || '',
                        location: this.nearbyLocation || null,
                        nearbyProviders: this.nearbyProviders || null,
                        result: this.result
                    };
                    localStorage.setItem('medicheckCurrentResult', JSON.stringify(payload));
                } catch (e) {
                    console.error('Could not save to localStorage', e);
                }
            },

            /* ── Build plain-text report with watermark ── */
            buildReport() {
                const r = this.result;
                if (!r) return '';
                const watermark = '\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n  Generated by Pharmasis — Know Your Medicine\n  https://pharmasis.biz.id\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n';
                let text = '═══════════════════════════════════════\n';
                text += '        PHARMASIS MEDCHECK REPORT\n';
                text += '═══════════════════════════════════════\n';
                text += `Date: ${new Date().toLocaleString()}\n`;
                text += `Symptoms: ${this.transcription || this.symptoms || 'N/A'}\n\n`;

                if (r.step1?.conditions?.length) {
                    text += '── DIFFERENTIAL DIAGNOSIS ──\n';
                    r.step1.conditions.forEach(c => {
                        text += `[${c.likelihood}] ${c.name}\n  ${c.description}\n\n`;
                    });
                    if (r.step1.summary) text += `Summary: ${r.step1.summary}\n\n`;
                }

                if (r.step2?.drugs?.length) {
                    text += '── DRUG RECOMMENDATIONS ──\n';
                    r.step2.drugs.forEach(d => {
                        text += `• ${d.name} (${d.generic})\n`;
                        text += `  Class: ${d.drug_class || 'N/A'} | OTC: ${d.otc ? 'Yes' : 'Rx Only'}\n`;
                        text += `  Dosage: ${d.dose || 'N/A'} | Frequency: ${d.frequency || 'N/A'}\n`;
                        text += `  Purpose: ${d.purpose || 'N/A'}\n`;
                        if (d.mechanism) text += `  Mechanism: ${d.mechanism}\n`;
                        if (d.how_to_take) text += `  How to Take: ${d.how_to_take}\n`;
                        if (d.side_effects?.length) text += `  Side Effects: ${d.side_effects.join(', ')}\n`;
                        if (d.contraindications?.length) text += `  Contraindications: ${d.contraindications.join(', ')}\n`;
                        text += '\n';
                    });
                    if (r.step2.pharmacist_notes) text += `Pharmacist Note: ${r.step2.pharmacist_notes}\n\n`;
                }

                if (r.step3?.interactions?.length) {
                    text += '── DRUG INTERACTIONS ──\n';
                    r.step3.interactions.forEach(i => {
                        text += `[${i.severity}] ${i.drug_a} + ${i.drug_b}\n  Effect: ${i.effect}\n`;
                        if (i.substitute) text += `  Substitute: ${i.substitute}\n`;
                        text += '\n';
                    });
                }

                if (r.step4) {
                    text += '── RECOVERY PLAN ──\n';
                    if (r.step4.recovery_timeline) text += `Timeline: ${r.step4.recovery_timeline}\n`;
                    if (r.step4.lifestyle_tips?.length) text += `Lifestyle: ${r.step4.lifestyle_tips.join('; ')}\n`;
                    if (r.step4.diet_recommendations?.length) text += `Diet: ${r.step4.diet_recommendations.join('; ')}\n`;
                    if (r.step4.warning_signs?.length) text += `Warning Signs: ${r.step4.warning_signs.join('; ')}\n`;
                    if (r.step4.follow_up) text += `Follow-up: ${r.step4.follow_up}\n`;
                    if (r.step4.disclaimer) text += `Disclaimer: ${r.step4.disclaimer}\n`;
                    text += '\n';
                }

                text += watermark;
                return text;
            },

            /* ── Copy report to clipboard ── */
            async copyResult() {
                const text = this.buildReport();
                if (!text) return;
                try {
                    await navigator.clipboard.writeText(text);
                    this.copyFeedback = 'Copied!';
                    setTimeout(() => this.copyFeedback = '', 2000);
                } catch (e) {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    this.copyFeedback = 'Copied!';
                    setTimeout(() => this.copyFeedback = '', 2000);
                }
            },

            /* ── Share via Web Share API or fallback ── */
            async shareResult() {
                const text = this.buildReport();
                if (!text) return;
                const shareData = {
                    title: 'Pharmasis MediCheck Report',
                    text: text,
                };
                if (navigator.canShare && navigator.canShare(shareData)) {
                    try {
                        await navigator.share(shareData);
                    } catch (e) {
                        if (e.name !== 'AbortError') {
                            await navigator.clipboard.writeText(text);
                            this.copyFeedback = 'Shared!';
                            setTimeout(() => this.copyFeedback = '', 2000);
                        }
                    }
                } else {
                    await navigator.clipboard.writeText(text);
                    this.copyFeedback = 'Copied for sharing!';
                    setTimeout(() => this.copyFeedback = '', 2000);
                }
            },

            /* ── Build clean HTML for PDF/print ── */
            buildPrintHTML() {
                const r = this.result;
                if (!r) return '';
                const dateStr = new Date().toLocaleString();
                const symptoms = this.transcription || this.symptoms || 'N/A';
                let html = '';

                // Header
                html += '<div style="text-align:center;margin-bottom:2rem;border-bottom:2px solid #3EAEB1;padding-bottom:1rem;">';
                html += '<h1 style="font-size:1.6rem;font-weight:800;color:#0d4f52;margin:0;font-family:system-ui,sans-serif;">Pharmasis MediCheck Report</h1>';
                html += '<p style="font-size:0.75rem;color:#64748b;margin:0.5rem 0 0;">Generated on ' + dateStr + '</p>';
                html += '</div>';

                // Symptoms
                html += '<div style="margin-bottom:1.5rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:0.5rem;padding:1rem;">';
                html += '<p style="font-size:0.7rem;font-weight:700;color:#3EAEB1;text-transform:uppercase;letter-spacing:0.05em;margin:0 0 0.5rem;">Symptoms</p>';
                html += '<p style="font-size:0.9rem;color:#334155;margin:0;font-style:italic;">"' + this._esc(symptoms) + '"</p>';
                html += '</div>';

                // Step 1: Diagnosis
                if (r.step1?.conditions?.length) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #3EAEB1;padding-left:0.5rem;">Differential Diagnosis</h2>';
                    r.step1.conditions.forEach(c => {
                        const badgeColor = c.likelihood === 'High' ? '#dc2626' : c.likelihood === 'Moderate' ? '#d97706' : '#059669';
                        const badgeBg = c.likelihood === 'High' ? '#fee2e2' : c.likelihood === 'Moderate' ? '#fef3c7' : '#d1fae5';
                        html += '<div style="margin-bottom:0.75rem;border:1px solid #e2e8f0;border-radius:0.5rem;padding:0.75rem;background:#fff;">';
                        html += '<div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.25rem;">';
                        html += '<span style="font-size:0.6rem;font-weight:700;padding:0.15rem 0.4rem;border-radius:0.25rem;text-transform:uppercase;" style="background:' + badgeBg + ';color:' + badgeColor + ';">' + this._esc(c.likelihood) + '</span>';
                        html += '<span style="font-size:0.85rem;font-weight:700;color:#1e293b;">' + this._esc(c.name) + '</span>';
                        html += '</div>';
                        html += '<p style="font-size:0.8rem;color:#475569;margin:0;">' + this._esc(c.description) + '</p>';
                        html += '</div>';
                    });
                    if (r.step1.summary) html += '<p style="font-size:0.8rem;color:#64748b;font-style:italic;margin:0.5rem 0 0;">' + this._esc(r.step1.summary) + '</p>';
                }

                // Step 2: Drugs
                if (r.step2?.drugs?.length) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #3EAEB1;padding-left:0.5rem;">Drug Recommendations</h2>';
                    r.step2.drugs.forEach(d => {
                        html += '<div style="margin-bottom:1rem;border:1px solid #e2e8f0;border-radius:0.5rem;padding:1rem;background:#fff;page-break-inside:avoid;">';
                        html += '<div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;flex-wrap:wrap;">';
                        html += '<span style="font-size:0.9rem;font-weight:700;color:#0f172a;">' + this._esc(d.name) + '</span>';
                        html += '<span style="font-size:0.6rem;padding:0.15rem 0.4rem;border-radius:0.25rem;background:#f1f5f9;color:#475569;">' + this._esc(d.generic) + '</span>';
                        html += '<span style="font-size:0.6rem;padding:0.15rem 0.4rem;border-radius:0.25rem;background:' + (d.otc ? '#d1fae5;color:#065f46' : '#ede9fe;color:#5b21b6') + ';">' + (d.otc ? 'OTC' : 'Rx Only') + '</span>';
                        if (d.drug_class) html += '<span style="font-size:0.6rem;padding:0.15rem 0.4rem;border-radius:0.25rem;background:#f1f5f9;color:#475569;">' + this._esc(d.drug_class) + '</span>';
                        html += '</div>';
                        html += '<table style="width:100%;border-collapse:collapse;font-size:0.8rem;margin-bottom:0.5rem;">';
                        if (d.dose) html += '<tr><td style="padding:0.25rem 0;color:#64748b;width:30%;">Dosage</td><td style="padding:0.25rem 0;color:#334155;font-weight:600;">' + this._esc(d.dose) + '</td></tr>';
                        if (d.frequency) html += '<tr><td style="padding:0.25rem 0;color:#64748b;">Frequency</td><td style="padding:0.25rem 0;color:#334155;font-weight:600;">' + this._esc(d.frequency) + '</td></tr>';
                        if (d.duration) html += '<tr><td style="padding:0.25rem 0;color:#64748b;">Duration</td><td style="padding:0.25rem 0;color:#334155;font-weight:600;">' + this._esc(d.duration) + '</td></tr>';
                        if (d.route) html += '<tr><td style="padding:0.25rem 0;color:#64748b;">Route</td><td style="padding:0.25rem 0;color:#334155;font-weight:600;">' + this._esc(d.route) + '</td></tr>';
                        html += '</table>';
                        if (d.purpose) html += '<div style="background:#eff6ff;border-radius:0.375rem;padding:0.5rem;margin-bottom:0.5rem;"><p style="font-size:0.65rem;font-weight:700;color:#3b82f6;text-transform:uppercase;margin:0 0 0.25rem;">Purpose</p><p style="font-size:0.8rem;color:#1e3a8a;margin:0;">' + this._esc(d.purpose) + '</p></div>';
                        if (d.mechanism) html += '<div style="background:#eef2ff;border-radius:0.375rem;padding:0.5rem;margin-bottom:0.5rem;"><p style="font-size:0.65rem;font-weight:700;color:#6366f1;text-transform:uppercase;margin:0 0 0.25rem;">Mechanism</p><p style="font-size:0.8rem;color:#3730a3;margin:0;">' + this._esc(d.mechanism) + '</p></div>';
                        if (d.how_to_take) html += '<div style="background:#ecfdf5;border-radius:0.375rem;padding:0.5rem;margin-bottom:0.5rem;"><p style="font-size:0.65rem;font-weight:700;color:#059669;text-transform:uppercase;margin:0 0 0.25rem;">How to Take</p><p style="font-size:0.8rem;color:#064e3b;margin:0;">' + this._esc(d.how_to_take) + '</p></div>';
                        if (d.side_effects?.length) html += '<p style="font-size:0.75rem;color:#b45309;margin:0;"><strong>Side Effects:</strong> ' + d.side_effects.map(s => this._esc(s)).join(', ') + '</p>';
                        if (d.contraindications?.length) html += '<p style="font-size:0.75rem;color:#dc2626;margin:0.25rem 0 0;"><strong>Contraindications:</strong> ' + d.contraindications.map(s => this._esc(s)).join(', ') + '</p>';
                        html += '</div>';
                    });
                    if (r.step2.pharmacist_notes) html += '<div style="background:#fffbeb;border:1px solid #fcd34d;border-radius:0.5rem;padding:0.75rem;margin-bottom:1rem;"><p style="font-size:0.7rem;font-weight:700;color:#b45309;text-transform:uppercase;margin:0 0 0.25rem;">Pharmacist Note</p><p style="font-size:0.8rem;color:#78350f;margin:0;">' + this._esc(r.step2.pharmacist_notes) + '</p></div>';
                }

                // Step 3: Interactions
                if (r.step3?.interactions?.length) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #3EAEB1;padding-left:0.5rem;">Drug Interactions</h2>';
                    r.step3.interactions.forEach(i => {
                        const bg = i.severity === 'Avoid' ? '#fef2f2' : i.severity === 'Caution' ? '#fffbeb' : '#f8fafc';
                        const border = i.severity === 'Avoid' ? '#fecaca' : i.severity === 'Caution' ? '#fcd34d' : '#e2e8f0';
                        const badgeColor = i.severity === 'Avoid' ? '#991b1b' : i.severity === 'Caution' ? '#92400e' : '#334155';
                        const badgeBg = i.severity === 'Avoid' ? '#fecaca' : i.severity === 'Caution' ? '#fde68a' : '#e2e8f0';
                        html += '<div style="margin-bottom:0.5rem;border:1px solid ' + border + ';border-radius:0.5rem;padding:0.75rem;background:' + bg + ';">';
                        html += '<span style="font-size:0.65rem;font-weight:700;padding:0.15rem 0.4rem;border-radius:0.25rem;background:' + badgeBg + ';color:' + badgeColor + ';">' + this._esc(i.severity) + '</span>';
                        html += '<p style="font-size:0.85rem;font-weight:600;color:#1e293b;margin:0.25rem 0;">' + this._esc(i.drug_a) + ' + ' + this._esc(i.drug_b) + '</p>';
                        html += '<p style="font-size:0.8rem;color:#475569;margin:0;">' + this._esc(i.effect) + '</p>';
                        if (i.substitute) html += '<p style="font-size:0.8rem;color:#059669;margin:0.25rem 0 0;">Substitute: ' + this._esc(i.substitute) + '</p>';
                        html += '</div>';
                    });
                }

                // Step 4: Recovery Plan
                if (r.step4) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #3EAEB1;padding-left:0.5rem;">Recovery Plan</h2>';
                    if (r.step4.recovery_timeline) html += '<div style="display:flex;align-items:flex-start;gap:0.75rem;background:#f0fdfa;border-radius:0.5rem;padding:0.75rem;margin-bottom:0.75rem;"><span style="font-size:1.1rem;"></span><div><p style="font-size:0.65rem;font-weight:700;color:#0f766e;text-transform:uppercase;margin:0 0 0.25rem;">Timeline</p><p style="font-size:0.85rem;color:#134e4a;margin:0;font-weight:600;">' + this._esc(r.step4.recovery_timeline) + '</p></div></div>';

                    if (r.step4.usage_instructions?.length) {
                        html += '<p style="font-size:0.7rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;margin:1rem 0 0.5rem;">Usage Instructions</p>';
                        r.step4.usage_instructions.forEach(u => {
                            html += '<div style="border:1px solid #e2e8f0;border-radius:0.5rem;padding:0.75rem;margin-bottom:0.5rem;background:#fff;">';
                            html += '<p style="font-size:0.85rem;font-weight:700;color:#0f172a;margin:0 0 0.5rem;">' + this._esc(u.drug) + '</p>';
                            if (u.step_by_step?.length) {
                                u.step_by_step.forEach((s, idx) => {
                                    html += '<div style="display:flex;align-items:flex-start;gap:0.5rem;margin-bottom:0.25rem;">';
                                    html += '<span style="width:1.1rem;height:1.1rem;border-radius:50%;background:#ccfbf1;color:#0f766e;font-size:0.6rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:0.1rem;">' + (idx + 1) + '</span>';
                                    html += '<p style="font-size:0.8rem;color:#334155;margin:0;">' + this._esc(s) + '</p>';
                                    html += '</div>';
                                });
                            }
                            if (u.instructions && !u.step_by_step?.length) html += '<p style="font-size:0.8rem;color:#334155;margin:0 0 0.5rem;">' + this._esc(u.instructions) + '</p>';
                            html += '</div>';
                        });
                    }

                    html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:0.75rem;">';
                    if (r.step4.lifestyle_tips?.length) {
                        html += '<div style="background:#ecfdf5;border-radius:0.5rem;padding:0.75rem;">';
                        html += '<p style="font-size:0.65rem;font-weight:700;color:#059669;text-transform:uppercase;margin:0 0 0.5rem;">Lifestyle</p>';
                        r.step4.lifestyle_tips.forEach(t => html += '<p style="font-size:0.8rem;color:#064e3b;margin:0 0 0.25rem;">• ' + this._esc(t) + '</p>');
                        html += '</div>';
                    }
                    if (r.step4.diet_recommendations?.length) {
                        html += '<div style="background:#f0fdfa;border-radius:0.5rem;padding:0.75rem;">';
                        html += '<p style="font-size:0.65rem;font-weight:700;color:#0f766e;text-transform:uppercase;margin:0 0 0.5rem;">Diet</p>';
                        r.step4.diet_recommendations.forEach(d => html += '<p style="font-size:0.8rem;color:#134e4a;margin:0 0 0.25rem;">• ' + this._esc(d) + '</p>');
                        html += '</div>';
                    }
                    html += '</div>';

                    if (r.step4.warning_signs?.length) {
                        html += '<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:0.5rem;padding:0.75rem;margin-bottom:0.75rem;">';
                        html += '<p style="font-size:0.7rem;font-weight:700;color:#dc2626;text-transform:uppercase;margin:0 0 0.5rem;">Seek Medical Help If</p>';
                        r.step4.warning_signs.forEach(w => html += '<p style="font-size:0.8rem;color:#991b1b;margin:0 0 0.25rem;">• ' + this._esc(w) + '</p>');
                        html += '</div>';
                    }
                    if (r.step4.follow_up) html += '<div style="background:#f8fafc;border-radius:0.5rem;padding:0.75rem;margin-bottom:0.5rem;"><p style="font-size:0.65rem;font-weight:700;color:#64748b;text-transform:uppercase;margin:0 0 0.25rem;">Follow-up</p><p style="font-size:0.8rem;color:#334155;margin:0;">' + this._esc(r.step4.follow_up) + '</p></div>';
                    if (r.step4.disclaimer) html += '<p style="font-size:0.75rem;color:#94a3b8;font-style:italic;margin:1rem 0 0;border-top:1px solid #e2e8f0;padding-top:0.75rem;">' + this._esc(r.step4.disclaimer) + '</p>';
                }

                // Nearby providers
                if (this.nearbyProviders?.providers?.length) {
                    html += '<h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin:1.5rem 0 0.75rem;border-left:3px solid #3EAEB1;padding-left:0.5rem;">Nearby Healthcare Providers</h2>';
                    html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">';
                    this.nearbyProviders.providers.forEach(p => {
                        html += '<div style="border:1px solid #e2e8f0;border-radius:0.5rem;padding:0.75rem;background:#fff;">';
                        html += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">';
                        html += '<p style="font-size:0.85rem;font-weight:700;color:#0f172a;margin:0;">' + this._esc(p.name) + '</p>';
                        html += '<span style="font-size:0.6rem;padding:0.1rem 0.35rem;border-radius:0.25rem;background:#f1f5f9;color:#475569;font-weight:600;">' + this._esc(p.type) + '</span>';
                        html += '</div>';
                        html += '<p style="font-size:0.75rem;color:#475569;margin:0 0 0.5rem;">' + this._esc(p.address) + '</p>';
                        html += '</div>';
                    });
                    html += '</div>';
                    if (this.nearbyProviders.emergency_numbers) html += '<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:0.5rem;padding:0.75rem;margin-top:0.75rem;display:flex;align-items:center;gap:0.5rem;"><span style="width:1.25rem;height:1.25rem;border-radius:0.25rem;background:#fee2e2;color:#dc2626;display:flex;align-items:center;justify-content:center;font-size:0.6rem;font-weight:700;flex-shrink:0;">!</span><p style="font-size:0.8rem;color:#991b1b;font-weight:700;margin:0;">' + this._esc(this.nearbyProviders.emergency_numbers) + '</p></div>';
                }

                // Footer watermark
                html += '<div style="margin-top:2rem;padding-top:1rem;border-top:2px solid #3EAEB1;text-align:center;">';
                html += '<p style="font-size:0.75rem;color:#3EAEB1;font-weight:600;margin:0;">Pharmasis — Know Your Medicine</p>';
                html += '<p style="font-size:0.65rem;color:#94a3b8;margin:0.25rem 0 0;">This report is for educational purposes only. Consult a healthcare professional for medical advice.</p>';
                html += '</div>';

                return html;
            },

            _esc(str) {
                if (!str) return '';
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            },

            /* ── Save as PDF via new print window ── */
            savePdf() {
                const html = this.buildPrintHTML();
                if (!html) return;
                const printWin = window.open('', '_blank');
                printWin.document.write(`
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Pharmasis MediCheck Report</title>
                        <meta charset="utf-8">
                        <style>
                            @page { margin: 1.5cm; size: A4; }
                            body { margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: #334155; background: #fff; line-height: 1.5; }
                            .watermark { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
                            .watermark > div { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); opacity: 0.06; font-size: 5rem; font-weight: 800; color: #3EAEB1; white-space: nowrap; letter-spacing: 0.1em; }
                            .content { position: relative; z-index: 1; }
                            @media print { .no-print { display: none !important; } }
                        </style>
                    </head>
                    <body>
                        <div class="watermark"><div>Pharmasis</div></div>
                        <div class="content" style="padding: 1.5cm;">${html}</div>
                        <div class="no-print" style="text-align:center;padding: 2rem;">
                            <button onclick="window.print()" style="padding: 0.6rem 2rem; border-radius: 999px; border: none; background: linear-gradient(135deg,#3EAEB1,#2d8a8d); color: #fff; font-weight: 700; font-size: 0.9rem; cursor: pointer; box-shadow: 0 4px 16px rgba(62,174,177,0.35);">Print / Save as PDF</button>
                        </div>
                    </body>
                    </html>
                `);
                printWin.document.close();
                printWin.focus();
                setTimeout(() => printWin.print(), 400);
            },

            /* ── Initialize component ────────────────────────────────── */
            init() {
                // Initialize textarea auto-resize
                this.$nextTick(() => {
                    this.autoResizeTextarea();
                });
            }
        };
    }
</script>