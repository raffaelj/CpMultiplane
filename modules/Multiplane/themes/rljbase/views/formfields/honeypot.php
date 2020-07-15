<?php
$prefix = cockpit('multiplane')->formIdPrefix;

$field['attr']['id']   = $prefix . $field['attr']['id'];
$field['attr']['name'] = $prefix . $field['attr']['name'];

$attributes = cockpit('multiplane')->arrayToAttributeString($field['attr']);
?>

    <input type="checkbox"{{ $attributes }} />
