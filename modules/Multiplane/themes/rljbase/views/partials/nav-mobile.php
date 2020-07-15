<?php
$type = $type ?? 'main';
$types = $types ?? ['main', 'footer'];
$navs = [];
foreach($types as $t) $navs[] = mp()->getNav(null, $t);
if (empty($navs)) return;
?>

        <nav id="nav" class="{{ $class ?? 'horizontal' }}" aria-label="@lang(ucfirst($types[0]))">
            <a href="#nav" class="icon-menu"></a>
            <a class="icon-close" href="#" title="@lang('Close')" aria-label="@lang('Close')"></a>
            <a href="@base('/')" class="icon-home nav-visible-tiny" title="@lang('Home')" aria-label="@lang('Home')"></a>
            <ul>
          @foreach($navs as $k => $nav)
           @if(!empty($nav))
            @render('views:partials/nav-mobile-subnav.php', ['nav' => $nav, 'onlyMobile' => $types[$k] != $type])
           @endif
          @endforeach
            </ul>
        </nav>
