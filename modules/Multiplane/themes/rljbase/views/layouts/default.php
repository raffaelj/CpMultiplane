
        <main id="main">
            @render('views:partials/featured-media.php', ['page' => $page, 'mode' => 'image'])

            <h2>{{{ $page['title'] }}}</h2>

          @if(mp()->collection != mp()->pages)
            @render('views:partials/posts-meta.php', ['post' => $page])
          @endif

            @render('views:partials/content.php', ['content' => $page['content']])

          @if(!empty($page['gallery']))
            @render('views:partials/gallery.php', ['gallery' => $page['gallery']])
          @endif

          @if(!empty($page['video']))
            @render('views:partials/video.php', ['video' => $page['video']])
          @endif

          @if(!empty($posts))
            @render('views:partials/posts.php', ['posts' => $posts])
          @endif

        </main>
