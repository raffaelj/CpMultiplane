<?php
// default view, if page type detection is "type" and the current type is "post"

$width  = mp()->get('lexy/headerimage/width', 800)  . 'px';
$height = mp()->get('lexy/headerimage/height', 200) . 'px';
?>

        <main id="main">
          @if(!empty($page['image']))
            <img class="featured_image" src="@headerimage($page['image']['_id'])" alt="{{ $page['image']['title'] ?? 'image' }}" width="{{ $width }}" height="{{ $height }}" />
          @elseif(!empty($page['featured_image']))
            <img class="featured_image" src="@headerimage($page['featured_image']['_id'])" alt="{{ $page['featured_image']['title'] ?? 'image' }}" width="{{ $width }}" height="{{ $height }}" />
          @endif

            <h2>{{{ $page['title'] }}}</h2>

            @render('views:partials/posts-meta.php', ['post' => $page])

            {{ $page['content'] }}

          @if(!empty($page['gallery']))
            @render('views:partials/gallery.php', compact('page'))
          @endif
          @if(!empty($page['video']))
            @render('views:partials/video.php', ['video' => $page['video']])
          @endif
          @if (!empty($posts))
            @render('views:partials/posts.php', ['posts' => $posts['posts'], 'pagination' => $posts['pagination']])
          @endif
        </main>

