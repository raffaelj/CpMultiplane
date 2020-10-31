
        <main id="main">
          @if(mp()->displayBreadcrumbs)
            @render('views:partials/breadcrumbs.php', ['page' => $page])
          @endif

            @render('views:partials/posts-meta.php', ['post' => $page])

            @render('views:partials/content.php', ['content' => $page['content']])

          @if(!empty($page['gallery']))
            @render('views:partials/gallery.php', compact('page'))
          @endif

          @if(!empty($page['video']))
            @render('views:partials/video.php', ['video' => $page['video']])
          @endif

          @if(!empty($posts))
            @render('views:partials/posts.php', ['posts' => $posts, 'pagination' => $posts['pagination']])
          @endif
        </main>

