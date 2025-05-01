@php use App\Models\Article; @endphp
@php
    /**
     * @var Article $article
     */
@endphp


<div x-data="{ open: false }" class="relative w-[220px] flex justify-end">
    <button @click="open = !open" class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-gray-100 hover:text-black focus:outline-none">
        <i class="fa-light fa-inbox-out"></i>
    </button>

    <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-12 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-20">
        <button onclick="copyLink()" class="w-full px-4 py-2 text-left text-sm text-gray-800 hover:bg-gray-100 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-1.415 1.415a4 4 0 01-5.656-5.656l1.415-1.415m2.828-2.828a4 4 0 015.656 5.656l-1.415 1.415" />
            </svg>
            Copy link
        </button>

        <a :href="`https://twitter.com/intent/tweet?url=${encodeURIComponent(window.location.href)}`" target="_blank" class="block px-4 py-2 text-sm text-gray-800 hover:bg-gray-100 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M22.46 6c-.77.35-1.6.58-2.46.69a4.3 4.3 0 001.88-2.37 8.59 8.59 0 01-2.72 1.04 4.28 4.28 0 00-7.29 3.9A12.14 12.14 0 013 5.1a4.27 4.27 0 001.32 5.71 4.25 4.25 0 01-1.94-.54v.06a4.28 4.28 0 003.44 4.2 4.28 4.28 0 01-1.93.07 4.28 4.28 0 003.99 2.97A8.6 8.6 0 012 19.54a12.15 12.15 0 006.56 1.92c7.88 0 12.2-6.53 12.2-12.2 0-.19 0-.38-.01-.57A8.73 8.73 0 0024 5.5a8.47 8.47 0 01-2.54.7z"/>
            </svg>
            Share on X
        </a>

        <a :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`" target="_blank" class="block px-4 py-2 text-sm text-gray-800 hover:bg-gray-100 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M22 12a10 10 0 10-11.63 9.87v-7h-2v-2.87h2V9.57c0-2 1.19-3.11 3-3.11.87 0 1.79.15 1.79.15v2h-1.01c-.99 0-1.3.61-1.3 1.23v1.48h2.22l-.36 2.87h-1.86v7A10 10 0 0022 12z"/>
            </svg>
            Share on Facebook
        </a>

        <a :href="`https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(window.location.href)}`" target="_blank" class="block px-4 py-2 text-sm text-gray-800 hover:bg-gray-100 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-10h3v10zm-1.5-11.269c-.966 0-1.75-.786-1.75-1.756 0-.971.784-1.755 1.75-1.755s1.75.784 1.75 1.755c0 .97-.784 1.756-1.75 1.756zm13.5 11.269h-3v-5.604c0-3.368-4-3.112-4 0v5.604h-3v-10h3v1.444c1.396-2.586 7-2.777 7 2.476v6.08z"/>
            </svg>
            Share on LinkedIn
        </a>
    </div>
</div>

<script>
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copied to clipboard');
        });
    }
</script>
