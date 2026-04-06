@php use App\Models\SiteSetting; @endphp
@php
    $heading = SiteSetting::get('stats_heading', 'Fighting for Justice');
    $donateLabel = SiteSetting::get('stats_donate_label', 'Donate');
    $bgImage = SiteSetting::get('stats_bg_image');
    $bgUrl = $bgImage ? asset('storage/' . $bgImage) : asset('images/callout-bg.jpg');
    $stat1Value = SiteSetting::get('stat_1_value', '150+');
    $stat1Text = SiteSetting::get('stat_1_text', 'Political prisoners documented across the United States');
    $stat2Value = SiteSetting::get('stat_2_value', '40+');
    $stat2Text = SiteSetting::get('stat_2_text', 'Years served by the longest-held political prisoner');
    $stat3Value = SiteSetting::get('stat_3_value', '26');
    $stat3Text = SiteSetting::get('stat_3_text', 'States with documented cases of political imprisonment');
@endphp

<section class="relative w-full overflow-hidden" style="min-height: 520px;">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $bgUrl }}');"></div>
    <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.5) 40%, rgba(0,0,0,0.85) 100%);"></div>

    <div class="relative z-10 flex flex-col justify-between h-full" style="min-height: 520px; padding: 60px 48px 48px;">
        <div class="flex justify-between items-start">
            <h2 class="text-white uppercase" style="font-size: 52px; font-weight: 900; line-height: 1.05; letter-spacing: 0.02em;">
                {!! nl2br(e($heading)) !!}
            </h2>
        </div>

        <div>
            <div style="border-top: 1px solid rgba(255,255,255,0.25); margin-bottom: 36px;"></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
                <div>
                    <div style="font-size: 52px; font-weight: 300; -webkit-text-stroke: 1px rgba(255,255,255,0.8); color: transparent; line-height: 1.1;">{{ $stat1Value }}</div>
                    <p style="font-size: 15px; font-style: italic; color: rgba(255,255,255,0.75); margin-top: 8px; line-height: 1.5;">{{ $stat1Text }}</p>
                </div>
                <div>
                    <div style="font-size: 52px; font-weight: 300; -webkit-text-stroke: 1px rgba(255,255,255,0.8); color: transparent; line-height: 1.1;">{{ $stat2Value }}</div>
                    <p style="font-size: 15px; font-style: italic; color: rgba(255,255,255,0.75); margin-top: 8px; line-height: 1.5;">{{ $stat2Text }}</p>
                </div>
                <div>
                    <div style="font-size: 52px; font-weight: 300; -webkit-text-stroke: 1px rgba(255,255,255,0.8); color: transparent; line-height: 1.1;">{{ $stat3Value }}</div>
                    <p style="font-size: 15px; font-style: italic; color: rgba(255,255,255,0.75); margin-top: 8px; line-height: 1.5;">{{ $stat3Text }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
