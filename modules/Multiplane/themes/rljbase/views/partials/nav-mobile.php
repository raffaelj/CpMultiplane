<?php
$type = $type ?? 'main';
$types = $types ?? ['main', 'footer'];
$navs = [];
foreach($types as $t) $navs[] = mp()->getNav(null, $t);
if (empty($navs)) return;
?>

        <nav id="nav" class="{{ $class ?? 'horizontal' }}" aria-label="@lang(ucfirst($types[0]))">
            <a href="#nav" class="icon-menu" title="@lang('Expand menu')"></a>
            <a class="icon-close" href="#" title="@lang('Hide menu')"></a>
            <a href="{{ mp()->base('/') }}" class="icon-home nav-visible-tiny" title="@lang('Home')"></a>
            <ul>
          @foreach($navs as $k => $nav)
           @if(!empty($nav))
            @render('views:partials/nav-mobile-subnav.php', ['nav' => $nav, 'onlyMobile' => $types[$k] != $type])
           @endif
          @endforeach
            </ul>
        </nav>
