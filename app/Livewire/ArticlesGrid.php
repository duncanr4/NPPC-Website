<?php

namespace App\Livewire;

use App\Models\Article;
use App\Models\Category;
use Livewire\Component;

class ArticlesGrid extends Component {
    public $categories;
    public $selectedCategory = 'Latest';
    public $articles;
    public $limit;

    public function mount($limit = null) {
        $this->categories = Category::all();
        $this->limit      = $limit;
        $this->loadArticles();
    }

    public function loadArticles() {
        if ($this->selectedCategory === 'Latest') {
            $query = Article::orderBy('published_at', 'desc');
        } else {
            $query = Article::whereHas('category', function ($query) {
                $query->where('title', $this->selectedCategory);
            })->orderBy('published_at', 'desc');
        }

        if ($this->limit) {
            $this->articles = $query->limit($this->limit)->get();
        } else {
            $this->articles = $query->get();
        }
    }

    public function selectCategory($category) {
        $this->selectedCategory = $category;
        $this->loadArticles();
    }

    public function render() {
        return view('livewire.articles-grid', [
            'categories' => $this->categories,
            'articles'   => $this->articles,
        ]);
    }
}
