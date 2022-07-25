@extend('views:formfields/field-wrapper.php')
<?php
$attr = $field['attr'];
if (isset($attr['value'])) unset($attr['value']);
$attributes = $app->module('multiplane')->getHtmlAttributesFromArray($attr);

$value = $field['attr']['value'] ?? 1;
$checked = isset($field['value']) && $field['value'] == $value ? ' checked' : '';
?>

    @render('views:formfields/field-info.php', compact('field'))
    <input type="checkbox" value="{{ $value }}"{{ $attributes }}{{ $checked }} />
    @render('views:formfields/field-label.php', compact('field'))
