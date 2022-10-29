
<main id="main">
    <section>
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
    </section>
  @if($count)
    <section>
      <p>{{ $count }} @lang($count == 1 ? 'result' : 'results')</p>
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
  @endif
</main>
