<?php

$description = $app->escape(!empty($page['description']) ? $page['description'] : ($site['description'] ?? ''));
$site_name = $site['site_name'] ?? $app['app.name'];
$title = (!empty($page['title']) ? $page['title'] : $site_name);

$img = $page['featured_image']['_id'] ?? $site['logo']['_id'] ?? $page['featured_image']['path'] ?? $site['logo']['path'] ?? null;
if ($img) $image = $app['site_url'].'/getImage?src='.urlencode($img).'&w=1500&h=1500';

$query_string = !empty($_SERVER['QUERY_STRING']) ? '?'.urlencode($app->escape($_SERVER['QUERY_STRING'])) : '';

/*
  to do:
  * <meta property="og:type" content="website" /> // "website" or "article"
  * twitter tags
  * multiple images are possible, e. g. page gallery or featured_image + logo
*/
?>

        <meta property="og:locale" content="{{ $app('i18n')->locale }}" />
@if($img)
        <meta property="og:image" content="{{ $image }}" />
@endif
        <meta property="og:site_name" content="{{ $site_name }}" />
        <meta property="og:title" content="{{ $title }}" />
        <meta property="og:url" content="{{ $app['site_url'] . $app['route'] . $query_string }}" />
        <meta property="og:description" content="{{ $description }}" />
