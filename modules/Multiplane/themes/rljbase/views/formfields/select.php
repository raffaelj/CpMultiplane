@extend('views:formfields/field-wrapper.php')
    @render('views:formfields/field-label.php', compact('field'))
    @render('views:formfields/field-info.php', compact('field'))
@foreach($field['options']['options'] as $option => $label)
<?php
$checked = isset($field['value']) && $field['value'] == $option ? ' checked' : '';
$id = $field['attr']['id'].'_'.$option;
$required = isset($field['attr']['required']) && $field['attr']['required'] ? ' required' : '';
?>
    <input type="radio" name="{{ $field['attr']['name'] }}" id="{{ $id }}" value="{{ $option }}"{{ $checked }}{{ $required }} />
    <label for="{{ $id }}">{{ $label }}</label>
@endforeach
