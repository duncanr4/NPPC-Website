{{-- Impact Stats Block - inserted between video hero and articles grid --}}
<section class="relative w-full overflow-hidden" style="min-height: 520px;">
    {{-- Background image with gradient overlay --}}
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/callout-bg.jpg') }}');"></div>
    <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.5) 40%, rgba(0,0,0,0.85) 100%);"></div>

    {{-- Content overlay --}}
    <div class="relative z-10 flex flex-col justify-between h-full" style="min-height: 520px; padding: 60px 48px 48px;">

        {{-- Top area: headline + donate button --}}
        <div class="flex justify-between items-start">
            <h2 class="text-white uppercase" style="font-size: 52px; font-weight: 900; line-height: 1.05; letter-spacing: 0.02em;">
                Fighting for<br>Justice
            </h2>
            <a href="/donate" class="inline-block uppercase text-white font-bold no-underline transition-colors" style="background-color: #d42b2b; font-size: 14px; padding: 14px 32px; letter-spacing: 0.08em;" onmouseenter="this.style.backgroundColor='#b82424'" onmouseleave="this.style.backgroundColor='#d42b2b'">
                Donate
            </a>
        </div>

        {{-- Bottom area: divider + stats --}}
        <div>
            <div style="border-top: 1px solid rgba(255,255,255,0.25); margin-bottom: 36px;"></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                <div>
                    <div style="font-size: 52px; font-weight: 300; -webkit-text-stroke: 1px rgba(255,255,255,0.8); color: transparent; line-height: 1.1;">150+</div>
                    <p style="font-size: 15px; font-style: italic; color: rgba(255,255,255,0.75); margin-top: 8px; line-height: 1.5;">Political prisoners documented across the United States</p>
                </div>
                <div>
                    <div style="font-size: 52px; font-weight: 300; -webkit-text-stroke: 1px rgba(255,255,255,0.8); color: transparent; line-height: 1.1;">40+</div>
                    <p style="font-size: 15px; font-style: italic; color: rgba(255,255,255,0.75); margin-top: 8px; line-height: 1.5;">Years served by the longest-held political prisoner</p>
                </div>
                <div>
                    <div style="font-size: 52px; font-weight: 300; -webkit-text-stroke: 1px rgba(255,255,255,0.8); color: transparent; line-height: 1.1;">26</div>
                    <p style="font-size: 15px; font-style: italic; color: rgba(255,255,255,0.75); margin-top: 8px; line-height: 1.5;">States with documented cases of political imprisonment</p>
                </div>
            </div>
        </div>
    </div>
</section>
