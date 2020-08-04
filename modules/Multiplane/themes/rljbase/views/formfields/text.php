<?php $attributes = cockpit('multiplane')->arrayToAttributeString($field['attr']); ?>

    @render('views:formfields/field-label.php', compact('field'))
    @render('views:formfields/field-info.php', compact('field'))
    <input type="text" value="{{ $field['value'] ?? '' }}"{{ $attributes }} />
