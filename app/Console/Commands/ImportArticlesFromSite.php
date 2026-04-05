<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportArticlesFromSite extends Command {
    protected $signature = 'articles:import-from-site
                            {url : Base URL of the site (e.g. https://nationalpoliticalprisonercoalition.org)}
                            {--dry-run : Preview without saving}
                            {--limit=0 : Max articles to import (0 = all)}';

    protected $description = 'Scrape articles from the live site and import them into the local database';

    private string $baseUrl;

    public function handle(): int {
        $this->baseUrl = rtrim($this->argument('url'), '/');
        $dryRun = $this->option('dry-run');
        $limit = (int) $this->option('limit');

        $this->info("Scraping articles from: {$this->baseUrl}/news");

        // Step 1: Get the article listing page
        $articleUrls = $this->scrapeArticleList();

        if (empty($articleUrls)) {
            $this->error('No articles found on the listing page.');
            $this->line('This could mean:');
            $this->line('  - The site is blocking requests (try from a different machine)');
            $this->line('  - The page structure has changed');
            $this->line('  - The URL is incorrect');

            return self::FAILURE;
        }

        $this->info(count($articleUrls).' article URLs found.');

        if ($limit > 0) {
            $articleUrls = array_slice($articleUrls, 0, $limit);
            $this->info("Limited to {$limit} articles.");
        }

        if ($dryRun) {
            $this->table(['#', 'URL'], array_map(fn ($url, $i) => [$i + 1, $url], $articleUrls, array_keys($articleUrls)));
            $this->warn('Dry run — no articles imported.');

            return self::SUCCESS;
        }

        Storage::disk('public')->makeDirectory('articles');

        $bar = $this->output->createProgressBar(count($articleUrls));
        $imported = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($articleUrls as $url) {
            try {
                $slug = $this->extractSlug($url);

                // Skip if already exists
                if (Article::where('slug', $slug)->exists()) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $articleData = $this->scrapeArticle($url);

                if (! $articleData) {
                    $failed++;
                    $bar->advance();
                    continue;
                }

                // Resolve category
                $categoryId = null;
                if ($articleData['category']) {
                    $category = Category::firstOrCreate(
                        ['slug' => Str::slug($articleData['category'])],
                        ['title' => $articleData['category']]
                    );
                    $categoryId = $category->id;
                }

                // Resolve author
                $authorId = null;
                if ($articleData['author']) {
                    $author = Author::firstOrCreate(
                        ['name' => $articleData['author']],
                        ['name' => $articleData['author']]
                    );
                    $authorId = $author->id;
                }

                // Download image
                $imagePath = null;
                if ($articleData['image_url']) {
                    $imagePath = $this->downloadImage($articleData['image_url'], $slug);
                }

                Article::create([
                    'title'        => $articleData['title'],
                    'slug'         => $slug,
                    'body'         => $articleData['body'],
                    'image'        => $imagePath,
                    'category_id'  => $categoryId,
                    'author_id'    => $authorId,
                    'published_at' => $articleData['date'],
                ]);

                $imported++;
            } catch (\Throwable $e) {
                $this->newLine();
                $this->warn("Failed: {$url} — {$e->getMessage()}");
                $failed++;
            }

            $bar->advance();

            // Be polite — don't hammer the server
            usleep(500000); // 0.5 second delay
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(['Result', 'Count'], [
            ['Imported', $imported],
            ['Skipped (already exists)', $skipped],
            ['Failed', $failed],
        ]);

        return self::SUCCESS;
    }

    private function scrapeArticleList(): array {
        $urls = [];
        $page = $this->fetchPage("{$this->baseUrl}/news");

        if (! $page) {
            // Try the base URL and look for article links
            $page = $this->fetchPage($this->baseUrl);
        }

        if (! $page) {
            return [];
        }

        // Find all /news/slug links
        preg_match_all('#href=["\'](/news/[a-zA-Z0-9\-]+)["\']#', $page, $matches);

        foreach ($matches[1] as $path) {
            $fullUrl = $this->baseUrl.$path;
            if (! in_array($fullUrl, $urls)) {
                $urls[] = $fullUrl;
            }
        }

        // Also try finding links in different formats
        preg_match_all('#href=["\']('.preg_quote($this->baseUrl, '#').'/news/[a-zA-Z0-9\-]+)["\']#', $page, $matches2);

        foreach ($matches2[1] as $fullUrl) {
            if (! in_array($fullUrl, $urls)) {
                $urls[] = $fullUrl;
            }
        }

        return $urls;
    }

    private function scrapeArticle(string $url): ?array {
        $html = $this->fetchPage($url);

        if (! $html) {
            return null;
        }

        $data = [
            'title'     => null,
            'body'      => null,
            'image_url' => null,
            'category'  => null,
            'author'    => null,
            'date'      => null,
        ];

        // Extract title — the article h1 has class "text-6xl"
        if (preg_match('#<h1[^>]*class="[^"]*text-6xl[^"]*"[^>]*>(.*?)</h1>#si', $html, $m)) {
            $data['title'] = trim(strip_tags($m[1]));
        }
        // Fallback: last h1 on the page (skip nav h1s)
        if (! $data['title']) {
            preg_match_all('#<h1[^>]*>(.*?)</h1>#si', $html, $matches);
            if (! empty($matches[1])) {
                foreach (array_reverse($matches[1]) as $h1) {
                    $text = trim(strip_tags($h1));
                    if (strlen($text) > 10) {
                        $data['title'] = $text;
                        break;
                    }
                }
            }
        }

        // Extract article body — inside <article> tag
        // Use greedy match up to </article> but stop before <script> tags
        if (preg_match('#<article[^>]*>(.*?)</article>#si', $html, $m)) {
            $body = $m[1];
            // Remove any script tags injected by Cloudflare
            $body = preg_replace('#<script[^>]*>.*?</script>#si', '', $body);
            $data['body'] = $this->htmlToMarkdown(trim($body));
        }

        // Extract hero image from background-image style on the article image div
        // Look for the large hero div specifically (h-[420px] class)
        if (preg_match('#<div[^>]*(?:h-\[420px\]|h-56)[^>]*style="[^"]*background-image:\s*url\([\'"]?(.*?)[\'"]?\)[^"]*"#si', $html, $m)) {
            $imgUrl = $m[1];
            if (str_starts_with($imgUrl, '/')) {
                $imgUrl = $this->baseUrl.$imgUrl;
            }
            $data['image_url'] = $imgUrl;
        }
        // Fallback: any background-image with /storage/ path
        if (! $data['image_url'] && preg_match('#background-image:\s*url\([\'"]?(/storage/[^\'"\)]+)[\'"]?\)#', $html, $m)) {
            $data['image_url'] = $this->baseUrl.$m[1];
        }

        // Extract date — "Published Feb 20, 2025" format
        if (preg_match('#Published\s+((?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\w*\s+\d{1,2},?\s+\d{4})#i', $html, $m)) {
            try {
                $data['date'] = \Carbon\Carbon::parse($m[1])->format('Y-m-d');
            } catch (\Exception) {
            }
        }
        // Fallback: any date format
        if (! $data['date'] && preg_match('#((?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)\w*\s+\d{1,2},?\s+\d{4})#i', $html, $m)) {
            try {
                $data['date'] = \Carbon\Carbon::parse($m[1])->format('Y-m-d');
            } catch (\Exception) {
            }
        }

        // Extract author — look for "By AuthorName" or author section
        if (preg_match('#<span[^>]*>\s*(?:By|Author:?)\s*</span>\s*<span[^>]*>(.*?)</span>#si', $html, $m)) {
            $data['author'] = trim(strip_tags($m[1]));
        } elseif (preg_match('#(?:By|Author:?)\s+([A-Z][a-z]+(?:\s+[A-Z][a-z]+){1,3})#', $html, $m)) {
            $data['author'] = trim($m[1]);
        }

        // Extract category from h5 tag near the article
        if (preg_match('#<h5[^>]*>\s*(.*?)\s*</h5>#si', $html, $m)) {
            $cat = trim(strip_tags($m[1]));
            if ($cat && strlen($cat) > 1 && strlen($cat) < 50 && ! str_contains(strtolower($cat), 'news')) {
                $data['category'] = $cat;
            }
        }

        if (! $data['title']) {
            return null;
        }

        return $data;
    }

    private function htmlToMarkdown(string $html): string {
        // Basic HTML to Markdown conversion
        $text = $html;

        // Headers
        $text = preg_replace('#<h1[^>]*>(.*?)</h1>#si', "# $1\n\n", $text);
        $text = preg_replace('#<h2[^>]*>(.*?)</h2>#si', "## $1\n\n", $text);
        $text = preg_replace('#<h3[^>]*>(.*?)</h3>#si', "### $1\n\n", $text);
        $text = preg_replace('#<h4[^>]*>(.*?)</h4>#si', "#### $1\n\n", $text);

        // Bold and italic
        $text = preg_replace('#<strong[^>]*>(.*?)</strong>#si', '**$1**', $text);
        $text = preg_replace('#<b[^>]*>(.*?)</b>#si', '**$1**', $text);
        $text = preg_replace('#<em[^>]*>(.*?)</em>#si', '*$1*', $text);
        $text = preg_replace('#<i[^>]*>(.*?)</i>#si', '*$1*', $text);

        // Links
        $text = preg_replace('#<a[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)</a>#si', '[$2]($1)', $text);

        // List items
        $text = preg_replace('#<li[^>]*>(.*?)</li>#si', "- $1\n", $text);
        $text = preg_replace('#</?[uo]l[^>]*>#si', "\n", $text);

        // Paragraphs and breaks
        $text = preg_replace('#<br\s*/?>#si', "\n", $text);
        $text = preg_replace('#<p[^>]*>(.*?)</p>#si', "$1\n\n", $text);

        // Remove remaining HTML tags
        $text = strip_tags($text);

        // Clean up whitespace
        $text = preg_replace('#\n{3,}#', "\n\n", $text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        return trim($text);
    }

    private function downloadImage(string $url, string $slug): ?string {
        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $contentType = $response->header('Content-Type');
            $ext = match (true) {
                str_contains($contentType ?? '', 'png')  => 'png',
                str_contains($contentType ?? '', 'gif')  => 'gif',
                str_contains($contentType ?? '', 'webp') => 'webp',
                default                                  => 'jpg',
            };

            $filename = "articles/{$slug}.{$ext}";
            Storage::disk('public')->put($filename, $response->body());

            return $filename;
        } catch (\Throwable) {
            return null;
        }
    }

    private function extractSlug(string $url): string {
        $path = parse_url($url, PHP_URL_PATH);

        return basename($path);
    }

    private function fetchPage(string $url): ?string {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                ])
                ->get($url);

            if (! $response->successful()) {
                $this->warn("HTTP {$response->status()} for {$url}");

                return null;
            }

            return $response->body();
        } catch (\Throwable $e) {
            $this->warn("Failed to fetch {$url}: {$e->getMessage()}");

            return null;
        }
    }
}
