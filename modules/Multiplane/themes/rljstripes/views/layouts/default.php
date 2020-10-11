
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

            <div class="section">
                @render('views:partials/content.php', ['content' => $page['content']])
            </div>

            @if (!empty($posts))
                @render('views:partials/posts.php', ['posts' => $posts])
            @endif

        </main>
