<?php

namespace App\Services;

use App\Models\LogAudit;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class LogAuditService
{
    /**
     * Get paginated audit logs with optional filters.
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = LogAudit::with('user')->orderBy('id_log', 'desc');

        if (! empty($filters['id_user'])) {
            $query->where('id_user', $filters['id_user']);
        }

        if (! empty($filters['model_type'])) {
            $query->where('model_type', $filters['model_type']);
        }

        if (! empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get audit history for a specific model instance.
     */
    public function getHistory(string $modelType, int $modelId): Collection
    {
        // Check if modelType is an alias or class name
        $types = [$modelType];
        $morphedClass = Relation::getMorphedModel($modelType);

        if ($morphedClass) {
            $types[] = $morphedClass;
        }

        return LogAudit::with('user')
            ->whereIn('model_type', array_unique($types))
            ->where('model_id', $modelId)
            ->orderBy('id_log', 'desc')
            ->get();
    }
}
