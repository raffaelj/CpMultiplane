
var d      = document,
    MP     = require('./MP.js'),
    Modal  = require('./SimpleModalManager.js'),
    Cookie = require('./Cookie.js');

module.exports = {

    modalSelector: 'privacy-notice',       // id of modal
    formSelector:  'privacy-notice-form',  // id of form inside modal

    modal:              null,
    initialized:        false,
    initializedEvents:  false,
    active:             false,
    lastFocus:          null,

    init: function(options) {

        if (options) {
            if (options.modalSelector) this.modalSelector = options.modalSelector;
            if (options.formSelector)  this.formSelector  = options.formSelector;
        }

        this.modal = d.getElementById(this.modalSelector);
        if (!this.modal) return;

        this.initialized = true;

    },

    displayPrivacyNotice: function (params) {

        if (!this.initialized) this.init();

        var $this  = this
          , target = params.target || d.activeElement
          , form   = d.getElementById(this.formSelector)
          ;

        if (!form) return;

        this.lastFocus = target,

        this.show();

        if (this.initializedEvents) return; // avoid duplicated event listeners

        form.addEventListener('submit', function(e) {

            if (e) e.preventDefault();

            var data = new FormData(form);

            // manipulate data, e. g. to force setting a "zero cookie" when no checkbox was checked
            MP.trigger('privacy.form.submit', data);

            var entries = data.entries(), entry = entries.next();

            // set cookies for all inputs from privacy modal
            // to do: custom lifeTime per cookie
            while (!entry.done) {
                Cookie.set(entry.value[0], entry.value[1]);
                entry = entries.next();
            }

            // trigger 'privacy' again to check for set cookies
            // and to pass params to following event
            MP.trigger('privacy', params);

            $this.hide();

        });

        form.addEventListener('reset', function(e) {

            // set cookies manually to zero, if you don't want to annoy your
            // visitors with this popup over and over again
            MP.trigger('privacy.form.reset');

            $this.hide();
        });

        this.initializedEvents = true;

    },

    show: function() {

        if (!this.initialized) this.init();

        var $this = this;

        this.active = true;

        this.modal.classList.add('show');
        this.modal.tabIndex = -1;
        this.modal.setAttribute('role', 'dialog');
        this.modal.focus();

        MP.trigger('privacy.show');

        // force focus to modal
        Modal.keepFocus({
            modal:     this.modal,
            condition: function(){return $this.active;},
            priority:  1000,
            elements: 'button,a,input'
        });

        // close modal with Escape
        // d.addEventListener('keydown', function(e) {
            // if ($this.active) {
                // if (e.keyCode == 27) $this.hide(e);
            // }
        // });

    },

    hide: function() {

        if (!this.initialized) this.init();

        this.active = false;

        this.modal.classList.remove('show');

        // accessibility
        this.lastFocus.focus();

    }

}
