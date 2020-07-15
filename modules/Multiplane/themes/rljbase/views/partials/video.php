<?php

$asset = $app->storage->findOne('cockpit/assets', ['_id' => $video['asset_id']]);

if (!$asset || !isset($asset['video_provider']) || !isset($asset['video_id'])) return;

$width    = $asset['width'] ?? '';
$height   = $asset['height'] ?? '';
$ratio    = $width / $height == 16 / 9 ? '16-9' : '4-3';

$title    = !empty($video['title']) ? ' title="' . $video['title'] . '"' : '';
$provider = $asset['video_provider'];

if ($provider == 'youtube') {

    // downloaded thumbnails are always 4:3 (640px x 480px) with black borders
    // lazy fix: overwrite ratio
    $ratio = '16-9';

    $src = 'https://www.youtube-nocookie.com/embed/' . $asset['video_id'] . '?enablejsapi=1&rel=0&showinfo=0&autoplay=1';
}
if ($provider == 'vimeo') {
    $src = 'https://player.vimeo.com/video/'. $asset['video_id'] . '?color=ffffff&title=0&byline=0&portrait=0&autoplay=1';
}

?>
<div class="video_embed_container ratio-{{$ratio}}">
    <iframe class="video_embed" src="about:blank" data-src="{{ $src }}" data-provider="{{ $provider }}" style="background-image: url('@route('/getImage')?src={{ $asset['_id'] }}&w=480&o=1');" width="{{ $width }}" height="{{ $height }}" allow="autoplay; fullscreen" allowfullscreen="" title="@lang('Video')"></iframe>
    <a href="#" class="icon-play" aria-label="@lang('Play')"></a>
</div>
@if($video['provider'] == 'youtube')
    <a href="https://www.youtube.com/watch?v={{ $video['id'] }}"{{ $title }}>{{ $video['text'] }}</a>
@elseif($video['provider'] == 'vimeo')
    <a href="https://vimeo.com/{{ $video['id'] }}"{{ $title }}>{{ $video['text'] }}</a>
@endif
