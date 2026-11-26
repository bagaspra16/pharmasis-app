<?php

return [
    'title' => 'Bukti dan referensi',
    'ai_summary' => 'Ringkasan buatan AI',
    'status' => [
        'verified' => 'Setiap pernyataan dipasangkan dengan kutipan sumber. Ini bukan verifikasi medis: baca sumbernya.',
        'partial' => 'Hanya sebagian pernyataan punya kutipan sumber yang cocok. Anggap sisanya belum terverifikasi.',
        'conflicting' => 'Sebagian pernyataan bertentangan dengan sumber. Konfirmasikan ke dokter atau apoteker.',
        'insufficient' => 'Bukti tidak cukup. Anggap jawaban ini belum terverifikasi.',
        'unavailable' => 'Sumber tidak dapat diperiksa. Anggap jawaban ini belum terverifikasi.',
    ],
    'claim' => [
        'supported' => 'Kutipan cocok ditemukan',
        'unsupported' => 'Belum terverifikasi',
        'conflicting' => 'Sumber berbeda pendapat',
        'contradicted' => 'Dibantah sumber',
        'unchecked' => 'Tidak diperiksa',
    ],
    'level' => [
        'established' => 'Dari sumber resmi atau internasional',
        'research' => 'Tinjauan penelitian, belum tentu pasti',
        'outdated' => 'Sumber mungkin usang',
    ],
    'tier' => [1 => 'Otoritas pemerintah', 2 => 'Organisasi kesehatan internasional', 3 => 'Tinjauan sistematis'],
    'date_kind' => ['effective' => 'Berlaku', 'updated' => 'Diperbarui', 'published' => 'Terbit'],
    'no_date' => 'Tanggal tidak dicantumkan',
    'stale' => 'Mungkin usang',
    'critical' => 'Terkait keselamatan',
    'against' => 'Sumber menyatakan lain',
    'count' => 'pernyataan punya kutipan sumber yang cocok',
    'critical_warning' => 'pernyataan terkait keselamatan belum terverifikasi. Konfirmasikan ke dokter atau apoteker sebelum bertindak.',
    'show' => 'Lihat tiap pernyataan',
    'hide' => 'Sembunyikan pernyataan',
    'references' => 'Referensi',
    'checked_on' => 'Diperiksa pada',
    'method' => 'Pemeriksaan otomatis terhadap kutipan sumber. Bisa keliru; buka sumber untuk memastikan.',
];
