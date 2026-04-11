@php $quotes = App\Models\Quote::inRandomOrder()->get(); @endphp
@if($quotes->isNotEmpty())
<section class="quotes-section" style="position:relative; min-height:320px; overflow:hidden; padding:64px 0;">
    <div style="max-width:1100px; margin:0 auto; padding:0 24px; position:relative;">

        @foreach($quotes as $i => $quote)
            <div class="quote-slide" style="position:absolute; top:0; left:0; right:0; opacity:{{ $i === 0 ? '1' : '0' }}; transition:opacity 0.8s ease; padding:0 24px;">
                <div style="display:flex; align-items:center; gap:40px;">

                    {{-- Author image --}}
                    @if($quote->author_image)
                        <div style="flex:0 0 160px;">
                            <div style="width:160px; height:160px; border-radius:50%; overflow:hidden; background:#111;">
                                <img src="/storage/{{ $quote->author_image }}" alt="{{ $quote->author_name }}" style="width:100%; height:100%; object-fit:cover;">
                            </div>
                        </div>
                    @endif

                    {{-- Quote content --}}
                    <div style="flex:1; min-width:0;">
                        {{-- Quote marks --}}
                        <div style="display:flex; align-items:flex-start; gap:16px;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="#5660fe" style="flex-shrink:0; margin-top:4px;">
                                <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/>
                            </svg>
                            <div>
                                <p style="font-size:1.65rem; font-weight:800; color:#fff; line-height:1.4; margin:0 0 20px;">{{ $quote->text }}</p>
                                <cite style="font-size:15px; color:rgba(255,255,255,0.5); font-style:normal; font-weight:600;">- {{ $quote->author_name }}</cite>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Navigation dots --}}
        @if($quotes->count() > 1)
            <div class="quote-dots" style="display:flex; justify-content:center; gap:8px; margin-top:240px;">
                @foreach($quotes as $i => $quote)
                    <button onclick="goToQuote({{ $i }})" class="quote-dot" data-index="{{ $i }}"
                        style="width:10px; height:10px; border-radius:50%; border:none; cursor:pointer; transition:background 0.3s; {{ $i === 0 ? 'background:#5660fe;' : 'background:rgba(255,255,255,0.2);' }}">
                    </button>
                @endforeach
            </div>
        @endif
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var slides = document.querySelectorAll('.quote-slide');
    var dots = document.querySelectorAll('.quote-dot');
    if (slides.length <= 1) return;

    var current = 0;
    var interval = 8000;
    var timer;

    function showQuote(index) {
        slides.forEach(function (s) { s.style.opacity = '0'; });
        dots.forEach(function (d) { d.style.background = 'rgba(255,255,255,0.2)'; });
        slides[index].style.opacity = '1';
        if (dots[index]) dots[index].style.background = '#5660fe';
        current = index;
    }

    function next() {
        showQuote((current + 1) % slides.length);
    }

    window.goToQuote = function (index) {
        clearInterval(timer);
        showQuote(index);
        timer = setInterval(next, interval);
    };

    timer = setInterval(next, interval);
});
</script>
@endif
