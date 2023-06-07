define([
    'Magento_Ui/js/form/element/region'
], function (Region) {
    'use strict'

    return Region.extend({
        defaults: {
            regionScope: 'data.address.region'
        },

        /**
         * Set region to source form
         *
         * @param {String} value - region
         */
        setDifferedFromDefault: function (value) {
            this._super();

            if (parseFloat(value)) {
                this.source.set(this.regionScope, this.indexedOptions[value].label);
            }
        }
    });
});
