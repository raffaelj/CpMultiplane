<?php $count = count($columns); ?>

<div class="grid">
@foreach($columns as $col)
    <div class="width-medium-1-{{ $count }}">
    @foreach($col['children'] as $child)
        @render('views:blocks/'.$child['component'].'.php', $child)
    @endforeach
    </div>
@endforeach
</div>
