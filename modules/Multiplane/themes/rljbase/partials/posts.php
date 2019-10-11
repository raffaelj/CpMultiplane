<?php
$path = $app->path('views:posts/'.$posts['collection']['name'].'.php');

if (!$path) $path = 'views:posts/posts.php';

$app->renderView($path, $posts);
