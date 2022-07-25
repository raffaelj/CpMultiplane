{% $attributes = cockpit('multiplane')->getHtmlAttributesFromArray($field['attr']); %}

    <input type="checkbox"{{ $attributes }} />
