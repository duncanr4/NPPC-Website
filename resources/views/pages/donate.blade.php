@extends('app')

@section('body')
    <h1 class="text-6xl mt-12 mb-12">Donate</h1>
    <div class="line mt-8"></div>

   <section class="block lg:flex justify-between mt-8">
       <div class="w-full lg:w-1/2 pr-0 lg:pr-12">
           <div class="h-[280px] lg:h-[430px] rounded-lg mb-6 overflow-hidden  justify-center items-center bg-center bg-cover" style="background-image: url('/images/stop-jailing-truth-tellers.webp')">
           </div>
       </div>

       <div class="w-full lg:w-1/2">
           <h2 class="text-3xl font-bold mb-2">Donate to help free political prisoners.</h2>
           <p class="mb-6">
               The National Political Prisoner Coalition works to support our nation's political prisoners,
               fight against wrongful convictions, and create fair, compassionate, and equitable systems of
               justice for everyone. With your support, we can do even more—donate today.
           </p>

           <livewire:donation />
       </div>
   </section>

    @include('sections.faq', ['type'=>'donation'])


@endsection


@section('footer')
    <div id="app-gallery"></div>
@endsection
