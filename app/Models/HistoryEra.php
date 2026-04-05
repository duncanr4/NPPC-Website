<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

final class HistoryEra extends Model {
    public function topics(): HasMany {
        return $this->hasMany(HistoryTopic::class)->orderBy('sort_order');
    }
}
