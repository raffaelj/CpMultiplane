{% $dateformat_long = $app('i18n')->get('@dateformat_long', 'Y-m-d H:i'); %}
{% $dateformat      = $app('i18n')->get('@dateformat', 'Y-m-d'); %}

                <div class="posts-meta">
                  @if(mp()->displayBreadcrumbs && (!isset($displayBreadcrumbs) || $displayBreadcrumbs))
                    @render('views:partials/breadcrumbs.php', ['page' => $post])
                  @endif
                  @if(!empty($post['tags']))
                    <span>
                    @foreach($post['tags'] as $tag)
                    <a href="@base('/tags/'.urlencode($tag))" class="label tag">{{ $tag }}</a>
                    @endforeach
                    </span>
                  @endif
                  @if(!empty($post['_created']))
                    <span title="{{ date($dateformat_long, $post['_created']) }}">@lang('created'): <time class="date created" datetime="{{ date('Y-m-d H:i', $post['_created']) }}">{{ date($dateformat, $post['_created']) }}</time></span>
                  @endif
                  @if(!empty($post['_modified']) && $post['_modified'] != $post['_created'])
                    <span title="{{ date($dateformat_long, $post['_modified']) }}">@lang('modified'): <time class="date created" datetime="{{ date('Y-m-d H:i', $post['_created']) }}">{{ date($dateformat, $post['_modified']) }}</time></span>
                  @endif
                  @if(getenv('MPDOCS_ENVIRONMENT') === 'DEVELOPMENT')
                    <span><a href="@route('/'.MP_ADMINFOLDER.'/collections/entry/pages/'.$post['_id'].'?lang='.mp()->lang)">edit</a></span>
                  @endif
                </div>
