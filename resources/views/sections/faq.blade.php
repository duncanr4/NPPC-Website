@php use App\Models\Faq; @endphp
<section id="map_faq">
    <div class="{{ $type === 'map' ? 'container' : 'mt-8' }}">
        <h2 style="font-size: 2rem; font-weight: bold;">Frequently Asked Questions</h2>
        <div class="accordion">
            @foreach(Faq::getFaqsByType($type) as $faq)
                <div class="accordion-item">
                    <button class="accordion-button" aria-expanded="false">
                        <span class="accordion-title">{{ $faq->question }}</span>
                        <div class="faq-open-close"></div>
                    </button>
                    <div class="accordion-content">
                        <p>{{ $faq->answer }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const items = document.querySelectorAll(".accordion-button");

        function toggleAccordion() {
            const isExpanded = this.getAttribute('aria-expanded') === "true";

            items.forEach(item => {
                item.setAttribute('aria-expanded', 'false');
                item.querySelector(".faq-open-close").classList.remove("active");
                item.nextElementSibling.style.maxHeight = null;
            });

            if (!isExpanded) {
                this.setAttribute('aria-expanded', 'true');
                this.querySelector(".faq-open-close").classList.add("active");
                this.nextElementSibling.style.maxHeight = this.nextElementSibling.scrollHeight + "px";
            }
        }

        items.forEach(item => item.addEventListener('click', toggleAccordion));
    });
</script>
