
var d      = document,
    MP     = require('./MP.js'),
    Cookie = require('./Cookie.js');

module.exports = {

    modal: d.getElementById('privacy-notice'),
    active: false,
    lastFocus: null,

    displayPrivacyNotice: function (params) {

        var $this  = this,
            target = params.target || d.activeElement,
            form   = d.getElementById('privacy-notice-form')
            ;

        this.lastFocus = target,

        this.show();

        form.addEventListener('submit', function(e) {

            if (e) e.preventDefault();

            var data    = new FormData(form),
                entries = data.entries(),
                entry   = entries.next()
                ;

            while (!entry.done) {
                Cookie.set(entry.value[0], entry.value[1])
                entry = entries.next();
            }

            MP.trigger('privacy', {
                type:   params.type,
                target: target
            });

            $this.hide();

        });

        form.addEventListener('reset', function(e) {
            $this.hide();
        });

    },

    show: function() {

        var $this = this;

        this.active = true;

        this.modal.style.display = 'block';
        this.modal.tabIndex = -1;
        this.modal.setAttribute('role', 'dialog');
        this.modal.focus();

        // force focus to modal
        d.addEventListener('focus', function(e) {
            if ($this.active && !$this.modal.contains(e.target)) {
                e.stopPropagation();
                $this.modal.focus();
            }
        }, true);

        // close modal with Escape
        // d.addEventListener('keydown', function(e) {
            // if ($this.active) {
                // if (e.keyCode == 27) $this.hide(e);
            // }
        // });

    },

    hide: function() {

        this.active = false;

        this.modal.style.display = '';

        // accessibility
        this.lastFocus.focus();

    }

}
