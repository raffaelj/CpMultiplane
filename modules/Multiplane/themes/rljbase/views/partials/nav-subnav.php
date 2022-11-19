
            <ul{{ isset($class) ? " class=\"$class\"" : '' }}>
              @foreach($nav as $n){% $hasChildren = !empty($n['children']) %}
                <li{{ isset($n['class']) ? " class=\"{$n['class']}\"" : '' }}>
                  @if($hasChildren){% $_id = uniqid('mp-nav-') %}
                    <input type="checkbox" id="{{ $_id }}" tabindex="-1" aria-label="@lang('Expand sub menu')" /><label for="{{ $_id }}"></label>
                  @endif
                  @if(isset($n['url']))
                    <a class="{{ ($n['active'] ?? false) ? 'active' : '' }}{{ $hasChildren ? ' dropdown' : '' }}" href="{{ $n['url'] }}">{{{ $n['title'] }}}</a>
                  @elseif(isset($n[mp()->slugName]))
                    <a class="{{ ($n['active'] ?? false) ? 'active' : '' }}{{ $hasChildren ? ' dropdown' : '' }}" href="@base(!empty($n['startpage']) && !mp()->usePermalinks ? '/' : $n[mp()->slugName])">{{{ $n['title'] }}}</a>
                  @endif
                  @if($hasChildren)
                    @render('views:partials/nav-subnav.php', ['nav' => $n['children']])
                  @endif
                </li>
              @endforeach
            </ul>
