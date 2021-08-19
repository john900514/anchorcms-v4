@extends('errors.layout')

@php
    $error_number = $error_number ?? '';
    $heading = 'Section Not Yet Available';
@endphp

@section('title')
    Coming Soon.
@endsection

@section('heading')
    {{ $heading }}
@endsection

@section('description')
    @php
        $default_error_message = "Please <a href=\"javascript:history.back()\">go back</a> or return to <a href='".url('admin')."'>the dashboard</a>.";
    @endphp
    {!! isset($exception)? ($exception->getMessage()?$exception->getMessage():$default_error_message): $default_error_message !!}
@endsection
