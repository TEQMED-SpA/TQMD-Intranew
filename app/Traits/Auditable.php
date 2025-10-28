<?php

namespace App\Traits;

use App\Models\Audit;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            self::logChange($model, 'created');
        });

        static::updated(function ($model) {
            self::logChange($model, 'updated');
        });

        static::deleted(function ($model) {
            self::logChange($model, 'deleted');
        });
    }

    protected static function logChange($model, $action)
    {
        $before = [];
        $after = [];

        if ($action === 'updated') {
            $before = array_intersect_key($model->getOriginal(), $model->getDirty());
            $after = $model->getDirty();
        } elseif ($action === 'created') {
            $after = $model->getAttributes();
        } elseif ($action === 'deleted') {
            $before = $model->getAttributes();
        }

        Audit::create([
            'user_id' => auth()->id(),
            'entity_type' => get_class($model),
            'entity_id' => $model->id,
            'action' => $action,
            'before_changes' => !empty($before) ? json_encode($before) : null,
            'after_changes' => !empty($after) ? json_encode($after) : null,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}
