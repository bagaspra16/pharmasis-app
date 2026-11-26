{{-- Evidence report for a medical response.
     $expr: Alpine expression that yields the report built by EvidenceService.
     All model and source text is bound with x-text, never as HTML. --}}
<template x-for="ev in [{{ $expr }}].filter(Boolean)" :key="ev.status + ev.summary.total">
    <section x-data="{ open: false }" class="rounded-3xl bg-white border border-slate-200 px-5 py-4 space-y-3 text-left"
        :aria-label="ev.labels.title">
        <div>
            <h3 class="text-sm font-bold text-slate-800" x-text="ev.labels.title"></h3>
            <p class="mt-1 text-xs leading-relaxed" role="status"
                :class="ev.status === 'verified' ? 'text-emerald-800' : 'text-amber-800'"
                x-text="ev.labels.status[ev.status]"></p>
            <p x-show="ev.summary.total" class="mt-1 text-xs text-slate-600">
                <span x-text="ev.summary.supported + ' / ' + ev.summary.total"></span>
                <span x-text="ev.labels.count"></span>
            </p>
        </div>

        <p x-show="ev.summary.critical_unsupported" class="rounded-xl bg-red-50 border border-red-200 px-3 py-2 text-xs text-red-800">
            <strong x-text="ev.summary.critical_unsupported"></strong>
            <span x-text="ev.labels.critical_warning"></span>
        </p>

        <div x-show="ev.sources.length">
            <h4 class="text-xs font-bold text-slate-700 mb-1.5" x-text="ev.labels.references"></h4>
            <ul class="space-y-2">
                <template x-for="s in ev.sources" :key="s.id">
                    <li class="text-xs leading-relaxed text-slate-600 break-words">
                        <span class="font-bold" x-text="'[' + s.id + ']'"></span>
                        <a :href="s.url" target="_blank" rel="noopener noreferrer" class="font-semibold text-teal-700 underline" x-text="s.title"></a>
                        <span class="block" x-text="s.publisher + ' · ' + (s.date ? ev.labels.date_kind[s.date_kind] + ' ' + s.date : ev.labels.no_date) + ' · ' + ev.labels.tier[s.tier]"></span>
                        <span x-show="s.stale" class="inline-block rounded bg-amber-100 text-amber-900 px-1.5" x-text="ev.labels.stale"></span>
                    </li>
                </template>
            </ul>
        </div>

        <div x-show="ev.claims.length">
            <button type="button" @click="open = !open" :aria-expanded="open"
                class="text-xs font-semibold text-teal-700 underline py-2"
                x-text="open ? ev.labels.hide : ev.labels.show"></button>
            <ul x-show="open" class="space-y-2.5 mt-1">
                <template x-for="c in ev.claims" :key="c.id">
                    <li class="border-t border-slate-100 pt-2.5 text-xs leading-relaxed text-slate-700 break-words">
                        <p x-text="c.text"></p>
                        <p class="mt-1 flex flex-wrap gap-1.5">
                            <span class="rounded px-1.5 font-semibold"
                                :class="c.status === 'supported' ? 'bg-emerald-100 text-emerald-900' : (c.status === 'unsupported' || c.status === 'unchecked' ? 'bg-slate-100 text-slate-700' : 'bg-red-100 text-red-900')"
                                x-text="ev.labels.claim[c.status]"></span>
                            <span x-show="c.level" class="rounded bg-slate-100 px-1.5" x-text="c.level ? ev.labels.level[c.level] : ''"></span>
                            <span x-show="c.critical" class="rounded bg-amber-100 text-amber-900 px-1.5" x-text="ev.labels.critical"></span>
                        </p>
                        <template x-for="q in c.supports" :key="q.source + q.quote">
                            <p class="mt-1 text-slate-500" x-text="'[' + q.source + '] “' + q.quote + '”'"></p>
                        </template>
                        <template x-for="q in c.contradicts" :key="q.source + q.quote">
                            <p class="mt-1 text-red-800" x-text="ev.labels.against + ' [' + q.source + '] “' + q.quote + '”'"></p>
                        </template>
                    </li>
                </template>
            </ul>
        </div>

        <p class="text-[11px] text-slate-500">
            <span x-text="ev.labels.method"></span>
            <span x-text="ev.labels.checked_on + ' ' + ev.checked_at + '.'"></span>
        </p>
    </section>
</template>
