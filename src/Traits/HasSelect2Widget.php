<?php

namespace Metalogico\Formello\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait HasSelect2Widget
{
    /**
     * Handle AJAX search for Select2 widgets
     * 
     * @param string $query Model class name (e.g., Category::class)
     * @param array $searchFields Fields to search in
     * @param string|null $term Search term
     * @param array $ids Specific IDs to load
     * @param string $labelField Field to use as display text
     * @param int $limit Maximum results
     * @return JsonResponse
     */
    public function select2Search(
        string $query, 
        array $searchFields, 
        ?string $term = null, 
        array $ids = [], 
        string $labelField = 'name',
        int $limit = 50
    ): JsonResponse {
        $queryBuilder = app($query)->newQuery();
        
        if ($term) {
            $queryBuilder->where(function ($q) use ($searchFields, $term) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'ILIKE', "%{$term}%");
                }
            });
        }
        
        if (!empty($ids)) {
            $queryBuilder->whereIn('id', $ids);
        }
        
        $items = $queryBuilder
            ->limit($limit)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'text' => data_get($item, $labelField), // supports nested fields like 'user.name'
            ]);
            
        return response()->json(['results' => $items]);
    }
}