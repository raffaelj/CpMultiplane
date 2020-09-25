
<div class="section">
@foreach($children as $child)
    @render('views:blocks/'.$child['component'].'.php', $child)
@endforeach
</div>
