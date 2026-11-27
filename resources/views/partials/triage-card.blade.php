{{-- Urgency assessment from TriageService. $expr: Alpine expression yielding it. --}}
<template x-for="t in [{{ $expr }}].filter(Boolean)" :key="t.category + t.title">
    <section role="status" class="rounded-3xl border-2 px-5 py-4 space-y-3 text-left"
        :class="{ 'bg-red-50 border-red-600': t.category === 'emergency', 'bg-amber-50 border-amber-500': t.category === 'urgent', 'bg-emerald-50 border-emerald-600': t.category === 'routine', 'bg-slate-50 border-slate-500': t.category === 'unable_to_assess' }">
        <p x-show="!t.reviewed" class="text-xs font-bold text-red-800" x-text="t.labels.unreviewed"></p>
        <div>
            <p class="text-xs font-semibold text-slate-600" x-text="t.labels.heading"></p>
            <h3 class="text-xl font-bold text-slate-900" x-text="t.title"></h3>
            <p class="text-sm text-slate-800" x-text="t.meaning"></p>
        </div>

        <div x-show="t.next_steps.length">
            <h4 class="text-sm font-bold text-slate-900" x-text="t.labels.steps"></h4>
            <ul class="list-disc pl-5 text-sm text-slate-800 space-y-1">
                <template x-for="step in t.next_steps" :key="step"><li x-text="step"></li></template>
            </ul>
        </div>

        <a x-show="t.category === 'emergency'" :href="'tel:' + t.number"
            class="inline-block rounded-full bg-red-700 px-6 py-3 text-base font-bold text-white"
            x-text="t.labels.call_button + ' ' + t.number"></a>

        <div x-show="t.reasons.length">
            <h4 class="text-sm font-bold text-slate-900" x-text="t.labels.why"></h4>
            <ul class="list-disc pl-5 text-sm text-slate-800">
                <template x-for="reason in t.reasons" :key="reason"><li x-text="reason"></li></template>
            </ul>
        </div>

        <div x-show="t.missing.length">
            <h4 class="text-sm font-bold text-slate-900" x-text="t.labels.unknown"></h4>
            <ul class="list-disc pl-5 text-sm text-slate-800">
                <template x-for="gap in t.missing" :key="gap"><li x-text="gap"></li></template>
            </ul>
        </div>

        <p x-show="t.watch_for" class="text-sm font-semibold text-slate-800" x-text="t.watch_for"></p>

        <p x-show="t.sources.length" class="text-xs text-slate-600">
            <span x-text="t.labels.basis"></span>
            <template x-for="s in t.sources" :key="s.url">
                <a :href="s.url" target="_blank" rel="noopener noreferrer" class="underline mr-2" x-text="s.title"></a>
            </template>
        </p>
        <p class="text-xs text-slate-600" x-text="t.labels.not_diagnosis"></p>
    </section>
</template>
