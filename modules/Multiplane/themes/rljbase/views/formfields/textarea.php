@extend('views:formfields/field-wrapper.php')

{% $attributes = $app->module('multiplane')->getHtmlAttributesFromArray($field['attr']) %}

    @render('views:formfields/field-label.php', compact('field'))
    @render('views:formfields/field-info.php', compact('field'))
    <textarea{{ $attributes }}>{{ $field['value'] ?? '' }}</textarea>
