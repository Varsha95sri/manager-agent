<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        AuditLog::create([
            'action' => 'created',
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'changes' => $model->getAttributes(),
        ]);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        AuditLog::create([
            'action' => 'updated',
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'changes' => $model->getChanges(),
        ]);
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        AuditLog::create([
            'action' => 'deleted',
            'model_type' => class_basename($model),
            'model_id' => $model->id,
            'changes' => $model->getAttributes(),
        ]);
    }
}
