
        <main id="main">

            <header>
                @render('views:partials/featured-media.php', ['page' => $page, 'mode' => 'image'])
                <h2>{{{ $page['title'] }}}</h2>
            </header>

          @if(mp()->collection != mp()->pages)
            <section>
                @render('views:partials/posts-meta.php', ['post' => $page])
            </section>
          @endif

          @if(is_array($page['content']))
            @render('views:fields/repeater.php', ['content' => $page['content']])
          @else
            <div class="section">
                {{ $page['content'] }}
            </div>
          @endif

            @if (!empty($posts))
                @render('views:partials/posts.php', ['posts' => $posts])
            @endif

        </main>
