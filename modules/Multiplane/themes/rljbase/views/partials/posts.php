<?php
// allow custom partials for different sub page collections
if ($path = $app->path("views:collections/{$posts['collection']['name']}/posts.php")) {
    $app->renderView($path, $posts);
    return;
}

$usePermalinks = mp()->usePermalinks;
$slugName      = mp()->slugName;

// make $posts, $collection and $pagination available
extract($posts);
?>

            @render('views:partials/pagination.php', compact('pagination'))

          @foreach($posts as $post){% $_url = $usePermalinks ? $app->routeUrl($post[$slugName]) : $app->baseUrl($pagination['posts_slug'].'/'.$post[$slugName]); %}
            <article class="excerpt">
              @if(!empty($post['title']))
                <h3><a href="{{ $_url }}">{{{ $post['title'] }}}</a></h3>
              @endif

                @render('views:partials/posts-meta.php', compact('post'))

                @render('views:partials/featured-media.php', ['page' => $post, 'mode' => 'image', 'format' => 'bigthumbnail'])

              @if(!empty($post['excerpt']))
                {{ $post['excerpt'] }}
              @elseif(!empty($post['content']))
                {{ $post['content'] }}
              @endif

                <p class="read_more"><a href="{{ $_url }}">@lang('read more...')</a></p>

            </article>
          @endforeach
            @render('views:partials/pagination.php', compact('pagination'))
