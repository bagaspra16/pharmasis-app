{{-- When the server asks for a browser check, solve it and repeat the request.
     Wraps fetch so existing callers need no change. --}}
@if (config('botguard.mode') !== 'off')
<script>
(() => {
    const texts = {!! \Illuminate\Support\Js::from(['en' => trans('botguard', [], 'en'), 'id' => trans('botguard', [], 'id')]) !!};
    const t = () => texts[window.PharmasisI18n && window.PharmasisI18n.getLanguage() === 'id' ? 'id' : 'en'];
    const realFetch = window.fetch.bind(window);
    let running = null;

    const el = (tag, text, css) => {
        const node = document.createElement(tag);
        if (text) node.textContent = text;
        if (css) node.style.cssText = css;
        return node;
    };

    function zeroBits(bytes) {
        let bits = 0;
        for (const byte of bytes) {
            if (byte === 0) { bits += 8; continue; }
            return bits + Math.clz32(byte) - 24;
        }
        return bits;
    }

    async function solve(challenge, stopped) {
        if (!window.crypto || !window.crypto.subtle) return null;
        const encoder = new TextEncoder();
        for (let n = 0; n < 4000000 && !stopped(); n += 256) {
            const batch = Array.from({ length: 256 }, (_, i) =>
                crypto.subtle.digest('SHA-256', encoder.encode(challenge.nonce + ':' + (n + i))));
            const found = (await Promise.all(batch)).findIndex(d => zeroBits(new Uint8Array(d)) >= challenge.bits);
            if (found >= 0) return String(n + found);
        }
        return null;
    }

    /* Resolves with {solution} or {answer}, or null if the person cancels. */
    function ask(challenge) {
        const box = el('div', '', 'position:fixed;left:16px;right:16px;bottom:16px;max-width:420px;margin:0 auto;z-index:2147483647;background:#fff;color:#0f172a;border:2px solid #0f766e;border-radius:16px;padding:16px;box-shadow:0 12px 40px rgba(0,0,0,.2);font-size:14px');
        box.setAttribute('role', 'dialog');
        box.setAttribute('aria-label', t().checking);
        const status = el('p', t().checking, 'margin:0 0 8px');
        status.setAttribute('role', 'status');
        const emergency = el('p', t().emergency, 'margin:0 0 8px;font-weight:700;color:#991b1b');
        const buttons = el('div', '', 'display:flex;gap:8px;flex-wrap:wrap');
        const button = (label) => {
            const b = el('button', label, 'min-height:44px;padding:0 16px;border-radius:999px;border:1px solid #0f766e;background:#fff;color:#115e59;font-weight:700');
            b.type = 'button';
            buttons.appendChild(b);
            return b;
        };
        box.append(status, emergency, buttons);
        document.body.appendChild(box);

        return new Promise(resolve => {
            let done = false;
            const finish = result => { if (!done) { done = true; box.remove(); resolve(result); } };
            const showQuestion = () => {
                if (done || box.querySelector('input')) return;
                done = 'asking';
                other?.remove();
                status.textContent = challenge.question;
                const form = el('form', '', 'display:flex;gap:8px;margin-bottom:8px');
                const input = el('input', '', 'flex:1;min-width:0;min-height:44px;border:1px solid #64748b;border-radius:8px;padding:0 12px');
                input.inputMode = 'numeric';
                input.setAttribute('aria-label', t().answer + '. ' + challenge.question);
                const submit = el('button', t().submit, 'min-height:44px;padding:0 16px;border-radius:999px;border:0;background:#0f766e;color:#fff;font-weight:700');
                submit.type = 'submit';
                form.append(input, submit);
                form.addEventListener('submit', e => { e.preventDefault(); done = false; finish({ answer: input.value }); });
                box.insertBefore(form, buttons);
                input.focus();
            };

            const other = challenge.question ? button(t().use_question) : null;
            other?.addEventListener('click', showQuestion);
            box.addEventListener('keydown', e => { if (e.key === 'Escape') { done = false; finish(null); } });
            button(t().cancel).addEventListener('click', () => { done = false; finish(null); });

            solve(challenge, () => done !== false).then(solution => {
                if (done !== false) return;
                if (solution !== null) finish({ solution });
                else if (challenge.question) showQuestion();
                else finish(null);
            }).catch(() => (challenge.question ? showQuestion() : finish(null)));
        });
    }

    async function verify(challenge) {
        const result = await ask(challenge);
        if (!result) return false;
        const response = await realFetch('/bot/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json', 'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify({ id: challenge.id, ...result }),
        });
        return response.ok;
    }

    window.fetch = async (input, init) => {
        const response = await realFetch(input, init);
        const url = new URL(typeof input === 'string' ? input : input.url, location.href);
        if (response.status !== 429 || url.origin !== location.origin) return response;

        const data = await response.clone().json().catch(() => null);
        if (!data || !data.bot_challenge) return response;

        // One check at a time: other blocked requests wait for it, then retry.
        if (!running) running = verify(data.bot_challenge).finally(() => { running = null; });
        return (await running) ? realFetch(input, init) : response;
    };
})();
</script>
@endif
