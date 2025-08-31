<?php

namespace Metalogico\Formello\Traits;

use Illuminate\Http\JsonResponse;

trait HasTomSelectWidget
{
    /**
     * Handle AJAX search for Tom Select widgets
     *
     * Returns a Select2-compatible payload to ease frontend adapters:
     * { "results": [ {"id": <value>, "text": <label>} ] }
     *
     * @param  string|\Illuminate\Database\Eloquent\Builder  $query  Model class name (e.g., Category::class) or Query Builder instance
     * @param  array  $searchFields  Fields to search in
     * @param  string|null  $term  Search term
     * @param  string  $labelField  Field to use as display text (supports dot notation)
     * @param  string  $valueField  Field to use as value
     * @param  int  $limit  Maximum results
     */
    public function tomSelectSearch(
        $query,
        array $searchFields,
        ?string $term = null,
        string $labelField = 'name',
        string $valueField = 'id',
        int $limit = 50
    ): JsonResponse {
        // Create new query or use existing query builder
        $queryBuilder = is_string($query) ? app($query)->newQuery() : $query;

        // Apply search term if provided
        if ($term && ! empty($searchFields)) {
            $queryBuilder->where(function ($q) use ($searchFields, $term) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$term}%");
                }
            });
        }

        // Execute query and format results
        $items = $queryBuilder
            ->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'id' => $item->$valueField,
                'text' => data_get($item, $labelField), // supports nested fields like 'user.name'
            ]);

        return response()->json(['results' => $items]);
    }
}
