<?php

namespace App\Views;

use App\Models\Page;
use App\Views\DTOs\MenuItemDTO;

abstract class ViewSupport {
    /**
     * @return array<MenuItemDTO>
     */
    public static function getMenuItems(): array {
        $response   = [];
        $response[] = new MenuItemDTO(title: 'Database', href: '/database', active: true);
        $response[] = new MenuItemDTO(title: 'News', href: '/news');

        $parentPages = Page::where('parent_id', null)->get();

        foreach ($parentPages as $page) {
            $children = [];

            foreach ($page->children as $child) {
                $children[] = new MenuItemDTO(title: $child->title, href: $child->url);
            }

            if ($page->slug === 'get-involved') {
                $children[] = new MenuItemDTO(title: 'Volunteer', href: '/volunteer');
            }

            $response[] = new MenuItemDTO(title: $page->title, href: $page->url, children: empty($children) ? null : $children);
        }

        return $response;
    }
}
