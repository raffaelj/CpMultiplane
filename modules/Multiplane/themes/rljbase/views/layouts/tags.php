<main id="main">
    <p class="tags">
        @if(!empty($tags))
          @lang('All tags:')
          @foreach($tags as $tag)
          <a href="@base('/tags/'.urlencode($tag))" class="label tag">{{ $tag }}</a>
          @endforeach
        @else
          @lang('No tags found')
        @endif
    </p>
  @if($count)
    @if (isset($error))<p>@lang($error)</p>@endif
    <p>{{ $count }} @lang($count == 1 ? 'result' : 'results')</p>
    @foreach($list as $l)
    <div class="search-entries" data-weight="{{ $l['weight'] }}">
        <h3><a href="{{ $l['url'] }}">{{ $l['title'] }}</a></h3>
        @render('views:partials/posts-meta.php', ['post' => $l])
      @if(!empty($l['content']))
        {{ $l['content'] }}
      @endif
    </div>
    @endforeach
  @endif
</main>
