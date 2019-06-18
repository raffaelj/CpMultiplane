<?php
$attributes = cockpit('multiplane')->arrayToAttributeString($field['attr']);
$value = $field['attr']['value'] ?? 1;
$checked = isset($field['value']) && $field['value'] == $value ? ' checked' : '';
?>

    <input type="checkbox" value="{{ $value }}"{{ $attributes }}{{ $checked }} />
    @render('views:formfields/field-label.php', compact('field'))
