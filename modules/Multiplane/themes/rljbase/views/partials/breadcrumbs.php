<?php
$breadcrumbs = mp()->breadcrumbs;
$last = count($breadcrumbs) -1;
if ($last == 0) return;
// to do: display page titles instead of page slugs
?>

            <nav class="breadcrumbs horizontal" aria-label="@lang('Breadcrumb')">
                <ol>
                  @foreach($breadcrumbs as $k => $n)
                    <li>
                    @if($k == 0)
                        <a href="@base('/'.$n)" title="@lang('Home')" class="icon-home"></a>
                    @elseif($k < $last)
                        <a href="@base('/'.$n)">{{{ $n }}}</a>
                    @else
                        <span>{{{ $n }}}</span>
                    @endif
                    </li>
                  @endforeach
                </ol>
            </nav>
