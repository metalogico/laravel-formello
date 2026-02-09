<?php

namespace Metalogico\Formello\Traits;

use Illuminate\Http\JsonResponse;

trait HasSelect2Widget
{
    /**
     * @deprecated Deprecated. Use HasTomSelectWidget trait instead.
     */
    /**
     * Handle AJAX search for Select2 widgets
     *
     * @param  string|\Illuminate\Database\Eloquent\Builder  $query  Model class name (e.g., Category::class) or Query Builder instance
     * @param  array  $searchFields  Fields to search in
     * @param  string|null  $term  Search term
     * @param  string  $labelField  Field to use as display text
     * @param  int  $limit  Maximum results
     */
    public function select2Search(
        $query,
        array $searchFields,
        ?string $term = null,
        string $labelField = 'name',
        string $valueField = 'id',
        int $limit = 50
    ): JsonResponse {
        throw new \RuntimeException("Deprecated: HasSelect2Widget is deprecated. Use HasTomSelectWidget trait instead.");
    }
}

