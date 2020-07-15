<?php
$nav = mp()->getNav(null, $type);
if (empty($nav)) return;
$id = $type == 'main' ? ' id="nav"' : '';
?>

        <nav{{ $id }} class="{{ $class ?? 'horizontal' }}" aria-label="@lang(ucfirst($type))">
          @if($type == 'main')
            <a href="#nav" class="icon-menu"></a><a class="icon-close" href="#" aria-label="@lang('Close')"></a>
          @endif
            @render('views:partials/nav-subnav.php', ['nav' => $nav])
        </nav>
