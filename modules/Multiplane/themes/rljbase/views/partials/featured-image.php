<?php
$format = isset($format) && $format ? $format : 'headerimage';
$width  = mp()->get('lexy/'.$format.'/width', 0);
$height = mp()->get('lexy/'.$format.'/height', 0);
?>

<img class="featured_image" src="{{ mp()->imageUrl($image, $format) }}" alt="{{ $image['alt'] ?? $image['title'] ?? '' }}"{{ $width ? ' width="'.$width.'"' : '' }}{{ $height ? ' height="'.$height.'"' : '' }} />
