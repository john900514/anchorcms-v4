@extends(backpack_view('blank_with_vue_router'))

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
            <h1>Asset Secrets Vault</h1>
            <small>Powered by 1Password</small>
        </div>
    </section>
@endsection

@section('content')
<div class="col-md-12 row">
    <secret-vault
        auth-token="{!! $auth_token !!}"
        vault-token="{!! $vault_token !!}"
        session="{!! $session ?? 'not_logged_in' !!}"
    ></secret-vault>
</div>
@endsection
