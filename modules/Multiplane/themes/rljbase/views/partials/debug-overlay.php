<style>
.debug {
    position: fixed;
    top: 0;
    right: 0;
    background: rgba(255,255,255,.5);
    z-index: 9999;
}
.debug pre {
    margin: .5em 0 0 0;
    font-size: 10px;
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
route: {{ $app['route'], PHP_EOL }}
lang:  {{ mp()->lang, PHP_EOL }}
title: {{ $page['title'] ?? 'n/a', PHP_EOL }}
</pre>
</div>
