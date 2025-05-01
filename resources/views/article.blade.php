@php use App\Models\Article; @endphp
@php
    /**
     * @var Article $article
     */
@endphp
@extends('app')

@section('body')
    <div class="line mt-8"></div>
    <h1 class="text-6xl mt-12">{{$article->title}}</h1>

    <div class="flex justify-between">
        @include('partials.articles.author')
        @include('partials.articles.share')
    </div>


    <div class=" h-[420px] rounded-lg mt-12 mb-6 overflow-hidden  justify-center items-center bg-center bg-cover" style="background-image: url('{{ $article->image_url }}')">
    </div>
    <article class="mt-12">
        @markdom($article->body)


   <div class="flex justify-between mt-12">
       @include('partials.articles.tags', ['size'=>'normal'])

       @include('partials.articles.citations')
   </div>


    </article>
@endsection
