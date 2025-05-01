<?php

namespace App\Models;

/**
 * @property string $name
 * @property string $avatar
 * @property string $avatar_url
 */
final class Author extends Model {
    public $timestamps = false;
    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute(): ?string {
        if (! $this->avatar) {
            return null;
        }

        return '/storage/'.$this->avatar;
    }
}
