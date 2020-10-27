/**
 * Simple mail address protection
 * 
 * 'john.doe [AT] example [DOT] com' or 'john.doe[AT]example[DOT]com' will
 * be converted to 'john.doe@example.com'
 * 
 * Usage: see docs
 */

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

        var $this = this;

        // (?:[^\S\r\n]*\[AT\](?:[^\S\r\n]*))(.+?)(?:[^\S\r\n]*\[DOT\][^\S\r\n]*)
        // regex demo: https://regex101.com/r/XvJZ7X/1

        var regex = this.pattern || new RegExp(
                '(?:[^\\S\r\n]*'            // unlimited whitespaces (except newlines)
              + this.escapeRegExp(this.at) // @ pattern
              + '(?:[^\\S\r\n]*))'
              + '(.+?)'                     // text between @ and . (domain)
              + '(?:[^\\S\r\n]*'
              + this.escapeRegExp(this.dot)  // . pattern
              + '[^\\S\r\n]*)'
              , 'g'),
            replace = this.replace || '@$1.'; // replace match with '@domain.' and ignore all whitespaces

        Array.prototype.forEach.call(document.querySelectorAll(this.selector), function(el) {
            $this.replaceInText(el, regex, replace); 
        });

    },

    // source: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Regular_Expressions#Escaping
    escapeRegExp: function (str) {
        return str.replace(/[.*+\-?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
    },

    // inspired by: https://stackoverflow.com/a/50537862
    replaceInText: function (element, pattern, replacement) {

        var $this = this;

        Array.prototype.forEach.call(element.childNodes, function(node) {

            switch (node.nodeType) {
                case Node.ELEMENT_NODE:

                    // don't convert mail addresses inside code blocks
                    if (node.nodeName == 'CODE' || node.nodeName == 'PRE') return;

                    $this.replaceInText(node, pattern, replacement);

                    // in case of already existing mailto links
                    if (node.nodeName == 'A' && node.href.match(/^mailto:/)) {
                        node.setAttribute('href', node.getAttribute('href').replace(pattern, replacement));
                    }
                    break;
                case Node.TEXT_NODE:
                    node.textContent = node.textContent.replace(pattern, replacement);
                    break;
                case Node.DOCUMENT_NODE:
                    $this.replaceInText(node, pattern, replacement);
            }

        });

    },

}
