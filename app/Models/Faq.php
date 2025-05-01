<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

/**
 * Class Faq.
 *
 * @property string $question
 * @property string $answer
 * @property string $type
 */
final class Faq extends Model implements Sortable {
    use SortableTrait;

    public $sortable = [
        'order_column_name'  => 'sort_order',
        'sort_when_creating' => true,
    ];
    protected $fillable = ['question', 'answer', 'type'];

    /**
     * @return Collection<Faq>|Faq[]
     */
    public static function getFaqsByType(string $type): Collection {
        return self::where('type', $type)->orderBy('sort_order', 'ASC')->get();
    }
}
