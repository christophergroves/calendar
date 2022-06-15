@extends('layouts.layout')


@section('view_specific_links')
    @include('partials.calendar.links')
@endsection

@section('view_specific_css')
    @include('partials.calendar.css')
@endsection


@section('title')
    <h1 style='padding-bottom: 1ch;'>Calendar Title</h1>
@endsection


@section('section_1')
    <div id='calendar'></div>
@endsection


@section('footer_scripts')
    @include('partials.calendar.js')
@endsection






