<style>
.debug {
    position: fixed;
    top: 0;
/*     right: 0; */
    left: 0;
    background: rgba(255,255,255,.5);
    z-index: 9999;
}
.debug pre {
    margin: .5em 0 0 0;
    font-size: 10px;
    background: transparent;
}
#debug_toggle + label {
    position: fixed;
    top: 0;
    right: 0;
}
#debug_toggle:checked + label + pre {
    display: none;
}
</style>
<div class="debug">
<input type="checkbox" id="debug_toggle" /><label for="debug_toggle"></label>
<pre>
route:          {{ $app['route'], PHP_EOL }}
title:          {{ $page['title'] ?? 'n/a', PHP_EOL }}
lang:           {{ mp()->lang, PHP_EOL }}
isMultilingual: {{ mp()->isMultilingual, PHP_EOL }}
collection:     {{ mp()->collection, PHP_EOL }}
hasParentPage:  {{ mp()->hasParentPage, PHP_EOL }}
isStartpage:    {{ mp()->isStartpage, PHP_EOL }}
currentSlug:    {{ mp()->currentSlug, PHP_EOL }}
pageTypeDetection: {{ mp()->pageTypeDetection, PHP_EOL }}
slugName:       {{ mp()->get('fieldNames/slug'), PHP_EOL }}
posts:          {{ isset($posts['posts']) ? count($posts['posts']) : 0, PHP_EOL }}
</pre>
</div>
