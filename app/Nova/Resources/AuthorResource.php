<?php

namespace App\Nova\Resources;

use App\Models\Author;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;

class AuthorResource extends Resource {
    public static $model = Author::class;
    public static $title = 'name';
    public static $group = 'Content';

    public function fields(NovaRequest $request): array {
        return [
            Text::make('Name'),
            Textarea::make('About'),
            Image::make('Avatar'),
        ];
    }
}
