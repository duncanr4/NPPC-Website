@extends('app')

@section('body')
    <div style="max-width: 900px; margin: 0 auto; padding: 48px 24px 80px;">
        <h1 style="font-size: 3rem; font-weight: 900; color: #fff; margin-bottom: 16px;">Podcast</h1>
        <p style="font-size: 18px; color: rgba(255,255,255,0.6); line-height: 1.7; margin-bottom: 40px; max-width: 600px;">
            Listen to conversations about political prisoners, civil liberties, and the fight for justice.
        </p>
        <div class="line" style="margin-bottom: 40px;"></div>

        @include('sections.podcast-player', ['episodes' => $episodes])

        @if($episodes->isEmpty())
            <div style="text-align: center; padding: 60px 0; color: rgba(255,255,255,0.4);">
                No episodes yet. Check back soon!
            </div>
        @endif
    </div>
@endsection
