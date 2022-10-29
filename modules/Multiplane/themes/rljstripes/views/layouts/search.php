
<main id="main">
    <section>
    @render('views:partials/search-extended.php')
        @if (isset($error))<p>@lang($error)</p>@endif
        <p>{{ count($list) }} @lang($count == 1 ? 'result' : 'results')</p>
    </section>
    @foreach($list as $l)
    <article class="search-entries" data-weight="{{ $l['weight'] }}">
        <h3><a href="{{ $l['url'] }}">{{ $l['title'] }}</a></h3>
        @render('views:partials/posts-meta.php', ['post' => $l])
      @if(!empty($l['content']))
        {{ $l['content'] }}
      @endif
    </article>
    @endforeach
</main>
