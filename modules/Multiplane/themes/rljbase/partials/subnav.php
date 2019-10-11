
            <ul>
@foreach($nav as $n)
                <li>
                    <a class="{{ $n['active'] ? 'active' : '' }}{{ !empty($n['children']) ? ' dropdown' : '' }}" href="@base($n[mp()->slugName])">{{ $n['title'] }}</a>@if(!empty($n['children']))<input type="checkbox" id="{{ $n['_id'] }}" /><label for="{{ $n['_id'] }}"></label>
                    @render('views:partials/subnav.php', ['nav' => $n['children']])
                    @endif
                </li>
@endforeach
            </ul>