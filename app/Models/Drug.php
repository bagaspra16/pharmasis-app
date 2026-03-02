<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Drug extends Model
{
    protected $table = 'drugs';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'generic_name', 'drug_class', 'alpha_index',
        'uses', 'warnings', 'before_taking', 'dosage', 'side_effects',
        'interactions', 'source', 'url', 'translated', 'is_deleted',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'translated' => 'boolean',
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Global scope: exclude soft-deleted records
     */
    protected static function booted(): void
    {
        static::addGlobalScope('active', fn($q) => $q->where(
        fn($q2) => $q2->where('is_deleted', false)->orWhereNull('is_deleted')
        ));
    }

    // ─── Raw HTML/JSON Cleaner Helpers ────────────────────────────────────────

    /**
     * Parse a field that may be a JSON array of HTML strings, or plain text.
     * Returns clean plain-text with newlines stripped of tags.
     */
    public static function cleanField(?string $raw): ?string
    {
        if (empty($raw))
            return null;

        // Try to decode JSON array of HTML strings
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $raw = implode(' ', $decoded);
        }

        // Strip HTML tags and decode HTML entities
        $text = html_entity_decode(strip_tags($raw), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Clean up excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        return $text ?: null;
    }

    /**
     * Parse a field and return an array of meaningful paragraphs/sentences.
     * Good for displaying as bullet lists.
     */
    public static function cleanFieldAsList(?string $raw): array
    {
        $text = static::cleanField($raw);
        if (!$text)
            return [];

        // Split by common delimiters: newline, period followed by capital, semicolons
        $sentences = preg_split('/(?<=[.!?])\s+(?=[A-Z])/', $text);
        return array_filter(array_map('trim', $sentences ?: [$text]));
    }

    /**
     * Parse the before_taking field into checkable items.
     */
    public static function cleanBeforeTaking(?string $raw): array
    {
        $text = static::cleanField($raw);
        if (!$text)
            return [];

        // Split sentences into checklist items
        $parts = preg_split('/(?<=[.!?])\s+(?=[A-Z])|;\s*/', $text);
        $items = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if (strlen($part) > 10) {
                $items[] = rtrim($part, '.');
            }
        }
        return $items ?: [$text];
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getCleanUsesAttribute(): ?string
    {
        return static::cleanField($this->uses);
    }

    public function getCleanWarningsAttribute(): ?string
    {
        return static::cleanField($this->warnings);
    }

    public function getCleanBeforeTakingAttribute(): array
    {
        return static::cleanBeforeTaking($this->before_taking);
    }

    public function getCleanDosageAttribute(): ?string
    {
        return static::cleanField($this->dosage);
    }

    public function getCleanSideEffectsAttribute(): ?string
    {
        return static::cleanField($this->side_effects);
    }

    public function getCleanInteractionsAttribute(): ?string
    {
        return static::cleanField($this->interactions);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? 'Unknown Drug';
    }

    public function getAlphaLabelAttribute(): string
    {
        return strtoupper($this->alpha_index ?? substr($this->name ?? '?', 0, 1));
    }

    public function getRiskLevelAttribute(): string
    {
        $warns = strtolower($this->warnings ?? '');
        if (str_contains($warns, 'fatal') || str_contains($warns, 'death') || str_contains($warns, 'severe')) {
            return 'major';
        }
        if (str_contains($warns, 'serious') || str_contains($warns, 'caution') || str_contains($warns, 'avoid')) {
            return 'moderate';
        }
        return 'minor';
    }
}