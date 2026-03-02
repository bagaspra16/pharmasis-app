<?php

namespace App\DTOs;

use App\Models\Drug;

/**
 * DrugDTO — wraps OpenFDA API data to look like the Drug Eloquent model.
 * Views that work with Drug also work with DrugDTO transparently.
 */
class DrugDTO
{
    public readonly string  $id;
    public readonly string  $slug;
    public readonly ?string $name;
    public readonly ?string $generic_name;
    public readonly ?string $drug_class;
    public readonly ?string $alpha_index;
    public readonly ?string $uses;
    public readonly ?string $warnings;
    public readonly ?string $before_taking;
    public readonly ?string $dosage;
    public readonly ?string $side_effects;
    public readonly ?string $interactions;
    public readonly ?string $source;
    public readonly ?string $url;
    public readonly bool    $translated;
    public readonly bool    $is_fda;
    public readonly ?string $updated_at_label;

    public function __construct(array $data)
    {
        $this->id               = $data['id']            ?? 'fda-' . md5($data['name'] ?? '');
        $this->slug             = $data['slug']           ?? strtolower(preg_replace('/[^a-z0-9]+/i', '-', $data['name'] ?? ''));
        $this->name             = $data['name']           ?? null;
        $this->generic_name     = $data['generic_name']   ?? null;
        $this->drug_class       = $data['drug_class']     ?? null;
        $this->alpha_index      = $data['alpha_index']    ?? strtoupper(substr($data['name'] ?? '?', 0, 2));
        $this->uses             = $data['uses']           ?? null;
        $this->warnings         = $data['warnings']       ?? null;
        $this->before_taking    = $data['before_taking']  ?? null;
        $this->dosage           = $data['dosage']         ?? null;
        $this->side_effects     = $data['side_effects']   ?? null;
        $this->interactions     = $data['interactions']   ?? null;
        $this->source           = $data['source']         ?? 'OpenFDA';
        $this->url              = $data['url']            ?? null;
        $this->translated       = $data['translated']     ?? true;
        $this->is_fda           = $data['is_fda']         ?? true;
        $this->updated_at_label = null; // FDA data doesn't carry a specific date
    }

    // ── Accessors matching Drug model ─────────────────────────────────────────

    public function getCleanUsesAttribute(): ?string         { return $this->cleanField($this->uses); }
    public function getCleanWarningsAttribute(): ?string     { return $this->cleanField($this->warnings); }
    public function getCleanDosageAttribute(): ?string       { return $this->cleanField($this->dosage); }
    public function getCleanSideEffectsAttribute(): ?string  { return $this->cleanField($this->side_effects); }
    public function getCleanInteractionsAttribute(): ?string { return $this->cleanField($this->interactions); }

    public function getCleanBeforeTakingAttribute(): array
    {
        return Drug::cleanBeforeTaking($this->before_taking);
    }

    public function getRiskLevelAttribute(): string
    {
        $warns = strtolower($this->warnings ?? '');
        if (str_contains($warns, 'fatal') || str_contains($warns, 'death') || str_contains($warns, 'severe')) return 'major';
        if (str_contains($warns, 'serious') || str_contains($warns, 'caution') || str_contains($warns, 'avoid'))  return 'moderate';
        return 'minor';
    }

    /**
     * Magic property access calls the Accessor if it exists.
     * This lets Blade use $drug->clean_uses just like with an Eloquent model.
     */
    public function __get(string $name): mixed
    {
        $method = 'get' . str_replace('_', '', ucwords($name, '_')) . 'Attribute';
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }
        return null;
    }

    private function cleanField(?string $raw): ?string
    {
        return Drug::cleanField($raw);
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}