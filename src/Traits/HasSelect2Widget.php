<?php

namespace Metalogico\Formello\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait HasSelect2Widget
{
    /**
     * Handle AJAX search for Select2 widgets
     * 
     * @param string|\Illuminate\Database\Eloquent\Builder $query Model class name (e.g., Category::class) or Query Builder instance
     * @param array $searchFields Fields to search in
     * @param string|array|null $term Search term or array of IDs when loading specific records
     * @param array|string $ids Specific IDs to load (string if passed as query parameter)
     * @param string $labelField Field to use as display text
     * @param int $limit Maximum results
     * @return JsonResponse
     */
    public function select2Search(
        $query, 
        array $searchFields, 
        $term = null, 
        $ids = [], 
        string $labelField = 'name',
        int $limit = 50
    ): JsonResponse {
        // Handle string IDs from query parameters
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        } elseif (!is_array($ids)) {
            $ids = [];
        }

        // Create new query or use existing query builder
        $queryBuilder = is_string($query) ? app($query)->newQuery() : $query;
        
        // Apply search term if provided
        if ($term && !empty($searchFields)) {
            $queryBuilder->where(function ($q) use ($searchFields, $term) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'LIKE', "%{$term}%");
                }
            });
        }
        
        // Filter by specific IDs if provided
        if (!empty($ids)) {
            $queryBuilder->whereIn('id', $ids);
        }
        
        // Execute query and format results
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