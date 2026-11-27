<?php

return [
    'heading' => 'Penilaian tingkat kegawatan',
    'not_diagnosis' => 'Ini menunjukkan seberapa cepat Anda perlu mencari pertolongan. Ini bukan diagnosis dan bisa keliru. Jika memburuk atau Anda khawatir, cari pertolongan medis.',
    'why' => 'Tanda bahaya dari cerita Anda',
    'steps' => 'Yang perlu dilakukan sekarang',
    'unknown' => 'Yang tidak bisa dinilai',
    'basis' => 'Berdasarkan',
    'unreviewed' => 'MODE UJI: aturan ini belum ditinjau oleh tenaga medis.',
    'call_button' => 'Telepon',
    'title' => [
        'emergency' => 'Gawat darurat',
        'urgent' => 'Segera periksa',
        'routine' => 'Tidak mendesak',
        'unable_to_assess' => 'Belum bisa dinilai',
    ],
    'meaning' => [
        'emergency' => 'Anda mungkin memerlukan pertolongan gawat darurat sekarang.',
        'urgent' => 'Anda perlu diperiksa tenaga kesehatan segera, sebaiknya hari ini.',
        'routine' => 'Tidak ada tanda bahaya yang dilaporkan. Konsultasi biasa atau perawatan mandiri mungkin cukup.',
        'unable_to_assess' => 'Informasi belum cukup untuk menilai kegawatan. Ini bukan berarti Anda aman.',
    ],
    'next' => [
        'emergency' => [
            'Jangan menyetir sendiri. Minta seseorang menemani Anda.',
            'Jangan menunggu saran lain dari aplikasi ini.',
        ],
        'urgent' => [
            'Hubungi dokter atau klinik hari ini.',
            'Jika tidak bisa diperiksa hari ini, datanglah ke IGD.',
        ],
        'routine' => [
            'Konsultasikan ke apoteker atau dokter jika tidak membaik atau lebih dari seminggu.',
            'Cari pertolongan lebih cepat jika muncul keluhan baru atau memburuk.',
        ],
        'unable_to_assess' => [
            'Periksakan ke tenaga kesehatan untuk dinilai.',
        ],
    ],
    'call_country' => 'Hubungi :number (:country) atau segera ke IGD terdekat. Jika Anda tidak berada di :country, hubungi nomor darurat setempat.',
    'call_default' => 'Hubungi nomor darurat setempat (119 di Indonesia, :number di banyak negara) atau segera ke IGD terdekat.',
    'none' => 'Tidak ada',
    'questions' => [
        'red_flags' => 'Apakah saat ini ada salah satu dari berikut?',
        'age' => 'Berapa usia orang yang mengalami keluhan?',
        'duration' => 'Sudah berapa lama keluhannya?',
        'risk' => 'Apakah ada yang sesuai?',
    ],
    'ages' => ['Di bawah 3 bulan', '3 bulan sampai 4 tahun', '5 sampai 17 tahun', '18 sampai 64 tahun', '65 tahun atau lebih'],
    'durations' => ['Kurang dari 3 hari', '3 sampai 7 hari', 'Lebih dari 7 hari'],
    'risks' => ['Hamil', 'Daya tahan tubuh lemah, diabetes atau kemoterapi', 'Penyakit jantung, paru atau ginjal', 'Tidak ada'],
    'missing' => [
        'age' => 'Usia belum diisi.',
        'duration' => 'Lama keluhan belum diisi.',
        'risk' => 'Kehamilan dan kondisi kesehatan belum dijawab.',
        'warning_signs' => 'Tanda bahaya belum semuanya dapat disingkirkan.',
        'recognised_complaint' => 'Keluhan ini tidak bisa dinilai "tidak mendesak" oleh alat ini.',
        'age_group_not_covered' => 'Alat ini tidak menilai kelompok usia ini sebagai tidak mendesak.',
        'risk_condition' => 'Ada kondisi kesehatan sehingga tidak aman dinilai tidak mendesak.',
        'language' => 'Alat ini tidak bisa menilai "tidak mendesak" dalam bahasa ini.',
    ],
];
