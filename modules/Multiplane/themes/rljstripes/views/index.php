<!DOCTYPE html>
<html lang="{{ $app('i18n')->locale }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        @render('views:partials/seometa.php')
        <link rel="shortcut icon" href="{{ MP_BASE_URL }}/favicon.png?ver={{ mp()->version }}">
        <script>
            var MP_BASE_URL = '{{ MP_BASE_URL }}',
                MP_POLYFILLS_URL = '{{ MP_BASE_URL }}/modules/Multiplane/themes/rljbase/assets/js/polyfills.min.js';
        </script>
        {{ mp()->assets($app['multiplane.assets.top'], mp()->version) }}
        {{ mp()->userStyles() }}
        @trigger('multiplane.head')

    </head>
    <body id="top" class="{{ !empty($page['class']) ? $page['class'] : '' }}">
        @trigger('multiplane.layout.contentbefore')

        <header>
            <a href="{{ mp()->base('/') }}">
              @if(!empty($site['logo']))
                <img class="logo" alt="{{ $site['logo']['title'] ?? 'logo' }}" src="@logo($site['logo']['_id'] ?? $site['logo']['path'])" title="@lang('back to start page')" />
              @endif
                <h1>{{{ $site['site_name'] ?? $app['app.name'] }}}</h1>
            </a>
          @if(mp()->isMultilingual)
            @render('views:partials/language-switch.php')
          @endif

            @render('views:partials/nav-mobile.php', ['type' => 'main'])

          @if(mp()->get('search/enabled'))
            @render('views:partials/search.php')
          @endif
        </header>

        {{ $content_for_layout }}

      @if(isset($page['contactform']['active']) && $page['contactform']['active'] == true)
        {{ $app->helper('form')->form($options['form'] ?? mp()->contact, $page['contactform']) }}
      @endif

        <footer>
            @render('views:partials/nav.php', ['type' => 'footer'])
            @render('views:partials/copyright.php')
            @render('views:partials/login-link.php')
        </footer>
        @trigger('multiplane.layout.contentafter')

        @render('views:partials/privacy-notice.php')
        {{ mp()->assets($app['multiplane.assets.bottom'], mp()->version) }}
        {{ mp()->userScripts() }}
    </body>
</html>
