<?php
if (!isset($id)) $id = 'search';
?>
            <div class="search">
                <form action="@base('/search')" role="search">
                    <label for="{{ $id }}">@lang('Search in site')</label>
                    <input name="search" type="search" minlength="{{ mp()->get('search/minLength') }}" id="{{ $id }}" required />
                    <input name="highlight" type="hidden" value="1" />
                    <button type="submit" class="icon-search" title="@lang('Search')"><span class="visually-hidden">@lang('Search')</span></button>
                </form>
            </div>
