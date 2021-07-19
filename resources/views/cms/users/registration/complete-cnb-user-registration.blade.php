@extends('backpack::layouts.animated-bg')

@section('header')
    <div class="logo-header-section">
        <div class="inner-logo-header">
            <img src=""/>
        </div>
    </div>
@endsection

@section('content')
    <div class="" id="app">
        <video-bg video-url="https://player.vimeo.com/video/447175009?title=0&portrait=0&muted=1&autopause=0&loop=1&background=1&app_id=122963"
                  :video-opacity="0.1"
        ></video-bg>

        <div class="col-md-12 col-md-offset-12">
            <div class="title m-b-md">
                <img src="https://amchorcms-assets.s3.amazonaws.com/anchorCMSLogo.png">
            </div>

            <h3 class="text-center m-b-20">Hi, {!! $user->name !!}!</h3>
            <div class="text-center m-b-20 intro-bar">
                <p class="text-center instructions-text">Verify the Details of Your New <b>{!! $role !!}</b> Account Below:</p>
            </div>
            <div class="nav-tabs-custom">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        @if (session('status'))
                            <div class="alert alert-danger">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form class="col-md-12 p-t-10" role="form" method="POST" action="/registration">
                            {!! csrf_field() !!}

                            <input type="hidden" value="{!! $user->id !!}" name="session_token"/>

                            <div class="deets-group">
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <div class="inner-form-group">
                                        <label class="control-label">{{ trans('backpack::base.email_address') }}</label>
                                        <div>
                                            <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>

                                            @if ($errors->has('email'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <div class="inner-form-group">
                                        <label class="control-label">Name</label>
                                        <div>
                                            <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>

                                            @if ($errors->has('name'))
                                                <span class="help-block">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="control-label">{{ trans('backpack::base.new_password') }}</label>

                                <div>
                                    <input type="password" class="form-control" name="password" required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label class="control-label">{{ trans('backpack::base.confirm_new_password') }}</label>
                                <div>
                                    <input type="password" class="form-control" name="password_confirmation" required>

                                    @if ($errors->has('password_confirmation'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div>
                                    <button type="submit" class="btn btn-block btn-primary">
                                        Complete My Registration
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="clearfix"></div>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
        </div>
    </div>
@endsection

@section('before_scripts')
    <?php session()->forget('status'); ?>
    <style>
        .app {
            flex-direction: column !important;
            background-color: #000;
            color: #fff;
        }

        label {
            color: #000;
        }

        .inner-logo-header {
            text-align: center;
        }

        .title {
            width: 100%;
            text-align: center;
        }

        @media screen and (max-width: 999px) {
            .inner-logo-header img {
                width: 25%;
            }

            .title img {
                width: 75%;
                margin: 5% 0 2.5%;
            }
        }

        @media screen and (min-width: 1000px) {
            .inner-logo-header img {
                width: 35%;
            }

            .title img {
                width: 50%;
                margin: 5% 0 2.5%;
            }
        }

    </style>
@endsection
