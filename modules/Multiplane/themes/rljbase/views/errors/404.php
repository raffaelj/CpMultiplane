
        <main id="main">
            <h1>@lang('Page not found')</h1>
            <p>@lang("Something went wrong. This site doesn't exist.")</p>
            <p><a href="{{ mp()->base('/') }}">@lang('Back to start')</a></p>

          @if(mp()->get('search/enabled'))
            @render('views:partials/search.php')
          @endif

        </main>
