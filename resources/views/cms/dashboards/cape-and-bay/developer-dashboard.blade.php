@extends(backpack_view('blank'))

@section('after_styles')
    <style media="screen">
        .backpack-profile-form .required::after {
            content: ' *';
            color: red;
        }
    </style>
@endsection

@php
    $breadcrumbs = $breadcrumbs ?? [
        trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
        'Secrets Vault' => false,
    ];
@endphp

@section('header')
    <section class="content-header">
        <div class="container-fluid mb-3">
            <h1>{!! $section_headers['title'] !!}</h1>
            <small>{!! $section_headers['subtitle'] !!}</small>
        </div>
    </section>
@endsection

@section('content')
    <div class="col-md-12 row">

    </div>
@endsection
