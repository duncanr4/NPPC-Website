<?php

namespace App\Nova\Resources;

use App\Models\Faq;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Outl1ne\NovaSortable\Traits\HasSortableRows;

class FaqResource extends Resource {
    use HasSortableRows;

    public static $model = Faq::class;
    public static $title = 'question';
    public static $group = 'Content';

    public function fields(NovaRequest $request): array {
        return [
            Text::make('Question'),
            Markdown::make('Answer'),
            Text::make('sort_order'),
            Select::make('Type')->options(['faq' => 'FAQ', 'map' => 'Map', 'donation' => 'Donation']),
        ];
    }
}
