<?php

namespace App\Http\Controllers;

use App\Models\AnnualReport;
use App\Models\Article;
use App\Models\CalendarEntry;
use App\Models\Event;
use App\Models\Faq;
use App\Models\HistoryEra;
use App\Models\Page;
use App\Models\Prisoner;
use App\Models\Product;
use App\Models\Staff;
use App\Models\Timeline;
use Illuminate\Http\Request;

final class SiteController extends Controller {
    public function page(string $slug) {
        if ($page = Page::getBySlug($slug)) {
            return view('page', compact('page'));
        }

        return view('pages.'.$slug);
    }

    public function timeline() {
        return view('pages.timeline', ['timelines' => Timeline::query()->orderBy('year')->get()]);
    }

    public function history() {
        return view('pages.history', ['eras' => HistoryEra::with('topics')->orderBy('sort_order')->get()]);
    }

    public function calendar(Request $request) {
        $month = (int) ($request->input('month', date('n')));
        if ($month < 1 || $month > 12) {
            $month = (int) date('n');
        }

        $entries = CalendarEntry::where('month', $month)
            ->where('published', true)
            ->orderBy('day')
            ->get();

        $monthName = date('F', mktime(0, 0, 0, $month, 1));
        $today = (int) date('j');
        $currentMonth = (int) date('n');

        return view('pages.calendar', compact('entries', 'month', 'monthName', 'today', 'currentMonth'));
    }

    public function store(Request $request) {
        $category = $request->input('category');
        $query = Product::published()->orderBy('sort_order');
        if ($category) {
            $query->where('category', $category);
        }
        $products = $query->get();
        $categories = Product::published()->whereNotNull('category')->where('category', '!=', '')->distinct()->pluck('category');
        $featured = Product::published()->featured()->first();

        return view('pages.store', compact('products', 'categories', 'featured', 'category'));
    }

    public function events(Request $request) {
        $tab = $request->input('tab', 'upcoming');
        $upcoming = Event::published()->upcoming()->get();
        $past = Event::published()->past()->get();
        $series = Event::published()->whereNotNull('series')->where('series', '!=', '')->distinct()->pluck('series');

        return view('pages.events', compact('upcoming', 'past', 'series', 'tab'));
    }

    public function volunteer() {
        return view('pages.volunteer');
    }

    public function prisonerOutreach() {
        return view('pages.prisoner-outreach');
    }

    public function staff(Request $request) {
        $group = $request->input('group');
        if ($group === 'board') {
            $staff = Staff::getBoardMembers();
        } elseif ($group === 'staff') {
            $staff = Staff::getStaffMembers();
        } else {
            $staff = Staff::where('published', true)->get();
        }

        return view('pages.staff', ['staff' => $staff]);
    }

    public function boardOfDirectors() {
        return view('pages.board-of-directors', ['directors' => Staff::getBoardMembers()]);
    }

    public function annualReport() {
        return view('pages.annual_reports', ['reports' => AnnualReport::all()]);
    }

    public function map() {
        return view('pages.map');
    }

    public function faq() {
        return view('pages.faq');
    }

    public function articles() {
        return view('articles', ['articles' => Article::all()]);
    }

    public function article(string $slug) {
        $article = Article::getBySlug($slug);

        return view('article', compact('article'));
    }

    public function search(Request $request) {
        $q = trim($request->input('q', ''));

        if (! $q) {
            return view('pages.search', ['query' => '', 'results' => []]);
        }

        $results = [];

        // Search articles
        $articles = Article::where('title', 'like', "%{$q}%")
            ->orWhere('body', 'like', "%{$q}%")
            ->limit(20)
            ->get();

        foreach ($articles as $article) {
            $results[] = [
                'type'  => 'Article',
                'title' => $article->title,
                'url'   => $article->url,
                'excerpt' => substr(strip_tags($article->body ?? ''), 0, 200),
            ];
        }

        // Search pages
        $pages = Page::where('title', 'like', "%{$q}%")
            ->orWhere('body', 'like', "%{$q}%")
            ->limit(20)
            ->get();

        foreach ($pages as $page) {
            $results[] = [
                'type'  => 'Page',
                'title' => $page->title,
                'url'   => $page->url,
                'excerpt' => substr(strip_tags($page->body ?? ''), 0, 200),
            ];
        }

        // Search prisoners
        $prisoners = Prisoner::where('name', 'like', "%{$q}%")
            ->orWhere('description', 'like', "%{$q}%")
            ->orWhere('aka', 'like', "%{$q}%")
            ->limit(20)
            ->get();

        foreach ($prisoners as $prisoner) {
            $results[] = [
                'type'  => 'Prisoner',
                'title' => $prisoner->name,
                'url'   => '/database',
                'excerpt' => substr($prisoner->description ?? '', 0, 200),
            ];
        }

        // Search FAQs
        $faqs = Faq::where('question', 'like', "%{$q}%")
            ->orWhere('answer', 'like', "%{$q}%")
            ->limit(10)
            ->get();

        foreach ($faqs as $faq) {
            $results[] = [
                'type'  => 'FAQ',
                'title' => $faq->question,
                'url'   => '/faq',
                'excerpt' => substr(strip_tags($faq->answer ?? ''), 0, 200),
            ];
        }

        // Search static pages by name
        $staticPages = [
            'history'           => ['title' => 'History', 'url' => '/history'],
            'volunteer'         => ['title' => 'Volunteer', 'url' => '/volunteer'],
            'prisoner outreach' => ['title' => 'Prisoner Outreach', 'url' => '/prisoner-outreach'],
            'staff'             => ['title' => 'Staff', 'url' => '/staff'],
            'board of directors' => ['title' => 'Board of Directors', 'url' => '/board-of-directors'],
            'annual report'     => ['title' => 'Annual Report', 'url' => '/annual-report'],
            'map'               => ['title' => 'Map', 'url' => '/map'],
            'faq'               => ['title' => 'FAQ', 'url' => '/faq'],
            'donate'            => ['title' => 'Donate', 'url' => '/donate'],
            'contact'           => ['title' => 'Contact Us', 'url' => '/contact'],
            'database'          => ['title' => 'Prisoner Database', 'url' => '/database'],
            'news'              => ['title' => 'News', 'url' => '/news'],
        ];

        foreach ($staticPages as $keyword => $page) {
            if (stripos($keyword, $q) !== false) {
                $results[] = [
                    'type'    => 'Page',
                    'title'   => $page['title'],
                    'url'     => $page['url'],
                    'excerpt' => '',
                ];
            }
        }

        return view('pages.search', ['query' => $q, 'results' => $results]);
    }

    public function home() {
        return view('home');
    }
}
