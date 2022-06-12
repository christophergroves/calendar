@extends('layouts.layout')


@section('view_specific_links')
    @include('partials.calendar.links')
@endsection


@section('section_1')
    <div id='calendar'></div>
@endsection


@section('footer_scripts')
    @include('partials.calendar.js')
@endsection






