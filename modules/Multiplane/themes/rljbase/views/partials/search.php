<?php
$search = $app->param('search', '');
if (is_array($search) || strpos($search, '{') === 0) $search = '';
?>
            <div class="search">
                <form action="@base('/search')">
                    <input name="search" type="text" value="{{{ $search }}}" minlength="{{ mp()->get('search/minLength') }}" aria-label="@lang('Search')" />
                    <input name="highlight" type="hidden" value="1" />
                    <button type="submit" aria-label="@lang('Search')"><i class="icon-search"></i>@lang('Search')</button>
                </form>
            </div>
