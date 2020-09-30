
                <p class="posts-meta">
                  @if(!empty($post['_created']))
                    <time class="date created" title="@lang('creation date')" datetime="{{ date('Y-m-d H:i', $post['_created']) }}">{{ date($app('i18n')->get('@dateformat_long', 'Y-m-d H:i'), $post['_created']) }}</time>
                  @endif
                  @if(!empty($post['tags']))
                    @foreach($post['tags'] as $tag)
                    <a href="@base('/tags/'.urlencode($tag))" class="label tag">{{ $tag }}</a>
                    @endforeach
                  @endif
                </p>
