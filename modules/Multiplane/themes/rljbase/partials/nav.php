<?php
$nav = mp()->getNav(null, $type);
if (empty($nav)) return;
$slugName = mp()->slugName;
?>

        <nav>
            <ul>
@foreach($nav as $n)
                <li><a class="{{ $n['active'] ? 'active' : '' }}" href="@base($n[$slugName])">{{ $n['title'] }}</a></li>
@endforeach
            </ul>
        </nav>
