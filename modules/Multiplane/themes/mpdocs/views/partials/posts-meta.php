
                <p class="posts-meta">
                  @if(!empty($post['tags']))
                    <span>
                    @foreach($post['tags'] as $tag)
                    <a href="@base('/tags/'.urlencode($tag))" class="label tag">{{ $tag }}</a>
                    @endforeach
                    </span>
                  @endif
                  @if(!empty($post['_created']))
                    <span title="{{ date($app('i18n')->get('@dateformat_long', 'Y-m-d H:i'), $post['_created']) }}">@lang('created'): <time class="date created" datetime="{{ date('Y-m-d H:i', $post['_created']) }}">{{ date($app('i18n')->get('@dateformat', 'Y-m-d'), $post['_created']) }}</time></span>
                  @endif
                  @if(!empty($post['_modified']) && $post['_modified'] != $post['_created'])
                    <span title="{{ date($app('i18n')->get('@dateformat_long', 'Y-m-d H:i'), $post['_modified']) }}">@lang('modified'): <time class="date created" datetime="{{ date('Y-m-d H:i', $post['_created']) }}">{{ date($app('i18n')->get('@dateformat', 'Y-m-d'), $post['_modified']) }}</time></span>
                  @endif
                  @if(getenv('MPDOCS_ENVIRONMENT') === 'DEVELOPMENT')
                    <span><a href="@route('/'.MP_ADMINFOLDER.'/collections/entry/pages/'.$post['_id'])">edit</a></span>
                  @endif
                </p>
