<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder {
    public function run(): void {
        $categories = [
            'Publications',
            'Policy Brief',
            'News',
            'Press Releases',
            'Reports',
        ];

        foreach ($categories as $title) {
            Category::firstOrCreate(
                ['slug' => Str::slug($title)],
                ['title' => $title]
            );
        }
    }
}
