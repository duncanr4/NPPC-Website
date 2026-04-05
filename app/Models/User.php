<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property bool   $is_admin
 * @property string $site_id
 * @property Site   $site
 */
final class User extends Authenticatable implements FilamentUser {
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function booted(): void {
        parent::booted();

        self::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function canAccessPanel(Panel $panel): bool {
        return $this->is_admin;
    }

    public static function isAdmin(): bool {
        return self::me()?->is_admin ?? false;
    }

    public static function getCurrentSite(): ?Site {
        return self::me()?->site;
    }

    public static function getSiteId(): ?string {
        return self::me()?->site_id;
    }

    public static function me(): ?self {
        return Auth::user();
    }

    public function site(): BelongsTo {
        return $this->belongsTo(Site::class);
    }
}
