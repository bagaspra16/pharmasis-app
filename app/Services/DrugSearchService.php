<?php

namespace App\Services;

use App\Models\Drug;
use Illuminate\Pagination\LengthAwarePaginator;

class DrugSearchService
{
    /**
     * Search drugs using pure ILIKE (pg_trgm not available on this server).
     * Priority: name starts-with > name contains > generic contains > class contains > uses contains.
     */
    public function search(string $query, array $filters = [], int $perPage = 18): LengthAwarePaginator
    {
        $q = Drug::query();

        $term = trim($query);

        if ($term !== '') {
            $q->where(function ($sub) use ($term) {
                $sub->whereRaw('name ILIKE ?', ["%{$term}%"])
                    ->orWhereRaw('generic_name ILIKE ?', ["%{$term}%"])
                    ->orWhereRaw('drug_class ILIKE ?', ["%{$term}%"]);
            });

            // Rank: exact/prefix matches first, then partial, then default alpha
            $q->orderByRaw("
                CASE
                    WHEN LOWER(name) = LOWER(?) THEN 0
                    WHEN name ILIKE ? THEN 1
                    WHEN generic_name ILIKE ? THEN 2
                    ELSE 3
                END,
                name ASC
            ", [$term, "{$term}%", "{$term}%"]);
        }
        else {
            $q->orderBy('name');
        }

        // Filters
        if (!empty($filters['drug_class'])) {
            $q->whereRaw('TRIM(drug_class) = TRIM(?)', [$filters['drug_class']]);
        }

        if (!empty($filters['alpha'])) {
            $q->whereRaw('LOWER(alpha_index) = LOWER(?)', [trim($filters['alpha'])]);
        }

        if (!empty($filters['translated'])) {
            $q->where('translated', true);
        }

        // Override sort
        if (!empty($filters['order'])) {
            if ($filters['order'] === 'alpha') {
                $q->reorder()->orderBy('name');
            }
            elseif ($filters['order'] === 'newest') {
                $q->reorder()->orderByDesc('updated_at')->orderBy('name');
            }
        }

        return $q->select([
            'id', 'name', 'generic_name', 'drug_class',
            'alpha_index', 'source', 'translated', 'uses',
        ])->paginate($perPage)->withQueryString();
    }

    /**
     * Instant search — top 8 results for navbar/hero dropdown.
     */
    public function instant(string $query): array
    {
        $term = trim($query);
        if (strlen($term) < 2)
            return [];

        return Drug::select(['id', 'name', 'generic_name', 'drug_class', 'alpha_index'])
            ->where(function ($sub) use ($term) {
            $sub->whereRaw('name ILIKE ?', ["%{$term}%"])
                ->orWhereRaw('generic_name ILIKE ?', ["%{$term}%"])
                ->orWhereRaw('drug_class ILIKE ?', ["%{$term}%"]);
        })
            ->orderByRaw("
                CASE
                    WHEN LOWER(name) = LOWER(?) THEN 0
                    WHEN name ILIKE ? THEN 1
                    ELSE 2
                END, name ASC
            ", [$term, "{$term}%"])
            ->limit(8)
            ->get()
            ->map(fn($d) => [
        'id' => $d->id,
        'name' => $d->name,
        'generic_name' => $d->generic_name,
        'drug_class' => $d->drug_class,
        'alpha_index' => $d->alpha_index,
        ])
            ->toArray();
    }

    /**
     * All distinct, non-blank drug classes (trimmed).
     */
    public function getClasses(): array
    {
        return Drug::selectRaw('TRIM(drug_class) as drug_class')
            ->whereNotNull('drug_class')
            ->whereRaw("TRIM(drug_class) != ''")
            ->groupByRaw('TRIM(drug_class)')
            ->orderByRaw('TRIM(drug_class)')
            ->pluck('drug_class')
            ->toArray();
    }

    /**
     * Featured/popular drugs for homepage (have uses data).
     */
    public function getFeatured(int $limit = 8): \Illuminate\Database\Eloquent\Collection
    {
        return Drug::select(['id', 'name', 'generic_name', 'drug_class', 'alpha_index', 'uses'])
            ->whereNotNull('uses')
            ->whereNotNull('name')
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * All distinct alpha_index values.
     */
    public function getAlphaIndex(): array
    {
        return Drug::select('alpha_index')
            ->whereNotNull('alpha_index')
            ->whereRaw("TRIM(alpha_index) != ''")
            ->distinct()
            ->orderBy('alpha_index')
            ->pluck('alpha_index')
            ->toArray();
    }
}