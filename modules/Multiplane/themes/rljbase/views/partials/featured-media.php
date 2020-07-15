<?php
if (!isset($mode))     $mode = 'image';
if ($mode == 'slider') $mode = 'carousel';
$format = isset($format) && $format ? $format : 'headerimage';

if ($mode == 'image') {

    $image = !empty($page['featured_image']) ? $page['featured_image']
             : (!empty($page['image']) ? $page['image'] : false);

    if (!$image) return;

    $app->renderView('views:partials/featured-image.php', compact('image', 'format'));

    return;

}

if ($mode == 'carousel') {

    $carousel = isset($carousel) ? $carousel
                : (!empty($page['carousel']) ? $page['carousel']
                    : (!empty($page['slider']) ? $page['slider'] : false));

    if (!$carousel) return;

    $app->renderView('views:partials/carousel.php', compact('carousel', 'format'));

    return;

}

// to do: other possible formats, e. g. video...
?>
