<?php

namespace App\Http\Controllers;

use App\Models\AnnualReport;
use App\Models\Article;
use App\Models\Page;
use App\Models\Staff;
use App\Models\Timeline;

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

    public function volunteer() {
        return view('pages.volunteer');
    }

    public function staff() {
        return view('pages.staff', ['staff' => Staff::getStaffMembers()]);
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

    public function home() {
        return view('home');
    }
}
