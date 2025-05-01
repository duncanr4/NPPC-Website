<?php

namespace App\Models;

/**
 * Class Staff.
 *
 * @property string                          $id
 * @property string                          $name
 * @property string|null                     $image
 * @property string|null                     $about
 * @property string|null                     $position
 * @property string|null                     $group
 * @property bool                            $published
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Staff extends Model {
    protected $table = 'staff';

    protected $fillable = [
        'name',
        'image',
        'about',
        'position',
    ];

    public static function getStaffMembers() {
        return self::where('group', 'staff')->where('published', true)->get();
    }

    public static function getBoardMembers() {
        return self::where('group', 'board')->where('published', true)->get();
    }
}
