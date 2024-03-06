/**
 * Copyright © k.tsiapalis86@gmail.com. All rights reserved.
 * @package Netsteps_MarketplaceSales
 * @author Kostas Tsiapalis
 */
define([
    'uiComponent',
    'underscore'
], function (Component, _){
    'use strict';

    return Component.extend({
        defaults: {
            displayArea: 'after_details',
            template: 'Netsteps_MarketplaceSales/summary/item/details/marketplace',
            itemData: window.checkoutConfig.marketplace.itemData ?? {}
        },

        /**
         * Check if item data
         * @param item
         * @returns {*|boolean}
         */
        hasData: function (item){
            return item.item_id &&
                this.itemData.hasOwnProperty(item.item_id) &&
                _.isObject(this.itemData[item.item_id]);
        },

        /**
         * Get item data
         * @param item
         * @returns {*[]}
         */
        getMarketplaceData: function (item){
            var data = [];

            _.each(this.itemData[item.item_id], function (value, key){
                data.push({
                    key: key,
                    value: value
                });
            });

            return data;
        }
    });
});
