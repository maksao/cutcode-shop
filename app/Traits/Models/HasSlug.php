<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Model;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::created(function (Model $model) {
            $model->slug = $model->slug ??
                str($model->{self::slugFrom()})
                    ->append('-' . $model->id)
                    ->slug();
            $model->save();
        });
    }

    public static function slugFrom(): string
    {
        return 'title';
    }
}