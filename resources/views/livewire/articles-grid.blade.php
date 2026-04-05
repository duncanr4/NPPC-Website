<?php

use App\Models\Article;

function renderArticle(Article $article, bool $large = false): string {
    if(!$article) return '';

    $imgHeight = $large ? 'h-56 md:h-[600px]' : 'h-56';
    return <<<EOB
<div class="article-item mb-6">
    <a href="{$article->url}" class="{$imgHeight} block overflow-hidden justify-center items-center bg-center bg-cover" style="background-image: url('{$article->image_url}')"></a>
    <div class="line"></div>
    <h5 class="mt-6">{$article->category?->title}</h5>
    <a class="text-xl text-white" href="{$article->url}">{$article->title}</a>
</div>
EOB;

}
?>
<section >
    <h1 class = "text-6xl mt-12 mb-12" > News</h1 >
    <div class = "line mt-8" ></div >

    <div class = "py-12" >
        <div style="display: flex; justify-content: center; gap: 24px; flex-wrap: wrap; margin-bottom: 48px;">
            <button
                    wire:click = "selectCategory('Latest')"
                    style="text-transform: uppercase; font-size: 14px; font-weight: 600; letter-spacing: 0.08em; padding-bottom: 8px; border-bottom: 2px solid {{ $selectedCategory === 'Latest' ? '#6366f1' : 'transparent' }}; background: none; color: #fff; cursor: pointer;">
                Latest
            </button >
            @foreach ($categories as $category)
                <button
                        wire:click="selectCategory('{{ $category->title }}')"
                        style="text-transform: uppercase; font-size: 14px; font-weight: 600; letter-spacing: 0.08em; padding-bottom: 8px; border-bottom: 2px solid {{ $selectedCategory === $category->title ? '#6366f1' : 'transparent' }}; background: none; color: #fff; cursor: pointer;">
                    {{ $category->title }}
                </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-12">

            <div>
                <?php $x = 0; foreach ($articles as $article) {
                    $x++; if ($x === 2) break; ?>
                {!! renderArticle($article, true) !!}
                <?php } ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php $x = 0; foreach ($articles as $article) {
                    $x++; if ($x === 1) continue;  if ($x === 6) break; ?>
                {!! renderArticle($article) !!}
                <?php } ?>
            </div>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            <?php $x = 0; foreach ($articles as $article) {
                $x++; if ($x < 6) continue; ?>
            {!! renderArticle($article) !!}
            <?php } ?>
        </div>

    </div>

</section>
