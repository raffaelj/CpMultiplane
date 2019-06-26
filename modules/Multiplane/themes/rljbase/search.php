
<main>
    @render('views:partials/search.php')
    @if (isset($error))<p>@lang($error)</p>@endif
    <p>{{ count($list) }} @lang('results')</p>
    @foreach($list as $l)
    <div class="search-entries">
        <h3><a href="{{ $l['url'] }}">{{ $l['title'] }}</a></h3>
        {{ $l['content'] }}
    </div>
    @endforeach
</main>
