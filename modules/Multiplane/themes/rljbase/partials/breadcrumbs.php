<?php
$breadcrumbs = cockpit('multiplane')->breadcrumbs;
$last = count($breadcrumbs) -1;
?>

<nav class="breadcrumbs">
    <ul>
@foreach($breadcrumbs as $k => $n)
        <li>
@if($k < $last)
        <a href="@base('/'.$n)">{{ $n }}</a>
@else
        <span>{{ $n }}</span>
@endif
        </li>
@endforeach
    </ul>
</nav>
