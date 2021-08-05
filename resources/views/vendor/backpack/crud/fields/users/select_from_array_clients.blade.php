@php
    $field['allows_null'] = $field['allows_null'] ?? $crud->model::isColumnNullable($field['name']);
@endphp
<!-- select from array -->
@include('crud::fields.inc.wrapper_start')
@include('crud::fields.inc.translatable_icon')
<user-client-select
        label="{!! $field['label'] !!}"
        name="{!! $field['name'] !!}"
        :options="{{ json_encode($field['options']) }}"
        value="{!! $field['value'] ?? '' !!}"
        :attrs="{{ json_encode($field['attributes'] ?? []) }}"
></user-client-select>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')

@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
        <!-- include select2 css-->
        <link href="{{ asset('https://amchorcms-assets.s3.amazonaws.com/backpack_packages/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('https://amchorcms-assets.s3.amazonaws.com/backpack_packages/select2-bootstrap-theme/dist/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <script src="https://amchorcms-assets.s3.amazonaws.com/backpack_packages/select2/dist/js/select2.full.min.js"></script>
    @endpush
@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
