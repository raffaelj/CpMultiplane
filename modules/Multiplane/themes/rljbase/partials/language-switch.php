<?php

$languages = cockpit('multiplane')->getLanguageSwitch($page['_id'] ?? '');

?>

            <nav class="language-switch">
                <ul>
@foreach($languages as $lang)
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
