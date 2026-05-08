{{-- Cookie Consent Banner --}}
<div x-data="cookieConsent()" x-show="showBanner" x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-0 left-0 right-0 z-[200] p-4 sm:p-5">

    <div class="max-w-3xl mx-auto rounded-2xl overflow-hidden"
        style="background: rgba(255,255,255,0.92); backdrop-filter: blur(28px) saturate(160%); -webkit-backdrop-filter: blur(28px) saturate(160%); border: 1px solid rgba(255,255,255,0.95); box-shadow: 0 24px 64px rgba(11,31,36,0.18), 0 1px 0 rgba(255,255,255,0.9) inset;">

        {{-- Detail panel (toggle) --}}
        <div x-show="showDetail" x-transition class="px-6 pt-5 pb-3" style="border-bottom: 1px solid rgba(62,174,177,0.10);">
            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3">Cookie Details</h4>
            <div class="space-y-3">
                {{-- Cookie 1: MediCheck History --}}
                <div class="flex items-start gap-3 p-3 rounded-xl" style="background: rgba(62,174,177,0.06); border: 1px solid rgba(62,174,177,0.10);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                        style="background: linear-gradient(135deg, rgba(62,174,177,0.15), rgba(97,186,202,0.25)); border: 1px solid rgba(62,174,177,0.15);">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800">MediCheck Consultation History</p>
                        <p class="text-xs text-slate-500 leading-relaxed mt-0.5">Stores your past AI symptom analysis results locally on your device so you can review previous consultations, drug recommendations, and recovery plans at any time.</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(62,174,177,0.10);color:#2d8a8d;">localStorage</span>
                            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(62,174,177,0.10);color:#2d8a8d;">First-party</span>
                            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(62,174,177,0.10);color:#2d8a8d;">No expiry</span>
                        </div>
                    </div>
                </div>

                {{-- Cookie 2: Device Location --}}
                <div class="flex items-start gap-3 p-3 rounded-xl" style="background: rgba(5,150,105,0.05); border: 1px solid rgba(5,150,105,0.10);">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                        style="background: linear-gradient(135deg, rgba(5,150,105,0.12), rgba(5,150,105,0.22)); border: 1px solid rgba(5,150,105,0.15);">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800">Device Location for Nearby Medical Centers</p>
                        <p class="text-xs text-slate-500 leading-relaxed mt-0.5">Uses your device's IP-based location to find and display nearby hospitals, clinics, and pharmacies when you run a MediCheck analysis, so you can get help quickly when needed.</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(5,150,105,0.08);color:#059669;">IP Geolocation</span>
                            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(5,150,105,0.08);color:#059669;">First-party</span>
                            <span class="text-[9px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(5,150,105,0.08);color:#059669;">Per-session</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main banner --}}
        <div class="px-5 sm:px-6 py-4 sm:py-5">
            <div class="flex items-start gap-3 sm:gap-4">
                {{-- Icon --}}
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                    style="background: linear-gradient(135deg, rgba(62,174,177,0.15), rgba(97,186,202,0.25)); border: 1px solid rgba(62,174,177,0.15);">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>

                {{-- Text --}}
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-bold text-slate-800 mb-1">Your privacy matters</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        We use cookies to store your <strong class="text-slate-700">MediCheck consultation history</strong> and check your <strong class="text-slate-700">device location</strong> to find nearby medical centers. No tracking or advertising cookies ever.
                        <button @click="showDetail = !showDetail" class="text-primary font-semibold hover:underline ml-1 inline-flex items-center gap-0.5">
                            <span x-text="showDetail ? 'Hide details' : 'Learn more'"></span>
                            <svg class="w-3 h-3 transition-transform duration-200" :class="showDetail ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-2 mt-4 pt-3" style="border-top: 1px solid rgba(62,174,177,0.08);">
                <button @click="deny()"
                    class="px-4 py-2 text-xs font-semibold rounded-full transition-all"
                    style="color: #64748b; background: rgba(241,245,249,0.8); border: 1px solid rgba(203,213,225,0.5);"
                    onmouseover="this.style.background='rgba(226,232,240,0.9)'"
                    onmouseout="this.style.background='rgba(241,245,249,0.8)'">
                    Decline
                </button>
                <button @click="accept()"
                    class="px-5 py-2 text-xs font-semibold text-white rounded-full transition-all flex items-center gap-1.5"
                    style="background: linear-gradient(135deg,#3EAEB1,#2d8a8d); box-shadow: 0 4px 14px rgba(62,174,177,0.35);"
                    onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px rgba(62,174,177,0.4)'"
                    onmouseout="this.style.transform='';this.style.boxShadow='0 4px 14px rgba(62,174,177,0.35)'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Accept All Cookies
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('cookieConsent', () => ({
        showBanner: false,
        showDetail: false,

        init() {
            const status = localStorage.getItem('cookie_consent_status');
            // Show banner if: never answered, or previously denied
            if (!status || status === 'denied') {
                this.showBanner = true;
            }
        },

        accept() {
            localStorage.setItem('cookie_consent_status', 'accepted');
            localStorage.setItem('cookie_consent_date', new Date().toISOString());
            this.showBanner = false;
            window.dispatchEvent(new CustomEvent('cookie-consent-changed', { detail: { status: 'accepted' } }));
        },

        deny() {
            localStorage.setItem('cookie_consent_status', 'denied');
            localStorage.setItem('cookie_consent_date', new Date().toISOString());
            // Clear stored data since user declined
            localStorage.removeItem('medicheckHistory');
            localStorage.removeItem('medicheckCurrentResult');
            this.showBanner = false;
            window.dispatchEvent(new CustomEvent('cookie-consent-changed', { detail: { status: 'denied' } }));
        }
    }));
});

/* ── Global helper: check if cookie consent is accepted ── */
window.cookieConsentAccepted = function () {
    return localStorage.getItem('cookie_consent_status') === 'accepted';
};
</script>
