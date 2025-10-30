<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class QueryFilters
{
    public static function apply(Builder $query, array $params = [], array $searchable = [], array $orderable = [], string $defaultOrderBy = 'id', string $defaultOrderDir = 'asc'): Builder
    {
        $search = trim((string)($params['search'] ?? ''));
        if ($search !== '' && !empty($searchable)) {
            $query->where(function (Builder $q) use ($search, $searchable) {
                foreach ($searchable as $field) {
                    $q->orWhere($field, 'ilike', "%{$search}%");
                }
            });
        }

        $orderBy = $params['order_by'] ?? $defaultOrderBy;
        $orderDir = strtolower((string)($params['order_dir'] ?? $defaultOrderDir));
        if (!in_array($orderDir, ['asc', 'desc'], true)) {
            $orderDir = $defaultOrderDir;
        }
        if ($orderBy && (empty($orderable) || in_array($orderBy, $orderable, true))) {
            $query->orderBy($orderBy, $orderDir);
        } else {
            $query->orderBy($defaultOrderBy, $defaultOrderDir);
        }

        return $query;
    }
}
