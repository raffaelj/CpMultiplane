<?php
if (!isset($id)) $id = 'search';
?>
            <div class="search">
                <form action="@base('/search')" role="search">
                    <label for="{{ $id }}">@lang('Search in site')</label>
                    <input name="search" type="search" minlength="{{ mp()->get('search/minLength') }}" aria-label="@lang('Search')" id="{{ $id }}" required />
                    <input name="highlight" type="hidden" value="1" />
                    <button type="submit" class="icon-search" aria-label="@lang('Search')" title="@lang('Search')"></button>
                </form>
            </div>
