<?php $attributes = cockpit('multiplane')->arrayToAttributeString($field['attr']); ?>

    <input type="text" value="{{ $field['value'] ?? '' }}"{{ $attributes }} />
    @render('views:formfields/field-label.php', compact('field'))
