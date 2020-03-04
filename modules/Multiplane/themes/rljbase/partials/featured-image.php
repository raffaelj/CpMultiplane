<?php
$format = isset($format) && $format ? $format : 'headerimage';
$width  = mp()->get('lexy/'.$format.'/width', 800);
$height = mp()->get('lexy/'.$format.'/height', 200);
?>

@if($format == 'headerimage')
<img class="featured_image" src="@headerimage($image['_id'])" alt="{{ $image['title'] ?? 'image' }}" width="{{ $width }}" height="{{ $height }}" />
@elseif($format == 'image')
<img class="featured_image" src="@image($image['_id'])" alt="{{ $image['title'] ?? 'image' }}" width="{{ $width }}" height="{{ $height }}" />
@elseif($format == 'thumbnail')
<img class="featured_image" src="@thumbnail($image['_id'])" alt="{{ $image['title'] ?? 'image' }}" width="{{ $width }}" height="{{ $height }}" />
@elseif($format == 'bigthumbnail')
<img class="featured_image" src="@bigthumbnail($image['_id'])" alt="{{ $image['title'] ?? 'image' }}" width="{{ $width }}" height="{{ $height }}" />
@endif
