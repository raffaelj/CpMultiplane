<?php
// allow custom partials for different sub page collections
if (isset($_meta['posts_collection']['name'])
    && $path = $app->path("views:collections/{$_meta['posts_collection']['name']}/posts.php")) {
    $app->renderView($path);
    return;
}

if (empty($posts)) return;

$usePermalinks = mp()->usePermalinks;
$slugName      = mp()->get('fieldNames/slug');
$permalinkName = mp()->get('fieldNames/permalink');
?>

            @render('views:partials/pagination.php')

          @foreach($posts as $post){% $_url = $usePermalinks ? $app->routeUrl($post[$permalinkName]) : $app->routeUrl($pagination['posts_slug'].'/'.$post[$slugName]); %}
            <article class="excerpt">
              @if(!empty($post['title']))
                <h3><a href="{{ $_url }}">{{{ $post['title'] }}}</a></h3>
              @endif

                @render('views:partials/posts-meta.php', ['post' => $post, 'displayBreadcrumbs' => false])

                @render('views:partials/featured-media.php', ['page' => $post, 'mode' => 'image', 'format' => 'bigthumbnail'])

              @if(!empty($post['excerpt']))
                {{ $post['excerpt'] }}
              @elseif(!empty($post['content']))
                @render('views:partials/content.php', ['content' => $post['content']])
              @endif

                <p class="read_more"><a href="{{ $_url }}">@lang('read more...')</a></p>

            </article>
          @endforeach
            @render('views:partials/pagination.php')
