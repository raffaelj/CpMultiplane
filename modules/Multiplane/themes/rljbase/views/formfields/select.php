
    <span class="form_label">@lang($field['label'] ?? $field['name'])</span>
    @render('views:formfields/field-info.php', compact('field'))
@foreach($field['options']['options'] as $option => $label)
<?php $checked = isset($field['value']) && $field['value'] == $option ? ' checked' : '';?>
    <input type="radio" name="{{ $field['name'] }}" id="{{ $field['name'].'_'.$option }}" value="{{ $option }}"{{ $checked }} />
    <label for="{{ $field['name'].'_'.$option }}">@lang($label)</label>
@endforeach
