<?php
if (!isset($id)) $id = 'search';
?>
            <div class="search">
                <form action="@base('/search')" role="search">
                    <label for="{{ $id }}">@lang('Search in site')</label>
                    <input name="search" type="search" minlength="{{ mp()->get('search/minLength') }}" aria-label="@lang('Search')" id="{{ $id }}" />
                    <input name="highlight" type="hidden" value="1" />
                    <button type="submit" aria-label="@lang('Search')"><i class="icon-search"></i>@lang('Search')</button>
                </form>
            </div>
