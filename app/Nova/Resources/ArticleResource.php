<?php

namespace App\Nova\Resources;

use App\Models\Article;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Markdown;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\TagsField\Tags;
use Whitecube\NovaFlexibleContent\Flexible;

final class ArticleResource extends Resource {
    public static $model = Article::class;
    public static $title = 'title';
    public static $group = 'Content';

    public function fields(NovaRequest $request): array {
        return [
            Text::make('Title'),
            //            Text::make('Slug'),
            Image::make('Image'),
            Markdown::make('Body'),
            Date::make('published_at')->sortable(),
            BelongsTo::make('Category', 'category', CategoryResource::class)->nullable(),
            BelongsTo::make('Author', 'author', AuthorResource::class)->nullable(),
            Tags::make('Tags'),

            Flexible::make('Citations', 'citations_json')
                ->addLayout('Citation', 'citation', [
                    Text::make('Title'),
                    Markdown::make('Content'),
                ]),
        ];
    }
}
