
module.exports = {

    lifeTime: '30', // cookie life time in days

    set: function(key, value, lifeTime) {

        if (!key || (!value && value != 0)) return;
        if (!lifeTime && lifeTime != 0) lifeTime = this.lifeTime;

        var expirationDate = new Date();
        expirationDate.setTime(expirationDate.getTime() + lifeTime * 86400000)

        document.cookie = key + '=' + value + ';expires=' + expirationDate.toUTCString() + '; path=/';

    },

    get: function(key) {

        if (document.cookie == '') return;

        // source: https://stackoverflow.com/a/42578414
        var cockie = document.cookie.split('; ').reduce(function(result, pairStr) {
            var arr = pairStr.split('=');
            if (arr.length === 2) { result[arr[0]] = arr[1]; }
            return result;
        }, {});

        return key ? cockie[key] : cockie;

    },

    destroy: function(key) {

        this.set(key, '', 0);

    }

};
