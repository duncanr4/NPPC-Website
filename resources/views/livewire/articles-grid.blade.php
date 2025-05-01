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
        <div class = "md:flex block justify-center mb-12" >
            <button
                    wire:click = "selectCategory('Latest')"
                    class = "uppercase text-lg md:text-lg md:inline block md:mb-0 mb-6  border-b-2 mx-2 md:mx-6 text-center {{ $selectedCategory === 'Latest' ? ' border-indigo-500' : 'border-transparent' }}" >
                Latest
            </button >
            @foreach ($categories as $category)
                <button
                        wire:click="selectCategory('{{ $category->title }}')"
                        class="uppercase text-lg md:text-lg md:inline block md:mb-0 mb-6  border-b-2 mx-2 md:mx-6 text-center {{ $selectedCategory === $category->title ? ' border-indigo-500' : 'border-transparent' }}">
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
