<?php
$attributes = cockpit('multiplane')->arrayToAttributeString($field['attr']);
?>

    <input type="checkbox"{{ $attributes }} />
