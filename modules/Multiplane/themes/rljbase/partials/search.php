
            <div class="search">
                <form action="@base('/search')">
                    <input name="search" type="text" value="{{ $app->escape($app->param('search', '')) }}" minlength="{{ mp()->searchMinLength }}" aria-label="@lang('Search')" />
                    <input name="highlight" type="hidden" value="1" />
                    <button type="submit" aria-label="@lang('Search')"><i class="icon-search"></i>@lang('Search')</button>
                </form>
            </div>
