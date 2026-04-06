@php use App\Models\Page; @endphp
@php
    /**
     * @var Page $page
     */
@endphp
@extends('app')

@section('body')
    <div class="line mt-8"></div>
    <h1 class="text-6xl mt-12">{{$page->title}}</h1>
    <div class=" h-[420px] rounded-lg mt-12 mb-6 overflow-hidden  justify-center items-center bg-center bg-cover"
         style="background-image: url('{{ $page->image_url }}')">
    </div>
    <article class="mt-12 prose prose-invert max-w-none">
        {!! $page->body !!}
    </article>
@endsection
