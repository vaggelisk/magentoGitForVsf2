define([
    'Magento_Ui/js/form/element/select',
    'uiRegistry'
], function (Select,registry) {
    'use strict';
    return Select.extend({
        initialize: function (){
            this._super();
            var source = registry.get('sellers_sellerstatic_form.sellers_sellerstatic_form_data_source');
            if(source.data.general.entity_id !== ''){
                this.disable();
            }
        }
    });
});
