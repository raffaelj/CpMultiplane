
    <label for="{{ $field['attr']['id'] }}">
        @lang(!empty($field['label']) ? $field['label'] : ucfirst($field['name']))
        @if($field['attr']['required'] ?? false)<span class="required" title="@lang('required')"> *</span>@endif
    </label>
