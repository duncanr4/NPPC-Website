<?php

namespace App\Nova\Resources;

use App\Models\Staff as StaffModel;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class StaffResource extends Resource {
    public static $model = StaffModel::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name',
    ];

    public function fields(Request $request) {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Position'),

            Image::make('Image')
                ->disk('public')
                ->path('staff/images')
                ->prunable(),

            Textarea::make('About')
                ->alwaysShow(),

            Select::make('Group')->options(['staff' => 'Staff', 'board' => 'Board']),
            Boolean::make('published'),
        ];
    }

    public static function label() {
        return 'Staff';
    }

    public static function singularLabel() {
        return 'Staff Member';
    }
}
