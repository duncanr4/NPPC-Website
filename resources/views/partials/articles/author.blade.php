@php use App\Models\Article; @endphp
@php
    /**
     * @var Article $article
     */
@endphp


@if(!empty($article->author))
    <div class="flex items-center gap-4 text-sm text-white">
        <img src="{{ $article->author['avatar_url'] }}" alt="{{ $article->author['name'] }}" class="w-8 h-8 rounded-full object-cover" />

        <div class="flex flex-col">
            <div class="flex items-center gap-1">
                <span class="text-gray-300">By</span>
                <span class="font-semibold text-white">{{ $article->author['name'] }}</span>
            </div>
            <div class="flex items-center gap-2 text-gray-400 mt-0.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Published {{ $article->published_at->format('M j, Y') }}</span>
            </div>
        </div>

    </div>
@endif
