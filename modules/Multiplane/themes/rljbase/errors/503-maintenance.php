<!DOCTYPE html>
<html lang="{{ $app('i18n')->locale }}">

    <head>

        <meta charset="utf-8" />
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0'>
        <title>{{ $app('i18n')->get('Maintenance Mode') . ' - ' . ($site['site_name'] ?? $app['app.name']) }}</title>

        <link rel="shortcut icon" href="{{ MP_BASE_URL }}/favicon.png">
        {{ $app->assets($app['multiplane.assets.top']) }}

    </head>

    <body id="top">

        <header>
@if(!empty($site['logo']))
            <img class="logo" alt="{{ $site['logo']['title'] ?? 'logo' }}" src="@logo($site['logo']['_id'] ?? $site['logo']['path'])" />
@endif
            <h1>@lang('Maintenance Mode')</h1>
        </header>
        <main id="main">
            <p>@lang("This site is under construction.")</p>
            <p>@lang("Please try it again later.")</p>
        </main>

    </body>

</html>
