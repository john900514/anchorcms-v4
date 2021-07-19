<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}" dir="{{ config('backpack.base.html_direction') }}">

<head>
  @include(backpack_view('inc.head'))

</head>

<body class="{{ config('backpack.base.body_class') }}">

  @include(backpack_view('inc.main_header'))

  <div class="app-body" id="app">

    @include(backpack_view('inc.sidebar'))

    <main class="main pt-2">

       @yield('before_breadcrumbs_widgets')

       @includeWhen(isset($breadcrumbs), backpack_view('inc.breadcrumbs'))

       @yield('after_breadcrumbs_widgets')

       @yield('header')

        <div class="container-fluid animated fadeIn">

          @yield('before_content_widgets')

          @yield('content')

          @yield('after_content_widgets')

        </div>

    </main>

  </div><!-- ./app-body -->

  <footer class="{{ config('backpack.base.footer_class') }}">
    @include(backpack_view('inc.footer'))
  </footer>

  @yield('before_scripts')
  @stack('before_scripts')

  @include(backpack_view('inc.scripts'))

  @yield('after_scripts')
  @stack('after_scripts')
    <script>
        $(document).ready(function () {
            console.log('Setting tooltips');
            setTimeout(function () {
                //$('[data-toggle="tooltip"]').tooltip();
            }, 1000);

            @if(\Silber\Bouncer\BouncerFacade::is(backpack_user())->a('executive', 'gm', 'trufit-rep'))
              $('.navbar-brand').empty();
              $('.navbar-brand').append('<img src="https://websales.webfdm.com/content/themes/trufit/images/logo-black.png" class="bg-logo" width="75%"/>');
            @endif
        })
    </script>

    <style>
        .app-footer div {
          color: #161c2d!important;
        }

        .app-footer a {
          color: #fff !important;
        }

        .app-header.bg-light .navbar-brand {
          width: 200px;
          justify-content: left;
          padding: 0 0 0 2rem;
          color: #73818f;
          font-size: 1.4rem;
          opacity: .2;
        }

        .fl-heading-text {
          font-family: semplicitapro, sans-serif !important;
          text-transform: uppercase;
          color: #06369e;
          font-size: 1.4rem;
          font-weight: 600;
        }
    </style>
</body>
</html>
