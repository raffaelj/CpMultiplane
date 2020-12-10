
              @foreach($nav as $k => $n){% $hasChildren = !empty($n['children']) %}
                <li class="{{ $n['class'] ?? '' }}{{ $onlyMobile ? 'nav-visible-tiny' : '' }}{{ $onlyMobile && $k == 0 ? ' nav-spacer' : ''}}">
                  @if($hasChildren){% $_id = uniqid('mp-nav-') %}
                    <input type="checkbox" id="{{ $_id }}" tabindex="-1" /><label for="{{ $_id }}"></label>
                  @endif
                  @if(isset($n['url']))
                    <a class="{{ $n['active'] ? 'active' : '' }}{{ $hasChildren ? ' dropdown' : '' }}" href="{{ $n['url'] }}">{{{ $n['title'] }}}</a>
                  @elseif(isset($n[mp()->slugName]))
                    <a class="{{ $n['active'] ? 'active' : '' }}{{ $hasChildren ? ' dropdown' : '' }}" href="@base(!empty($n['startpage']) && !mp()->usePermalinks ? '/' : $n[mp()->slugName])">{{{ $n['title'] }}}</a>
                  @endif
                  @if($hasChildren)
                    @render('views:partials/nav-subnav.php', ['nav' => $n['children']])
                  @endif
                </li>
              @endforeach
