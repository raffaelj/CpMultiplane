/* 
 * keep the focus in open modal(s) and prevent an endless loop if multiple modals
 * fight about their focus (e. g. cookie popup and lightbox a the same time)
 * to prevent the browser from crashing
 *
 * to do:
 * * cleanup
 * * detect dynamically changed focusable elements
 * * fix jumping to bottom of page when leaving focus with shift + tab
 *
 */

module.exports = {

    modals: [],
    conditions: [],
    priorities: [],
    focuses: [],
    initialized: false,
    focusableElements: 'a:not([disabled]), button:not([disabled]), textarea:not([disabled]), input:not([type="hidden"]):not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"]',

    keepFocus: function(options) {

        var modal = options.modal || null;

        if (!modal) return;

        if (this.modals.indexOf(modal) == -1) {

            this.modals.push(modal);
            this.conditions.push(options.condition || function(){return false;});
            this.priorities.push(options.priority  || 10);
            this.focuses.push(modal.querySelectorAll((options.elements || null) || this.focusableElements));

        }

        if (!this.initialized) this.holdFocus();

    },

    holdFocus: function() {

        var $this = this;

        document.addEventListener('keydown', function(e) {
            $this.tabKey   = (e.keyCode == 9);
            $this.shiftKey = (e.shiftKey);
        });

        document.addEventListener('focus', function(e) {

            var contains = null,
                active   = []
                ;

            Array.prototype.forEach.call($this.modals, function(el, i) {

                if ($this.conditions[i]()) {

                    active.push($this.priorities[i]);

                    if (el.contains(e.target)) {
                        contains = i;
                    }

                }

            });

            if (contains === null && active.length > 0) {

                // focus active modal with highest priority
                var maxPrio = Math.max.apply(null, active),
                    maxKey  = $this.priorities.indexOf(maxPrio)
                    ;

                if (maxKey == -1) return;

                e.stopPropagation();

                // focus modal if no inner elements defined or found
                if (!$this.focuses[maxKey] && !$this.focuses[maxKey][0]) {
                    $this.modals[maxKey].focus();
                }
                else {

                    // focus last element if tabbed backwards with shift + tab
                    if ($this.tabKey && $this.shiftKey) {
                        $this.focuses[maxKey][$this.focuses[maxKey].length -1].focus();
                    }
                    // focus first element
                    else {
                        $this.focuses[maxKey][0].focus();
                    }

                }

            }

        }, true);

        // don't add eventListener multiple times
        this.initialized = true;

    }

}
