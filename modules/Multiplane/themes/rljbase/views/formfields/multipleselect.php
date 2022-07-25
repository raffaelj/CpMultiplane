@extend('views:formfields/field-wrapper.php')

    @render('views:formfields/field-label.php', compact('field'))
    @render('views:formfields/field-info.php', compact('field'))

@foreach($field['options']['options'] as $option => $label)
<?php
$checked = isset($field['value']) && is_array($field['value']) && in_array($option, $field['value']) ? ' checked' : '';
$id = $field['attr']['id'].'_'.$option;
?>
    <input type="checkbox" name="{{ $field['attr']['name'] }}[]" id="{{ $id }}" value="{{ $option }}"{{ $checked }} />
    <label for="{{ $id }}">{{ $label }}</label>
@endforeach
