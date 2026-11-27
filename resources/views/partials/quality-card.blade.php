{{-- Notices and limits from ResponseQuality. $expr: Alpine expression yielding the report. --}}
<template x-for="q in [{{ $expr }}].filter(Boolean)" :key="q.limits.join() + q.notices.join()">
    <section class="rounded-3xl bg-white border border-slate-300 px-5 py-4 space-y-2 text-left" :aria-label="q.labels.title">
        <h3 class="text-sm font-bold text-slate-900" x-text="q.labels.title"></h3>
        <template x-for="notice in q.notices" :key="notice">
            <p role="status" class="rounded-xl bg-amber-50 border border-amber-300 px-3 py-2 text-sm text-amber-900 break-words" x-text="notice"></p>
        </template>
        <ul class="list-disc pl-5 text-sm text-slate-700 space-y-1">
            <template x-for="limit in q.limits" :key="limit"><li x-text="limit"></li></template>
        </ul>
    </section>
</template>
