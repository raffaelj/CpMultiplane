<?php
if (!isset($carousel) || !is_array($carousel) || !isset($carousel[0])) return;

$count = count($carousel);

$format = isset($format) && $format ? $format : 'headerimage';

if (count($carousel) == 1) {

    $image = $carousel[0]['meta']['asset'];
    if (!empty($carousel[0]['meta']['title'])) {
        $image['title'] = $carousel[0]['meta']['title'];
    }

    $app->renderView('views:partials/featured-image.php', compact('image', 'format'));
    return;
}

$width  = mp()->get('lexy/'.$format.'/width', 800)  . 'px';
$height = mp()->get('lexy/'.$format.'/height', 200) . 'px';

// $empty = 'data:,'; // seems to work, but it displays the alt text
// instead: empty gif pixel
$empty = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

// to do: inline css is bad, but I need a dynamic height...
?>

<aside class="carousel" style="height:{{ $height }}" tabindex="0">

  @foreach($carousel as $k => $image)
    <img class="{{ $k == 0 ? 'current' : '' }}" src="@if($k == 0)@headerimage($image['meta']['asset'])@else{{$empty}}@endif" data-src="@headerimage($image['meta']['asset'])" alt="{{ !empty($image['meta']['title']) ? $image['meta']['title'] : 'image' }}" width="{{ $width }}" height="{{ $height }}" />
  @endforeach

</aside>
