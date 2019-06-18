<?php
$nav = cockpit('multiplane')->getNav(null, $type);
if (empty($nav)) return;
$slugName = cockpit('multiplane')->slugName;
?>

        <nav>
            <ul>
@foreach($nav as $n)
                <li><a class="" href="@base($n[$slugName])">{{ $n['title'] }}</a></li>
@endforeach
            </ul>
        </nav>
