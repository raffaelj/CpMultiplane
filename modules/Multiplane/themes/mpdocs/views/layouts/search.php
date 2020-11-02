
<main>
    <h1>@lang('Search')</h1>
    @render('views:partials/search-extended.php')
    @if (isset($error))<p>@lang($error)</p>@endif
    <p>{{ count($list) }} @lang($count == 1 ? 'result' : 'results')</p>
    @foreach($list as $l)
    <article class="search-entries" data-weight="{{ $l['weight'] }}">
        <div class="search-entries-header">
            <a class="heading" href="{{ $l['url'] }}">{{ $l['_title'] ?? $l['title'] }}</a>
            @render('views:partials/posts-meta.php', ['post' => $l, 'displayBreadcrumbs' => false])
        </div>
      @if(!empty($l['content']))
        {{ $l['content'] }}
      @endif
    </article>
    @endforeach
</main>
