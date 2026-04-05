<?php

namespace App\Models;

use Illuminate\Support\Str;

abstract class Model extends \Illuminate\Database\Eloquent\Model {
    public $incrementing = false;
    protected $keyType   = 'string';

    public static function boot(): void {
        parent::boot();
        self::unguard();
    }

    public static function booted(): void {
        parent::booted();

        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function saveAndReturn(): static {
        $this->save();
        $this->refresh();

        return $this;
    }
}
