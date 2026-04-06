<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Str;

class FormSubmission extends BaseModel {
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'form_type',
        'data',
        'status',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected static function booted(): void {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function getNameAttribute(): ?string {
        $data = $this->data;
        return $data['name'] ?? ($data['first_name'] ?? '').($data['last_name'] ? ' '.($data['last_name'] ?? '') : '') ?: null;
    }

    public function getEmailAttribute(): ?string {
        return $this->data['email'] ?? null;
    }

    public function isNew(): bool {
        return $this->status === 'new';
    }

    public function isRead(): bool {
        return $this->status === 'read';
    }

    public function isArchived(): bool {
        return $this->status === 'archived';
    }
}
