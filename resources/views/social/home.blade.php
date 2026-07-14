@extends('layouts.app')

@section('title', 'Skrining Penyakit Berbasis AI')
@section('meta_description', (!empty($dbOffline) || !empty($fdaMode))
    ? 'Konsultasikan gejala penyakit Anda menggunakan Skrining Kesehatan AI mutakhir. Ketahui informasi 67.000+ obat secara akurat, cepat, dan up-to-date.'
    : 'Konsultasikan gejala penyakit Anda menggunakan Skrining Kesehatan AI mutakhir. Ketahui informasi 16.000+ obat secara akurat, cepat, dan up-to-date.')

@push('head')
<style>
    .display-italic {
        font-family: 'Instrument Serif', serif;
        font-style: italic;
        font-weight: 400;
        letter-spacing: -0.01em;
    }
    .display-roman {
        font-family: 'Instrument Serif', serif;
        font-weight: 400;
        letter-spacing: -0.015em;
    }
    .drug-card {
        transition: transform 320ms cubic-bezier(.22,1,.36,1), box-shadow 320ms ease;
    }
    .drug-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 44px rgba(11,31,36,0.10);
    }
    .alpha-pill {
        transition: background 220ms ease, color 220ms ease, transform 220ms ease, border-color 220ms ease;
    }
    .alpha-pill:hover { transform: scale(1.08); }

    @media (prefers-reduced-motion: reduce) {
        .drug-card, .alpha-pill { transition: none; }
        .drug-card:hover, .alpha-pill:hover { transform: none; }
    }
</style>
@endpush

@section('content')

{{-- ── DB Offline Banner ── --}}
@if(!empty($dbOffline))
<div class="border-b border-amber-200/60" style="background: rgba(254,243,199,0.65); backdrop-filter: blur(10px);"
    x-data="{ show: true }" x-init="setTimeout(() => show = false, 8000)" x-show="show" x-transition.opacity>
    <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-center gap-3">
        <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7" />
        </svg>
        <p class="text-xs text-amber-800 flex-1">
            <strong>Menggunakan sumber data OpenFDA.</strong> Basis data lokal kami sedang tidak tersedia, jadi hasil dimuat dari OpenFDA.
        </p>
        <button onclick="window.location.reload()"
            class="text-xs text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg hover:bg-amber-100 transition-colors">Coba lagi</button>
        <button @click="show=false" class="text-amber-400 hover:text-amber-700 ml-1">✕</button>
    </div>
</div>
@endif

@include('partials.hero-medicheck-id')

{{-- ── Riwayat AI (localStorage) ── --}}
@include('partials.medicheck-history')

