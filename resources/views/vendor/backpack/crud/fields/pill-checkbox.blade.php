<!-- checkbox field -->

@include('crud::fields.inc.wrapper_start')
    @include('crud::fields.inc.translatable_icon')
    <div class="checkbox">
        <label class="form-check-label font-weight-normal"  style="margin-right: 1rem;">{!! $field['left-label'] !!}</label>
        <label class="switch switch-label switch-pill switch-success">
            <input type="hidden" name="{{ $field['name'] }}" value="{{ old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? 0 }}">
            <input type="checkbox" class="switch-input" data-init-function="bpFieldInitCheckbox" style="margin-top: 5rem"
           @if (old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? false)
                   checked="checked"
            @endif
          >
            <span class="switch-slider" data-checked="On" data-unchecked="Off"
            @if (isset($field['attributes']))
                @foreach ($field['attributes'] as $attribute => $value)
                    {{ $attribute }}="{{ $value }}"
                @endforeach
            @endif
            ></span>
        </label>
        <label class="form-check-label font-weight-normal" style="margin-left: 1rem;">{!! $field['label'] !!}</label>
        {{-- HINT --}}
        @if (isset($field['hint']))
            <p class="help-block" style="margin-left: 1.5rem">{!! $field['hint'] !!}</p>
        @endif
    </div>
@include('crud::fields.inc.wrapper_end')

{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->fieldTypeNotLoaded($field))
    @php
        $crud->markFieldTypeAsLoaded($field);
    @endphp
    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <script>
            function bpFieldInitCheckbox(element) {
                var hidden_element = element.siblings('input[type=hidden]');
                var id = 'checkbox_'+Math.floor(Math.random() * 1000000);

                // make sure the value is a boolean (so it will pass validation)
                if (hidden_element.val() === '') hidden_element.val(0);

                // set unique IDs so that labels are correlated with inputs
                element.attr('id', id);
                element.siblings('label').attr('for', id);

                // set the default checked/unchecked state
                // if the field has been loaded with javascript
                if ((hidden_element.val() != 0) && (hidden_element.val() != undefined)) {
                  element.prop('checked', 'checked');
                } else {
                  element.prop('checked', false);
                }

                // when the checkbox is clicked
                // set the correct value on the hidden input
                element.change(function() {
                    console.log('Hidden Elem - ',hidden_element)
                  if (element.is(":checked")) {
                    hidden_element.val(1);
                  } else {
                    hidden_element.val(0);
                  }
                })
            }
        </script>
    @endpush

@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
