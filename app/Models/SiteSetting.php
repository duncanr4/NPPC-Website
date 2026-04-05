<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class SiteSetting extends BaseModel {
    protected $primaryKey = 'key';
    public $incrementing  = false;
    protected $keyType    = 'string';
    protected $fillable   = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string {
        return static::find($key)?->value ?? $default;
    }

    public static function set(string $key, ?string $value): void {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
