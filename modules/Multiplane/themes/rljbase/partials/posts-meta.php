
                <p>
                    <span class="date created">{{ date($app('i18n')->get('@dateformat_long', 'Y-m-d H:i'), $post['_created']) }}</span>
                    @if(!empty($post['tags'])) @foreach($post['tags'] as $tag)
                        <span class="label tag">{{ $tag }}</span>
                    @endforeach @endif
                </p>