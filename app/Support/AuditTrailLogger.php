<?php

namespace App\Support;

use App\Models\AuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditTrailLogger
{
    public static function log(string $module, string $action, Model $model, array $meta = []): void
    {
        try {
            AuditTrail::create([
                'user_id' => Auth::id(),
                'module' => $module,
                'action' => $action,
                'auditable_type' => $model::class,
                'auditable_id' => $model->getKey(),
                'meta' => $meta,
            ]);
        } catch (\Throwable) {
            // Never block business flow for audit write failures.
        }
    }
}
