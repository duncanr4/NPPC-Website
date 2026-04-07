<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CalendarEntry extends BaseModel {
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'month', 'day', 'year', 'title', 'description', 'image', 'published',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    protected static function booted(): void {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function getImageUrlAttribute(): ?string {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function getMonthNameAttribute(): string {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    public function getMonthShortAttribute(): string {
        return date('M', mktime(0, 0, 0, $this->month, 1));
    }
}
