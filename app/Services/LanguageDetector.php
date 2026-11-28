<?php

namespace App\Services;

/**
 * Deterministic conversation-language detector for user symptom/medical text.
 *
 * Separates UI chrome language from the language of the submitted medical text
 * so bilingual users get responses in the language they actually wrote in.
 */
class LanguageDetector
{
    public const SUPPORTED = ['en', 'id', 'ja', 'es', 'zh', 'ar', 'fr', 'de', 'ko', 'pt', 'ru'];

    /**
     * Resolve the target response language.
     *
     * @param  string      $requestedLang  Explicit language code or 'auto'
     * @param  string      $text           User-supplied text
     * @param  string|null $uiLang         Optional UI language fallback when text is neutral (e.g. a drug name)
     */
    public static function resolve(string $requestedLang, string $text, ?string $uiLang = null): string
    {
        $requestedLang = strtolower(trim($requestedLang));
        if ($requestedLang !== 'auto' && in_array($requestedLang, self::SUPPORTED, true)) {
            return $requestedLang;
        }

        $trimmed = trim($text);
        if ($trimmed !== '') {
            // 1. Unambiguous non-Latin scripts
            if (preg_match('/[\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $trimmed)) {
                return 'ja';
            }
            if (preg_match('/[\x{AC00}-\x{D7AF}\x{1100}-\x{11FF}]/u', $trimmed)) {
                return 'ko';
            }
            if (preg_match('/[\x{0600}-\x{06FF}]/u', $trimmed)) {
                return 'ar';
            }
            if (preg_match('/[\x{0400}-\x{04FF}]/u', $trimmed)) {
                return 'ru';
            }
            if (preg_match('/[\x{4E00}-\x{9FFF}]/u', $trimmed)) {
                return 'zh';
            }

            $padded = ' ' . preg_replace('/[^\p{L}\p{N}]+/u', ' ', mb_strtolower($trimmed)) . ' ';

            $idHits = self::countHits($padded, [
                'saya', 'aku', 'kamu', 'anda', 'yang', 'dan', 'atau', 'tidak', 'nggak', 'gak',
                'dengan', 'untuk', 'dari', 'ke', 'di', 'sakit', 'demam', 'batuk', 'pilek',
                'sejak', 'nyeri', 'hari', 'minggu', 'bulan', 'terasa', 'rasanya', 'sudah', 'udah',
                'masih', 'kepala', 'perut', 'badan', 'tenggorokan', 'dada', 'mual', 'muntah',
                'diare', 'pusing', 'sesak', 'lemas', 'gatal', 'ruam', 'bengkak', 'minum',
                'obat', 'dosis', 'efek', 'dokter', 'tolong', 'bantu', 'ada', 'juga', 'kenapa',
                'apa', 'apakah', 'berapa', 'bagaimana', 'kondisi', 'gejala', 'anak', 'keluarga',
                'meriang', 'kering', 'berdahak', 'aman', 'bahaya', 'berbahaya', 'bisa',
            ]);

            $enHits = self::countHits($padded, [
                'i', 'my', 'me', 'have', 'has', 'had', 'am', 'is', 'are', 'was', 'were',
                'the', 'a', 'an', 'and', 'or', 'not', 'with', 'for', 'from', 'since',
                'pain', 'ache', 'fever', 'cough', 'headache', 'nausea', 'dizzy', 'dizziness',
                'chest', 'stomach', 'throat', 'breathing', 'shortness', 'feel', 'feeling',
                'taking', 'medicine', 'medication', 'drug', 'dose', 'dosage', 'side', 'effects',
                'what', 'why', 'how', 'when', 'can', 'could', 'should', 'days', 'weeks',
                'yesterday', 'today', 'morning', 'doctor', 'symptoms', 'rash', 'swelling',
            ]);

            if ($idHits > 0 && $idHits >= $enHits) {
                return 'id';
            }
            if ($enHits > 0 && $enHits > $idHits) {
                return 'en';
            }

            // 3. Distinctive Latin-script languages
            $esHits = self::countHits($padded, ['tengo', 'dolor', 'fiebre', 'desde', 'qué', 'como', 'para', 'con', 'cabeza', 'garganta', 'estómago', 'dosis', 'medicamento']);
            if ($esHits >= 2 || preg_match('/[ñ¿¡]/u', $trimmed)) {
                return 'es';
            }

            $frHits = self::countHits($padded, ['jai', 'douleur', 'fièvre', 'toux', 'depuis', 'avec', 'pour', 'tête', 'gorge', 'médicament', 'quels', 'comment']);
            if ($frHits >= 2 || str_contains(mb_strtolower($trimmed), "j'ai")) {
                return 'fr';
            }

            $deHits = self::countHits($padded, ['ich', 'habe', 'schmerzen', 'fieber', 'husten', 'seit', 'kopfschmerzen', 'halsschmerzen', 'medikament', 'warum', 'wie']);
            if ($deHits >= 2 || preg_match('/[ßäöü]/u', mb_strtolower($trimmed))) {
                return 'de';
            }

            $ptHits = self::countHits($padded, ['estou', 'tenho', 'febre', 'tosse', 'cabeça', 'garganta', 'desde', 'remédio', 'posso', 'qual']);
            if ($ptHits >= 2 || preg_match('/[ãõç]/u', mb_strtolower($trimmed))) {
                return 'pt';
            }
        }

        if ($uiLang !== null) {
            $uiLang = strtolower(trim($uiLang));
            if (in_array($uiLang, self::SUPPORTED, true)) {
                return $uiLang;
            }
        }

        return 'en';
    }

    private static function countHits(string $padded, array $words): int
    {
        $hits = 0;
        foreach ($words as $word) {
            if (str_contains($padded, " {$word} ")) {
                $hits++;
            }
        }

        return $hits;
    }
}
