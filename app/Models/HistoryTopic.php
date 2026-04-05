<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

final class HistoryTopic extends Model {
    public function era(): BelongsTo {
        return $this->belongsTo(HistoryEra::class, 'history_era_id');
    }

    public function getImageUrlAttribute(): ?string {
        if (! $this->image) {
            return null;
        }

        return Storage::url($this->image);
    }
}
