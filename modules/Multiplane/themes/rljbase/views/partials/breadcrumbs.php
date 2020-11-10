<?php
$breadcrumbs = mp()->breadcrumbs;
if (\count($breadcrumbs) == 1 && mp()->isStartpage) return;
?>

            <nav class="breadcrumbs horizontal" aria-label="@lang('Breadcrumb')">
                <ol>
                  @foreach($breadcrumbs as $k => $n)
                    <li>
                    @if($k == 0)
                        <a href="@base('/')" title="@lang('Home')" class="icon-home"></a>
                    @else
                        <a href="@route('/'.$n['slug'])">{{{ $n['title'] }}}</a>
                    @endif
                    </li>
                  @endforeach
                    <li>
                        <span>{{{ $page['title'] }}}</span>
                    </li>
                </ol>
            </nav>
