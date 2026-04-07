<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property string  $title
 * @property string  $slug
 * @property string  $body
 * @property string  $header_image
 * @property string  $image_url
 * @property ?string $parent_id
 * @property string  $url
 * @property object  $blocks
 * @property Page[]  $children
 */
final class Page extends Model {
    use HasSlug;
    protected $contentColumn = 'body';

    protected $appends = ['url', 'image_url'];
    protected $casts   = [
        'blocks'      => 'json',
        'show_in_nav' => 'boolean',
    ];

    public static function getBySlug(string $slug): ?self {
        return self::where('slug', $slug)->first();
    }

    public function getImageUrlAttribute(): string {
        return Storage::url($this->header_image);
    }

    public function parent(): BelongsTo {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getUrlAttribute(): string {
        return '/'.$this->slug;
    }
}
