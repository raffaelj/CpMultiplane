<?php
$nav = mp()->getNav(null, $type);
if (empty($nav)) return;
$id = $type == 'main' ? ' id="nav"' : '';
$class = $class ?? 'horizontal';
?>

        <nav{{ $id }} class="{{ $class }}">
            @if($type == 'main')<a href="#nav" class="icon-menu"></a><a class="icon-close" href="#"></a>@endif
            @render('views:partials/subnav.php', ['nav' => $nav])
        </nav>
