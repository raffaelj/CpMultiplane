
    @render('views:formfields/field-label.php', compact('field'))
    @render('views:formfields/field-info.php', compact('field'))
@foreach($field['options']['options'] as $option => $label)
{% $checked = isset($field['value']) && is_array($field['value']) && in_array($option, $field['value']) ? ' checked' : ''; %}
{% $id = $field['attr']['id'].'_'.$option; %}
{{-- {% $required = isset($field['attr']['required']) && $field['attr']['required'] ? ' required' : ''; %}
required doesn't work with checkboxes (without JS) --}}
    <input type="checkbox" name="{{ $field['attr']['name'] }}[]" id="{{ $id }}" value="{{ $option }}"{{ $checked }} />
    <label for="{{ $id }}">{{ $label }}</label>
@endforeach
