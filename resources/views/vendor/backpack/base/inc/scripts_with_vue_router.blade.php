@if (config('backpack.base.scripts') && count(config('backpack.base.scripts')))
    @foreach (config('backpack.base.scripts') as $path)
        @if($path == 'js/app.js')
            @if (app()->environment('local'))
            <script type="text/javascript" src="{{ asset('js/router-app.js').'?v='.config('backpack.base.cachebusting_string') }}"></script>
            @else
            <script type="text/javascript" src="{{ mix('js/router-app.js').'?v='.config('backpack.base.cachebusting_string') }}"></script>
            @endif
        @else
        <script type="text/javascript" src="{{ asset($path).'?v='.config('backpack.base.cachebusting_string') }}"></script>
        @endif
    @endforeach
@endif

@if (config('backpack.base.mix_scripts') && count(config('backpack.base.mix_scripts')))
    @foreach (config('backpack.base.mix_scripts') as $path => $manifest)
        @if($path == 'js/app.js')
        <script type="text/javascript" src="{{ mix('js/router-app.js').'?v='.config('backpack.base.cachebusting_string') }}"></script>
        @else
        <script type="text/javascript" src="{{ mix($path, $manifest) }}"></script>
        @endif
    @endforeach
@endif

@include('backpack::inc.alerts')

<!-- page script -->
<script type="text/javascript">
    // To make Pace works on Ajax calls
    $(document).ajaxStart(function() { Pace.restart(); });

    // Ajax calls should always have the CSRF token attached to them, otherwise they won't work
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    {{-- Enable deep link to tab --}}
    var activeTab = $('[href="' + location.hash.replace("#", "#tab_") + '"]');
    location.hash && activeTab && activeTab.tab('show');
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        location.hash = e.target.hash.replace("#tab_", "#");
    });
</script>
