@extends('app')

@section('head')
<style>
    .cal-page { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
    .cal-header { display: flex; align-items: center; justify-content: space-between; padding: 48px 0 40px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 48px; }
    .cal-header-left { display: flex; align-items: center; gap: 16px; }
    .cal-header-title { font-size: 18px; font-weight: 800; color: #fff; }
    .cal-month-select { display: flex; align-items: center; gap: 8px; }
    .cal-month-name { font-size: 2.5rem; font-weight: 900; color: #fff; }
    .cal-month-nav { display: flex; gap: 4px; }
    .cal-month-btn { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15); color: #fff; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; border-radius: 4px; text-decoration: none; font-size: 18px; transition: background 0.15s; }
    .cal-month-btn:hover { background: rgba(255,255,255,0.12); }
    .cal-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; padding-bottom: 80px; }
    .cal-card { border: 1px solid rgba(255,255,255,0.12); border-radius: 4px; overflow: hidden; transition: border-color 0.2s; }
    .cal-card:hover { border-color: rgba(255,255,255,0.3); }
    .cal-card.today { border-color: #5660fe; border-width: 2px; }
    .cal-card-top { padding: 24px 20px 20px; text-align: center; }
    .cal-card-day { font-size: 5rem; font-weight: 900; color: #fff; line-height: 1; margin-bottom: 16px; }
    .cal-card.today .cal-card-day { color: #5660fe; }
    .cal-card-title { font-size: 15px; font-weight: 600; color: rgba(255,255,255,0.85); line-height: 1.4; min-height: 42px; }
    .cal-card-divider { width: 24px; height: 2px; background: rgba(255,255,255,0.3); margin: 16px auto 8px; }
    .cal-card-year { font-size: 14px; color: rgba(255,255,255,0.5); font-weight: 600; }
    .cal-card-image { aspect-ratio: 16/10; overflow: hidden; }
    .cal-card-image img { width: 100%; height: 100%; object-fit: cover; filter: grayscale(60%); transition: filter 0.3s; }
    .cal-card:hover .cal-card-image img { filter: grayscale(0); }
    .cal-card-placeholder { width: 100%; height: 100%; min-height: 120px; background: #111; }
    .cal-empty-card { border: 1px dashed rgba(255,255,255,0.08); border-radius: 4px; padding: 40px 20px; text-align: center; }
    .cal-empty-day { font-size: 5rem; font-weight: 900; color: rgba(255,255,255,0.08); line-height: 1; }
    .cal-months-bar { display: flex; gap: 4px; flex-wrap: wrap; margin-bottom: 32px; }
    .cal-months-btn { padding: 6px 16px; font-size: 13px; font-weight: 600; border: 1px solid rgba(255,255,255,0.15); color: rgba(255,255,255,0.6); background: transparent; cursor: pointer; border-radius: 4px; text-decoration: none; transition: all 0.15s; }
    .cal-months-btn:hover { border-color: #5660fe; color: #fff; }
    .cal-months-btn.active { background: #5660fe; border-color: #5660fe; color: #fff; }
    @media (max-width: 900px) { .cal-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 500px) { .cal-grid { grid-template-columns: 1fr; } .cal-month-name { font-size: 1.8rem; } }
</style>
@endsection

@section('body')
<div class="cal-page">
    {{-- Header --}}
    <div class="cal-header">
        <div class="cal-header-left">
            <div class="cal-header-title">A History of Political Prisoners</div>
        </div>
        <div class="cal-month-select">
            <a href="/calendar?month={{ $month > 1 ? $month - 1 : 12 }}" class="cal-month-btn">&larr;</a>
            <div class="cal-month-name">{{ $monthName }}</div>
            <a href="/calendar?month={{ $month < 12 ? $month + 1 : 1 }}" class="cal-month-btn">&rarr;</a>
        </div>
    </div>

    {{-- Month buttons --}}
    <div class="cal-months-bar">
        @php $monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp
        @for($m = 1; $m <= 12; $m++)
            <a href="/calendar?month={{ $m }}" class="cal-months-btn {{ $month === $m ? 'active' : '' }}">{{ $monthNames[$m-1] }}</a>
        @endfor
    </div>

    {{-- Calendar Grid --}}
    <div class="cal-grid">
        @php
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, 2026);
            $entriesByDay = $entries->keyBy('day');
        @endphp

        @for($d = 1; $d <= $daysInMonth; $d++)
            @if(isset($entriesByDay[$d]))
                @php $entry = $entriesByDay[$d]; @endphp
                <div class="cal-card {{ ($month === $currentMonth && $d === $today) ? 'today' : '' }}">
                    <div class="cal-card-top">
                        <div class="cal-card-day">{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="cal-card-title">{{ $entry->title }}</div>
                        <div class="cal-card-divider"></div>
                        <div class="cal-card-year">{{ $entry->year }}</div>
                    </div>
                    <div class="cal-card-image">
                        @if($entry->image)
                            <img src="{{ Storage::url($entry->image) }}" alt="{{ $entry->title }}">
                        @else
                            <div class="cal-card-placeholder"></div>
                        @endif
                    </div>
                </div>
            @else
                <div class="cal-empty-card">
                    <div class="cal-empty-day">{{ str_pad($d, 2, '0', STR_PAD_LEFT) }}</div>
                </div>
            @endif
        @endfor
    </div>
</div>
@endsection
