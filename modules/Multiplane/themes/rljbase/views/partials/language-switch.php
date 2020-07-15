
            <nav class="language-switch" aria-label="@lang('Language')">
                <ul>
                  @foreach(mp()->getLanguageSwitch($page['_id'] ?? '') as $lang)
                    <li>
                      @if($lang['active'])
                        <span title="{{ $lang['name'] }}">{{ $lang['code'] }}</span>
                      @else
                        <a class="{{ $lang['active'] ? 'active' : '' }}" href="{{ $lang['url'] }}" title="{{ $lang['name'] }}">{{ $lang['code'] }}</a>
                      @endif
                    </li>
                  @endforeach
                </ul>
            </nav>
