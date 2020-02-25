
        <main id="main">

            <header>
                @render('views:partials/featured-media.php', ['page' => $page, 'mode' => 'image'])
                <h2>{{{ $page['title'] }}}</h2>
            </header>

            @render('views:partials/content.php', ['content' => $page['content']])

            @if (!empty($posts))
                @render('views:partials/posts.php', ['posts' => $posts])
            @endif

        </main>
