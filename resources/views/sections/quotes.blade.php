@php $quotes = App\Models\Quote::inRandomOrder()->get(); @endphp
@if($quotes->isNotEmpty())
<section style="position:relative; min-height:200px; overflow:hidden; padding:16px 0 80px;">
    <div style="max-width:1100px; margin:0 auto; padding:0 24px; position:relative;">

        @foreach($quotes as $i => $quote)
            <div class="quote-slide" style="position:{{ $i === 0 ? 'relative' : 'absolute' }}; top:0; left:0; right:0; opacity:{{ $i === 0 ? '1' : '0' }}; transition:opacity 0.8s ease; padding:0 24px;">
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

    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var slides = document.querySelectorAll('.quote-slide');
    if (slides.length <= 1) return;

    var current = 0;
    var interval = 8000;

    function showQuote(index) {
        slides[current].style.position = 'absolute';
        slides[current].style.opacity = '0';
        slides[index].style.position = 'relative';
        slides[index].style.opacity = '1';
        current = index;
    }

    setInterval(function () {
        showQuote((current + 1) % slides.length);
    }, interval);
});
</script>
@endif
