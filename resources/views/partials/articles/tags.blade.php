@php use App\Models\Article; @endphp
@php
    /**
     * @var Article $article
     */
@endphp


@php
    $isSmall = $size === 'small';
@endphp


@if(!empty($article->tags))
    <div class="flex flex-wrap gap-2">
        @foreach($article->tags as $tag)
            <span
               class="bg-[#5660fe] text-white font-bold block uppercase {{ ($size ?? 'normal') === 'small' ? 'text-xs px-2 py-1' : 'text-sm px-3 leading-[38px]' }} rounded">
                {{ strtoupper($tag->name) }}
            </span>

        @endforeach
    </div>
@endif
