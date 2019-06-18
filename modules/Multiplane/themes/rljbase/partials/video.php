<?php

$asset = $app->storage->findOne('cockpit/assets', ['_id' => $video['asset_id']]);

$width = $asset['width'] ?? '';
$height = $asset['height'] ?? '';
$title = !empty($video['title']) ? ' title="'.$video['title'].'"' : '';

?>
<p>
@if($video['provider'] == 'youtube')
    <a href="https://www.youtube.com/watch?v={{ $video['id'] }}" data-video-id="{{ $video['id'] }}" data-video-provider="{{ $video['provider'] }}" data-video-thumb="{{ $video['asset_id'] }}" data-video-width="{{ $width }}" data-video-height="{{ $height }}"{{ $title }}>{{ $video['text'] }}</a>
@elseif($video['provider'] == 'vimeo')
    <a href="https://vimeo.com/{{ $video['id'] }}" data-video-id="{{ $video['id'] }}" data-video-provider="{{ $video['provider'] }}" data-video-thumb="{{ $video['asset_id'] }}" data-video-width="{{ $width }}" data-video-height="{{ $height }}"{{ $title }}>{{ $video['text'] }}</a>
@endif
</p>
