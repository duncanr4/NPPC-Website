<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Str;

class ClaudeSession extends BaseModel {
    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'prompt',
        'status',
        'branch_name',
        'worktree_path',
        'output',
        'diff',
        'files_changed',
        'created_by',
        'merged_at',
        'discarded_at',
    ];

    protected $casts = [
        'files_changed' => 'array',
        'merged_at'     => 'datetime',
        'discarded_at'  => 'datetime',
    ];

    protected static function booted(): void {
        static::creating(function ($model) {
            $model->id = (string) Str::uuid();
        });
    }

    public function isPending(): bool {
        return $this->status === 'pending';
    }

    public function isRunning(): bool {
        return $this->status === 'running';
    }

    public function isCompleted(): bool {
        return $this->status === 'completed';
    }

    public function isFailed(): bool {
        return $this->status === 'failed';
    }

    public function isMerged(): bool {
        return $this->merged_at !== null;
    }

    public function isDiscarded(): bool {
        return $this->discarded_at !== null;
    }

    public function isActive(): bool {
        return ! $this->isMerged() && ! $this->isDiscarded() && $this->isCompleted();
    }

    public function getStatusColorAttribute(): string {
        return match ($this->status) {
            'pending'   => 'gray',
            'running'   => 'warning',
            'completed' => $this->isMerged() ? 'success' : ($this->isDiscarded() ? 'danger' : 'info'),
            'failed'    => 'danger',
            default     => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string {
        if ($this->isMerged()) {
            return 'Merged';
        }
        if ($this->isDiscarded()) {
            return 'Discarded';
        }

        return ucfirst($this->status);
    }
}
