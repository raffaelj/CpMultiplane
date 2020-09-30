
<main>
    @render('views:partials/search-extended.php')
    @if (isset($error))<p>@lang($error)</p>@endif
    <p>{{ count($list) }} @lang($count == 1 ? 'result' : 'results')</p>
    @foreach($list as $l)
    <div class="search-entries" data-weight="{{ $l['weight'] }}">
        <h3><a href="{{ $l['url'] }}">{{ $l['title'] }}</a></h3>
      @if(!empty($l['collection']))
        <p><span class="label">{{ $l['collection'] }}</span></p>
      @endif
        @render('views:partials/posts-meta.php', ['post' => $l])
      @if(!empty($l['content']))
        {{ $l['content'] }}
      @endif
    </div>
    @endforeach
</main>
