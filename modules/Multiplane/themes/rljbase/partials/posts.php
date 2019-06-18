<?php
$width  = $app->retrieve('monoplane/lexy/thumbnail/width', 100)  . 'px';
$height = $app->retrieve('monoplane/lexy/thumbnail/height', 100) . 'px';

$slugName = cockpit('multiplane')->slugName;
?>

            @render('views:partials/pagination.php', compact('pagination'))
@foreach($posts as $post)
            <article class="excerpt">
                @if(!empty($post['title']))
                <h3> <a href="@base($pagination['slug'].'/'. ($post[$slugName] ?? $post['_id']))">{{ $post['title'] }}</a></h3>
                @endif
                
                <p><span class="date">{{ date('Y-m-d H:i', $post['_created']) }}</span></p>

                @if(!empty($post['image']))
                <img class="featured_image" src="@thumbnail($post['image']['_id'])" alt="{{ $post['image']['title'] ?? 'image' }}" width="{{ $width }}" height="{{ $height }}" />
                @elseif(!empty($post['featured_image']))
                <img class="featured_image" src="@thumbnail($post['featured_image']['_id'])" alt="{{ $post['featured_image']['title'] ?? 'image' }}" width="{{ $width }}" height="{{ $height }}" />
                @endif

                @if(!empty($post['excerpt']))
                {{ $post['excerpt'] }}
                @elseif(!empty($post['content']))
                {{ $post['content'] }}
                @endif

                <p class="read_more"><a href="@base($pagination['slug'].'/'. ($post[$slugName] ?? $post['_id']))">@lang('read more...')</a></p>

            </article>
@endforeach
            @render('views:partials/pagination.php', compact('pagination'))
