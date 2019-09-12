
<main>
    @render('views:partials/search.php')
    @if (isset($error))<p>@lang($error)</p>@endif
    <p>{{ count($list) }} @lang($count == 1 ? 'result' : 'results')</p>
    @foreach($list as $l)
    <div class="search-entries">
        <h3><a href="{{ $l['url'] }}">{{ $l['title'] }}</a></h3>
        <p><span class="label">{{ $l['collection'] }}</span></p>
        @if(!empty($l['content']))
        {{ $l['content'] }}
        @endif
    </div>
    @endforeach
</main>
