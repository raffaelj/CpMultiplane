<?php
$breadcrumbs = mp()->breadcrumbs;
$last = count($breadcrumbs) -1;
if ($last == 0) return;
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
