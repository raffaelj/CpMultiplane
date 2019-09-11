
<div class="search">
    <form action="@base('/')">
        <input name="search" type="text" value="{{ $app->escape($app->param('search', '')) }}" minlength="{{ mp()->searchMinLength }}" />
        <button type="submit">@lang('Search')</button>
    </form>
</div>
