<?php

$asset = $app->storage->findOne('cockpit/assets', ['_id' => $video['asset_id']]);

if (!$asset || !isset($asset['video_provider']) || !isset($asset['video_id'])) return;

$width    = $asset['width'] ?? '';
$height   = $asset['height'] ?? '';
$title    = !empty($video['title']) ? ' title="' . $video['title'] . '"' : '';
$provider = $asset['video_provider'];

if ($provider == 'youtube') {
    $src = 'https://www.youtube-nocookie.com/embed/' . $asset['video_id'] . '?rel=0&showinfo=0&autoplay=1';
}
if ($provider == 'vimeo') {
    $src = 'https://player.vimeo.com/video/'. $asset['video_id'] . '?color=ffffff&title=0&byline=0&portrait=0&autoplay=1';
}

?>
<div class="video_embed_container">
    <iframe class="video_embed" src="about:blank" data-src="{{ $src }}" allowfullscreen="" style="width: {{ $width }}px; height: {{ $height }}px; background-image: url('@route('/getImage')?src={{ $asset['_id'] }}&w=480&o=1');" width="{{ $width }}" height="{{ $height }}"></iframe>
    <a href="#" class="icon-play"></a>
@if($video['provider'] == 'youtube')
    <a href="https://www.youtube.com/watch?v={{ $video['id'] }}" data-video-id="{{ $video['id'] }}" data-video-provider="{{ $video['provider'] }}" data-video-thumb="{{ $video['asset_id'] }}" data-video-width="{{ $width }}" data-video-height="{{ $height }}"{{ $title }}>{{ $video['text'] }}</a>
@elseif($video['provider'] == 'vimeo')
    <a href="https://vimeo.com/{{ $video['id'] }}" data-video-id="{{ $video['id'] }}" data-video-provider="{{ $video['provider'] }}" data-video-thumb="{{ $video['asset_id'] }}" data-video-width="{{ $width }}" data-video-height="{{ $height }}"{{ $title }}>{{ $video['text'] }}</a>
@endif
</div>
