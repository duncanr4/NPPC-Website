<?php

namespace App\Filament\Pages;

use App\Models\Page as PageModel;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PageOrder extends Page {
    protected static ?string $navigationIcon  = 'heroicon-o-bars-3-bottom-right';
    protected static ?string $navigationLabel = 'Page Order';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort     = 1;
    protected static ?string $title           = 'Page Order';
    protected static string $view             = 'filament.pages.page-order';

    public array $parents = [];

    public function mount(): void {
        $this->loadPages();
    }

    public function loadPages(): void {
        $this->parents = PageModel::whereNull('parent_id')
            ->orderBy('sort_order')
            ->with(['children' => fn ($q) => $q->orderBy('sort_order')])
            ->get()
            ->map(fn (PageModel $page) => [
                'id'          => $page->id,
                'title'       => $page->title,
                'slug'        => $page->slug,
                'show_in_nav' => (bool) $page->show_in_nav,
                'sort_order'  => $page->sort_order,
                'children'    => $page->children->map(fn (PageModel $child) => [
                    'id'          => $child->id,
                    'title'       => $child->title,
                    'slug'        => $child->slug,
                    'show_in_nav' => (bool) $child->show_in_nav,
                    'sort_order'  => $child->sort_order,
                ])->values()->toArray(),
            ])
            ->values()
            ->toArray();
    }

    public function reorderParents(array $orderedIds): void {
        foreach ($orderedIds as $index => $id) {
            PageModel::where('id', $id)->update(['sort_order' => $index]);
        }

        $this->loadPages();

        Notification::make()
            ->title('Parent page order saved')
            ->success()
            ->send();
    }

    public function reorderChildren(string $parentId, array $orderedIds): void {
        foreach ($orderedIds as $index => $id) {
            PageModel::where('id', $id)->update(['sort_order' => $index]);
        }

        $this->loadPages();

        Notification::make()
            ->title('Child page order saved')
            ->success()
            ->send();
    }
}
