@php use App\Models\Article; @endphp
@php
    /**
     * @var Article $article
     */
@endphp


@if(!empty($article->citations))
    <div class="flex flex-wrap items-center gap-4">
        @foreach($article->citations as $title => $content)
            <div class="relative group">
                <button class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16h8M8 12h8m-6 8h6a2 2 0 002-2V6a2 2 0 00-2-2h-6a2 2 0 00-2 2v2M8 6h.01" />
                    </svg>
                    <span class="text-gray-800 font-medium text-sm">{{ $title }}</span>
                </button>
                <div class="absolute bottom-full mb-2 left-1/2 transform -translate-x-1/2 w-72 p-4 bg-gray-800 text-white text-sm rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                    <div class="whitespace-pre-line">{{ $content }}</div>
                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-3 h-3 bg-gray-800 rotate-45"></div>
                </div>
            </div>
        @endforeach
    </div>
@endif
