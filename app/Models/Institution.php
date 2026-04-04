<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string      $name
 * @property string|null $city
 * @property string|null $state
 * @property string|null $security
 * @property string|null $mailing_address
 * @property string|null $physical_address
 * @property float|null  $lat
 * @property float|null  $lng
 */
final class Institution extends Model {
    public function cases(): HasMany {
        return $this->hasMany(PrisonerCase::class);
    }
}
