/**
 * Simple mail address protection
 * 
 * 'john.doe [AT] example [DOT] com' or 'john.doe[AT]example[DOT]com' will
 * be converted to 'john.doe@example.com'
 * 
 * Usage:
 * Add a snippet to your (theme) bootstrap.php

```php
mp()->add('scripts', [
'MP.ready(function() {
    MP.MailProtection.decode();
});'
]);
```

 * Usage example with a customized pattern:

```php
mp()->add('scripts', [
'MP.ready(function() {
    MP.MailProtection.init({at:"[Ät]",dot:"[Punkt]"});
});'
]);
```

 */

var Utils = require('./Utils.js');

module.exports = {

    selector: 'body',
    at:       '[AT]',
    dot:      '[DOT]',
    pattern:  null,
    replace:  null,

    init: function(options) {

        var $this = this;

        // overwrite config
        if (options && typeof options == 'object') {
            Object.keys(options).forEach(function(k) {
                $this[k] = options[k];
            });
        }

        this.decode();

    },

    decode: function() {

        // (?:[^\S\r\n]*\[AT\](?:[^\S\r\n]*))(.+?)(?:[^\S\r\n]*\[DOT\][^\S\r\n]*)
        // regex demo: https://regex101.com/r/XvJZ7X/1

        var regex = this.pattern || new RegExp(
                '(?:[^\\S\r\n]*'            // unlimited whitespaces (except newlines)
              + Utils.escapeRegExp(this.at) // @ pattern
              + '(?:[^\\S\r\n]*))'
              + '(.+?)'                     // text between @ and . (domain)
              + '(?:[^\\S\r\n]*'
              + Utils.escapeRegExp(this.dot)  // . pattern
              + '[^\\S\r\n]*)'
              , 'g'),
            replace = this.replace || '@$1.'; // replace match with '@domain.' and ignore all whitespaces

        Array.prototype.forEach.call(document.querySelectorAll(this.selector), function(el) {
            MP.Utils.replaceInText(el, regex, replace); 
        });

    },

}