{{-- ════════════════════════════════════════ OBAT POPULER ════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    <div class="flex items-end justify-between mb-10 flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <span class="w-1 h-4 rounded-full" style="background: linear-gradient(to bottom,#3EAEB1,#61BACA);"></span>
                <p class="text-[11px] font-semibold text-primary uppercase tracking-[0.18em]">Basis Data</p>
            </div>
            <h2 class="display-roman text-3xl md:text-4xl text-ink-900 leading-tight">
                Obat yang populer
                <span class="display-italic text-primary">hari ini</span>
            </h2>
            <p class="text-sm text-ink-500 mt-2 max-w-md">
                {{ !empty($fdaMode) ? 'Dikurasi dari registri OpenFDA yang terbuka dan dipercaya klinisi.' : 'Sebagian kecil dari pustaka terverifikasi kami — paling banyak dicari minggu ini.' }}
            </p>
        </div>
        <a href="{{ route('drugs.search') }}" class="glass-soft rounded-full pl-5 pr-4 py-2.5 text-sm font-medium text-primary-dark hover:text-primary group inline-flex items-center gap-2 transition-colors">
            Lihat semua
            <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($featured as $drug)
        @php
        $isDto = $drug instanceof \App\DTOs\DrugDTO;
        $uses = $isDto ? $drug->uses : \App\Models\Drug::cleanField($drug->uses);
        $preview = $uses ? Str::limit($uses, 85) : 'Ketuk untuk mempelajari obat ini lebih lanjut.';
        $href = $isDto ? route('drugs.show_fda', $drug->slug) : route('drugs.show', $drug->id);
        $initials = strtoupper(substr($drug->name ?? '?', 0, 2));
        @endphp
        <a href="{{ $href }}" class="drug-card glass-card glass-hover group block rounded-2xl p-5">
            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0"
                    style="background: linear-gradient(135deg,rgba(62,174,177,0.18),rgba(97,186,202,0.30));">
                    <span class="font-semibold text-primary-dark text-sm leading-none tracking-tight">{{ $initials }}</span>
                </div>
                @if($drug->drug_class)
                <span class="text-[10px] font-medium px-2.5 py-1 rounded-full max-w-[120px] truncate"
                    style="background:rgba(62,174,177,0.08); color:#2d8a8d; border:1px solid rgba(62,174,177,0.15);">{{ Str::limit($drug->drug_class, 18) }}</span>
                @endif
            </div>
            <h3 class="font-medium text-ink-900 text-sm leading-snug mb-1 group-hover:text-primary transition-colors line-clamp-1 tracking-tight">
                {{ $drug->name }}</h3>
            @if($drug->generic_name)
            <p class="display-italic text-xs text-ink-400 mb-2.5 truncate">{{ $drug->generic_name }}</p>
            @endif
            <p class="text-xs text-ink-500 leading-relaxed line-clamp-3">{{ $preview }}</p>
            <div class="flex items-center justify-between mt-4 pt-3" style="border-top:1px solid rgba(62,174,177,0.10);">
                @if(!empty($drug->is_fda))
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(59,130,246,0.10);color:#3b82f6;">FDA</span>
                @elseif(!empty($drug->translated))
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full" style="background:rgba(16,185,129,0.10);color:#059669;">✓ Terverifikasi</span>
                @else
                <span class="text-xs text-ink-300">—</span>
                @endif
                <span class="text-xs font-medium text-primary group-hover:translate-x-0.5 transition-transform inline-block">Detail →</span>
            </div>
        </a>
        @endforeach
    </div>

</section>

{{-- ════════════════════════════════════════ MENGAPA CEK SEHAT ════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="text-center mb-14">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-[11px] font-semibold mb-5 glass-soft text-primary-dark uppercase tracking-[0.18em]">
            Mengapa Cek Sehat
        </div>
        <h2 class="display-roman text-3xl md:text-4xl lg:text-5xl text-ink-900 leading-tight max-w-3xl mx-auto">
            Semua yang perlu Anda ketahui
            <span class="display-italic text-primary">tentang obat Anda.</span>
        </h2>
        <p class="text-sm md:text-base text-ink-500 mt-4 max-w-xl mx-auto leading-relaxed">
            Dipercaya banyak orang. Didukung AI. Dibuat agar jelas dan mudah dipahami.
        </p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach([
        ['icon'=>'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z','title'=>'Pencarian Cerdas','desc'=>(!empty($dbOffline) || !empty($fdaMode))
            ? 'Cari di antara 67.000+ obat lewat OpenFDA berdasarkan nama dagang, generik, atau golongan dalam hitungan detik.'
            : 'Cari di antara 16.000+ obat berdasarkan nama dagang, generik, atau golongan dalam hitungan detik.','accent'=>'#3EAEB1'],
        ['icon'=>'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z','title'=>'Bahasa yang Mudah','desc'=>'Teks medis yang rumit disederhanakan AI menjadi bahasa sehari-hari yang benar-benar bisa Anda pahami.','accent'=>'#6366f1'],
        ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','title'=>'Utamakan Keamanan','desc'=>'Peringatan, interaksi obat, dan daftar periksa sebelum minum obat, agar Anda bisa mengambil keputusan yang tepat.','accent'=>'#059669'],
        ] as $f)
        <div class="drug-card glass-card glass-hover group rounded-2xl p-7 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-28 h-28 rounded-full opacity-[0.10] pointer-events-none"
                style="background: radial-gradient(circle, {{ $f['accent'] }}, transparent 70%); transform: translate(30%,-30%);"></div>
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-5 transition-transform group-hover:scale-105 duration-500"
                style="background: linear-gradient(135deg, {{ $f['accent'] }}22, {{ $f['accent'] }}38); border:1px solid {{ $f['accent'] }}25;">
                <svg class="w-5 h-5" style="color:{{ $f['accent'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}" />
                </svg>
            </div>
            <h3 class="display-roman text-ink-900 mb-2 text-2xl leading-tight">{{ $f['title'] }}</h3>
            <p class="text-sm text-ink-500 leading-relaxed">{{ $f['desc'] }}</p>
            <div class="mt-6 pt-4" style="border-top:1px solid rgba(62,174,177,0.10);"></div>
        </div>
        @endforeach
    </div>
    <div class="max-w-7xl mx-auto mt-5">

        {{-- Bento grid: kiri besar (gelap) + kolom kanan --}}
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

            {{-- ── KIRI: Kartu pipeline gelap ── --}}
            <div class="lg:col-span-3 rounded-3xl p-6 md:p-8 relative overflow-hidden flex flex-col justify-between min-h-[420px]"
                style="background:linear-gradient(145deg,#061e20 0%,#0d4f52 55%,#1a7a7d 100%); border:1px solid">
                <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full pointer-events-none"
                    style="background:radial-gradient(circle,rgba(62,174,177,0.25),transparent 70%);"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 rounded-full pointer-events-none"
                    style="background:radial-gradient(circle,rgba(97,186,202,0.15),transparent 70%); transform:translate(-30%,30%);"></div>

                <div class="relative mb-8">
                    <p class="text-teal-300/60 text-[11px] font-bold uppercase tracking-widest mb-2">Screening bertahap · disesuaikan dengan peran</p>
                    <h2 class="text-white text-2xl md:text-3xl font-heading leading-snug">
                        Dari keluhan<br class="hidden sm:block">menjadi kesimpulan yang tepat sasaran.
                    </h2>
                </div>

                <div class="relative space-y-0">
                    <div class="absolute left-[15px] top-2 bottom-2 w-px" style="background:linear-gradient(to bottom,rgba(62,174,177,0.5),rgba(62,174,177,0.05));"></div>

                    @foreach([
                        ['01','Ceritakan keluhan awal','Ketik atau bicara · bahasa apa saja · mulai dari satu kalimat'],
                        ['02','AI menyusun pertanyaan screening','3–5 pertanyaan relevan · fokus pada konteks masalah Anda'],
                        ['03','Jawab dengan cepat','Pilihan cepat (chip) · atau tulis detail Anda sendiri'],
                        ['04','Tentukan peran Anda','Tenaga kesehatan/dokter · atau pasien/masyarakat umum'],
                        ['05','Kesimpulan untuk dokter','Diagnosis banding · regimen obat · interaksi · penatalaksanaan · ICD-10'],
                        ['06','Kesimpulan untuk pasien','Penjelasan menenangkan · penanganan awal · pola hidup · arahan lanjutan'],
                        ['07','Arahan ke fasilitas kesehatan','Dokter yang tepat · apa yang perlu ditanyakan · faskes terdekat'],
                        ['08','Simpan & bagikan','Salin laporan · ekspor PDF · bagikan hasil'],
                    ] as [$n, $label, $detail])
                    <div class="flex items-start gap-4 py-2.5 group">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 z-10 transition-all duration-200 group-hover:scale-110"
                            style="background:rgba(62,174,177,0.18); border:1px solid rgba(62,174,177,0.30);">
                            <span class="text-[10px] font-black text-teal-300">{{ $n }}</span>
                        </div>
                        <div class="pt-1 min-w-0">
                            <p class="text-sm font-semibold text-white/90 leading-tight truncate">{{ $label }}</p>
                            <p class="text-[10px] text-teal-300/50 mt-0.5 truncate">{{ $detail }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ── KANAN: Dua kartu mini bertumpuk ── --}}
            <div class="lg:col-span-2 flex flex-col gap-4">

                <div class="rounded-3xl p-5 md:p-7 flex-1 relative overflow-hidden"
                    style="background:rgba(255,255,255,0.88); backdrop-filter:blur(24px); border:1px solid rgba(255,255,255,0.96); box-shadow:0 4px 24px rgba(62,174,177,0.10), 0 1px 0 rgba(255,255,255,0.9) inset;">
                    <div class="absolute top-0 right-0 w-32 h-32 pointer-events-none"
                        style="background:radial-gradient(circle at top right,rgba(62,174,177,0.12),transparent 70%);"></div>
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center mb-5"
                        style="background:linear-gradient(135deg,rgba(62,174,177,0.15),rgba(97,186,202,0.25)); border:1px solid rgba(62,174,177,0.15);">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 1a3 3 0 00-3 3v8a3 3 0 006 0V4a3 3 0 00-3-3z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 10v2a7 7 0 01-14 0v-2"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1.5">Input Suara & Teks</h3>
                    <p class="text-sm text-slate-500 leading-relaxed mb-4">Ceritakan gejala dengan bicara atau mengetik — dalam Bahasa Indonesia maupun Inggris. Didukung Whisper AI untuk transkripsi multibahasa.</p>
                    <div class="flex gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-2.5 py-1 rounded-full" style="background:rgba(62,174,177,0.10);color:#2d8a8d;">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>Suara
                        </span>
                        <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full" style="background:rgba(62,174,177,0.10);color:#2d8a8d;">Teks</span>
                        <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full" style="background:rgba(62,174,177,0.10);color:#2d8a8d;">ID / EN</span>
                        <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full" style="background:rgba(62,174,177,0.10);color:#2d8a8d;">Whisper AI</span>
                    </div>
                </div>

                <div class="rounded-3xl p-5 md:p-7 flex-1 relative overflow-hidden"
                    style="background:rgba(255,255,255,0.88); backdrop-filter:blur(24px); border:1px solid rgba(255,255,255,0.96); box-shadow:0 4px 24px rgba(62,174,177,0.10), 0 1px 0 rgba(255,255,255,0.9) inset;">
                    <div class="absolute top-0 right-0 w-32 h-32 pointer-events-none"
                        style="background:radial-gradient(circle at top right,rgba(5,150,105,0.08),transparent 70%);"></div>
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center mb-5"
                        style="background:linear-gradient(135deg,rgba(5,150,105,0.12),rgba(5,150,105,0.22)); border:1px solid rgba(5,150,105,0.15);">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1.5">Yang Anda Dapatkan</h3>
                    <ul class="space-y-1.5 text-sm text-slate-500">
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-emerald-400 flex-shrink-0"></span>Pertanyaan screening yang relevan</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-emerald-400 flex-shrink-0"></span>Kesimpulan yang menyesuaikan peran Anda</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-emerald-400 flex-shrink-0"></span>Mode dokter: diagnosis banding, regimen & interaksi</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-emerald-400 flex-shrink-0"></span>Mode pasien: penanganan awal & pola hidup</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-emerald-400 flex-shrink-0"></span>Arahan dokter yang tepat & faskes terdekat</li>
                    </ul>
                </div>

            </div>
        </div>

        <p class="text-center text-[11px] text-slate-400 mt-6">
            <svg class="w-3 h-3 inline mr-1 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            Hanya untuk tujuan edukasi. Selalu konsultasikan dengan tenaga kesehatan berlisensi sebelum mengonsumsi obat apa pun.
        </p>

    </div>
</section>

{{-- ════════════════════════════════════════ CTA STRIP ════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 py-12 lg:py-16">
    <div class="relative overflow-hidden rounded-3xl px-6 py-12 md:px-14 md:py-16 text-center"
        style="background: linear-gradient(135deg, rgba(13,79,82,0.96) 0%, rgba(26,122,125,0.94) 50%, rgba(62,174,177,0.92) 100%);">
        <div class="absolute -top-20 -right-20 w-72 h-72 rounded-full opacity-30 pointer-events-none"
            style="background: radial-gradient(circle, rgba(159,216,225,0.6), transparent 70%);"></div>
        <div class="absolute -bottom-24 -left-16 w-80 h-80 rounded-full opacity-25 pointer-events-none"
            style="background: radial-gradient(circle, rgba(97,186,202,0.6), transparent 70%);"></div>

        <div class="relative max-w-2xl mx-auto">
            <p class="display-italic text-teal-200/80 text-lg mb-3">— mulai dari sebuah pertanyaan</p>
            <h2 class="display-roman text-white text-3xl md:text-5xl leading-tight mb-5">
                Kenali <span class="display-italic">obat Anda</span>
                sebelum meminumnya.
            </h2>
            <p class="text-sm md:text-base text-white/70 leading-relaxed mb-8 max-w-lg mx-auto">
                Cari obat, periksa interaksi, atau ceritakan yang Anda rasakan — Cek Sehat mengubah istilah medis menjadi jawaban yang jelas.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('drugs.search') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold transition-all"
                    style="background: rgba(255,255,255,0.96); color:#0d4f52; box-shadow: 0 6px 24px rgba(0,0,0,0.15);"
                    onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 10px 30px rgba(0,0,0,0.22)';"
                    onmouseout="this.style.transform='';this.style.boxShadow='0 6px 24px rgba(0,0,0,0.15)';">
                    Telusuri Obat
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
                <a href="{{ route('interactions.index') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-sm font-semibold text-white transition-all"
                    style="background: rgba(255,255,255,0.10); border:1px solid rgba(255,255,255,0.25); backdrop-filter: blur(10px);"
                    onmouseover="this.style.background='rgba(255,255,255,0.18)';"
                    onmouseout="this.style.background='rgba(255,255,255,0.10)';">
                    Periksa Interaksi
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
@include('partials.hero-medicheck-id-scripts')
@endpush
