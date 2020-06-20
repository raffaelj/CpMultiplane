
module.exports = {

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
