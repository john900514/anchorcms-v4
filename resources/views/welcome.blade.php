<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description" content="AnchorCMS">
    <meta name="author" content="Cape&Bay \\ capeandbay.com">
    <meta name="keywords" content="anchor, anchorcms, cms, clients, capeandbay">

    <meta property="og:title" content="Anchor CMS">
    <meta property="og:description" content="AnchorCMS">

    <meta property="og:image" content="https://capeandbay.com/wp-content/uploads/2019/11/cropped-favicon-180x180.png">
    <meta property="og:url" content="{!! env('APP_URL') !!}">
    <meta property="og:video" content="https://i.vimeocdn.com/video/945480185.webp" />
    <meta property="og:video:type" content="video" />
    <meta property="og:video:width" content="1280" />
    <meta property="og:video:height" content="720" />

    <link rel="icon" href="https://capeandbay.com/wp-content/uploads/2019/11/cropped-favicon-32x32.png" sizes="32x32">
    <link rel="icon" href="https://capeandbay.com/wp-content/uploads/2019/11/cropped-favicon-192x192.png" sizes="192x192">
    <link rel="apple-touch-icon-precomposed" href="https://capeandbay.com/wp-content/uploads/2019/11/cropped-favicon-180x180.png">

    <title>{!! env('APP_NAME') !!}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link href="{!! asset('css/app.css') !!}" rel="stylesheet" />
    <link href="https://amchorcms-assets.s3.amazonaws.com/backpack_packages/@digitallyhappy/backstrap/css/style.min.css" rel="stylesheet" />
    <!-- Styles -->
    <style>
        html, body {
            background: rgb(3,26,69);
            /*background: linear-gradient(138deg, rgba(3,26,69,0.85) 0%, rgba(1,44,128,0.85) 28%, rgba(6,54,158,0.85) 50%, rgba(223,242,0,0.85) 83%, rgba(194,210,0,0.85) 100%);*/
            background: linear-gradient(138deg, rgba(194,210,0,0.85) 0%, rgba(223,242,0,0.85) 28%, rgba(6,54,158,0.85) 50%, rgba(1,44,128,0.85) 83%, rgba(3,26,69,0.85) 100%);
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .sweet-modal {
            background: rgba(1,44,128,0.85) 83% !important;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
            height: 90%;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .top-left {
            position: absolute;
            left: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        img {
            width: 75%;
        }

        .links > a {
            color: white;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji" !important;
        }

        .m-b-md {
            margin-bottom: 30px;
        }

        footer {
            width: 100%;
            height: 5%;
        }

        .inner-footer {
            text-align: center;
        }

        footer small {
            color: #fff;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji" !important;
            font-weight: 700;
        }

        .login-button {
            height: 3rem;
            width: 3rem;
            background-color: transparent;
            border: none;
        }

        .login-button i {
            color: #fff;
            font-size: 25px;
        }
    </style>
</head>
<body>

<div class="flex-center position-ref full-height" id="app">
    <welcome
        show-forgot-password="{!! '1' !!}"
        :show-registration="{{ config('backpack.base.registration_open') ? config('backpack.base.registration_open') : 'false' }}"
        forgot-password-url="{!! route('backpack.auth.password.reset') !!}"
        registration-url="{{ route('backpack.auth.register') }}"
        login-url="{{ route('backpack.auth.login') }}"
        csrf-field="{!! csrf_token() !!}"
    ></welcome>
</div>

<footer>
    <div class="inner-footer">
        <small><i class="fal fa-copyright"></i>2020. Cape & Bay. All Rights Reserved. </small>
        <br />
        <small>v.{!! env('APP_VERSION') !!}| Build {!! env('APP_BUILD') !!}</small>
    </div>
</footer>
</body>

<script src="{!! asset('/js/app.js') !!}"></script>
</html>
