
<div class="width-small-{{ $field['width'] ?? '1-1' }}">
    {{ $content_for_layout }}

@if(!empty($field['error']))
    <p class="message error" id="{{ $field['aria']['error'] }}"><i class="icon-close"></i>@lang($field['error'])</p>
@endif

@if(!empty($field['link']) && isset($field['link'][mp()->slugName]))
    <p class="message info link" id="{{ $field['aria']['link'] }}">
    @if(!empty($field['link']['text_before'])){{ $field['link']['text_before'] }}@endif
    <a href="@base($field['link'][mp()->slugName])">{{ $field['link']['title'] ?? 'link' }}</a>@if(!empty($field['link']['text_after'])){{ $field['link']['text_after'] }}@endif
    </p>
@endif

</div>
