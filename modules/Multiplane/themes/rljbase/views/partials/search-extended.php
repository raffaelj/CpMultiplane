<?php
$search = $app->param('search', '');
$highlight = (boolean) $app->param('highlight', false);
if (is_array($search)) $search = json_encode($search);
?>

            <div class="search-extended">
                <form action="@base('/search')">
                    <div>
                        <input name="search" type="text" value="{{{ $search }}}" minlength="{{ mp()->get('search/minLength') }}" aria-label="@lang('Search')" />
                    </div>
                    <div>
                        <input name="highlight" type="checkbox" value="1" id="mp_search_highlight" {{ $highlight ? 'checked' : '' }} />
                        <label for="mp_search_highlight">@lang('Highlight results')</label>
                    </div>
                    <button type="submit" aria-label="@lang('Search')">@lang('Search')</button>
                </form>
            </div>
