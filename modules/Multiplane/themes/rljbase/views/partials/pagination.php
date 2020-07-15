<?php
if (isset($pagination['hide']) && $pagination['hide'] == true) return;
$dropdownLimit = $dropdownLimit ?? $pagination['dropdownLimit'] ?? 5;
?>

          @if($pagination['pages'] > 1)
            <nav class="pagination {{ $pagination['pages'] > $dropdownLimit ? 'dropdown' : '' }}" aria-label="@lang('Pagination')">
                <span class="pagination_label">@lang('Page')</span>
                <ol>
                @if($pagination['pages'] > $dropdownLimit && $pagination['page'] > 1)
                    <li class="pagination_first"><a href="@base($pagination['slug'])" title="@lang('first')">&laquo;</a></li>
                @endif
                @if($pagination['page'] == 2)
                    <li class="pagination_previous"><a href="@base($pagination['slug'])" title="@lang('previous')">&lt;</a></li>
                @endif
                @if($pagination['page'] > 2)
                    <li class="pagination_previous"><a href="@base($pagination['slug'].'/page/'.($pagination['page']-1))" title="@lang('previous')">&lt;</a></li>
                @endif
                @if($pagination['pages'] > $dropdownLimit)
                    @for($i = $pagination['page'] - 3; $i < $pagination['page']; $i++)
                      @if($i > 0)
                        <li class="pagination_item">
                            <a href="@base($pagination['slug'].'/page/'.$i)" title="@lang('Page') {{ $i }} @lang('of') {{ $pagination['pages'] }}">{{ $i }}</a>
                        </li>
                      @endif
                    @endfor
                    <li class="pagination_dropdown_headline" tabindex="0" title="@lang('Page') {{ $pagination['page'] }} @lang('of') {{ $pagination['pages'] }}"><span>{{ $pagination['page'] }}</span><ol>
                @endif
                @for($i = 1; $i <= $pagination['pages']; $i++)
                    <li class="pagination_item {{ $i == $pagination['page'] ? 'active' : '' }}">
                    @if($i == $pagination['page'])
                        <span title="@lang('Page') {{ $i }} @lang('of') {{ $pagination['pages'] }}">{{ $i }}</span>
                    @else
                        <a href="@base($pagination['slug'].($i == 1 ? '' : '/page/'.$i))" title="@lang('Page') {{ $i }} @lang('of') {{ $pagination['pages'] }}">{{ $i }}</a>
                    @endif
                    </li>
                @endfor
                @if($pagination['pages'] > $dropdownLimit)
                    </ol></li>
                    @for($i = $pagination['page'] + 1; $i < $pagination['page'] + 3 && $i <= $pagination['pages']; $i++)
                        <li class="pagination_item">
                            <a href="@base($pagination['slug'].'/page/'.$i)" title="@lang('Page') {{ $i }} @lang('of') {{ $pagination['pages'] }}">{{ $i }}</a>
                        </li>
                    @endfor
                @endif
                @if($pagination['page'] < $pagination['pages'])
                    <li class="pagination_next"><a href="@base($pagination['slug'].'/page/'.($pagination['page']+1))" title="@lang('next')">&gt;</a></li>
                @endif
                @if($pagination['pages'] > $dropdownLimit && $pagination['page'] < $pagination['pages'])
                    <li class="pagination_last"><a href="@base($pagination['slug'].'/page/'.$pagination['pages'])" title="@lang('last')">&raquo;</a></li>
                @endif
                </ol>
            </nav>
          @endif
