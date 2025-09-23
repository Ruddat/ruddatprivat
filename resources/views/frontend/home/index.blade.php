@extends("frontend.layouts.app")

@section("title", "Willkommen bei Ingo Ruddat")

@section("content")

    @include("frontend.home.sections.hero")

    @include("frontend.home.sections.about")

    @include("frontend.home.sections.call-to-action")

    @livewire("frontend.portfolio-grid")


    @include("frontend.home.sections.why-us")

    {{--

    @include('frontend.home.sections.service-new')

@include('frontend.home.sections.services')

@include('frontend.home.sections.team')

@include('frontend.home.sections.clients')

@include('frontend.home.sections.portfolio')
@include('frontend.home.sections.testimonal')



    @include("frontend.home.sections.testimonal")
--}}

    @include("frontend.home.sections.contact")

@endsection
