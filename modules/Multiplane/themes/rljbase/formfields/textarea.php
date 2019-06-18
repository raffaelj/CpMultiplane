<?php $attributes = cockpit('multiplane')->arrayToAttributeString($field['attr']); ?>

    <textarea{{ $attributes }}>{{ $field['value'] ?? '' }}</textarea>
    @render('views:formfields/field-label.php', compact('field'))
