<?php

namespace App\Observers;

use App\Models\LogAudit;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->recordLog($model, 'created', null, $model->getAttributes());
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $changes = $model->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $oldValues = [];
        $original = $model->getOriginal();

        foreach (array_keys($changes) as $key) {
            $oldValues[$key] = $original[$key] ?? null;
        }

        $this->recordLog($model, 'updated', $oldValues, $changes);
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->recordLog($model, 'deleted', $model->getAttributes(), null);
    }

    /**
     * Helper to write LogAudit record.
     */
    protected function recordLog(Model $model, string $action, ?array $oldValues, ?array $newValues): void
    {
        $userId = auth('sanctum')->user()?->id ?? auth()->id();
        $request = request();

        LogAudit::create([
            'id_user' => $userId,
            'model_type' => $model->getMorphClass(),
            'model_id' => $model->getKey(),
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent() ? substr($request->userAgent(), 0, 255) : null,
        ]);
    }
}
