<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * @property string      $name
 * @property int         $sort_order
 * @property string|null $photo
 * @property string|null $description
 * @property int|null    $years_in_prison
 * @property string|null $state
 * @property string|null $address
 * @property float|null  $lat
 * @property float|null  $lng
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string|null $aka
 * @property string|null $race
 * @property string|null $gender
 * @property string|null $birthdate
 * @property string|null $death_date
 * @property int|null    $age
 * @property array|null  $ideologies
 * @property string|null $era
 * @property array|null  $affiliation
 * @property bool        $in_custody
 * @property bool        $released
 * @property bool        $in_exile
 * @property bool        $currently_in_exile
 * @property bool        $imprisoned_or_exiled
 * @property string|null $website
 * @property string|null $twitter
 * @property string|null $facebook
 * @property string|null $instagram
 * @property string|null $inmate_number
 * @property bool        $awaiting_trial
 */
final class Prisoner extends Model {
    protected $casts = [
        'ideologies'          => 'array',
        'affiliation'         => 'array',
        'birthdate'           => 'date',
        'death_date'          => 'date',
        'in_custody'          => 'boolean',
        'released'            => 'boolean',
        'in_exile'            => 'boolean',
        'currently_in_exile'  => 'boolean',
        'imprisoned_or_exiled' => 'boolean',
        'awaiting_trial'      => 'boolean',
    ];

    public function cases(): HasMany {
        return $this->hasMany(PrisonerCase::class);
    }

    public function getPhotoUrlAttribute(): ?string {
        if (! $this->photo) {
            return null;
        }

        return Storage::url($this->photo);
    }
}
