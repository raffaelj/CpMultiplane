
<div class="width-small-{{ $field['width'] ?? '1-1' }}">
    {{ $content_for_layout }}
@if(!empty($field['error']))
    <p class="message error">@lang($field['error'])</p>
@endif
@if(!empty($field['link']) && isset($field['link'][mp()->slugName]))
    <p class="message info link">
    @if(!empty($field['link']['text_before']))@lang($field['link']['text_before'])@endif
    <a href="@base($field['link'][mp()->slugName])">{{ $field['link']['title'] ?? 'link' }}</a>@if(!empty($field['link']['text_after']))@lang($field['link']['text_after'])@endif
    </p>
@endif
</div>
