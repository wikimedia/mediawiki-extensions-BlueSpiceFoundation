Ext.define('Ext.ux.form.field.GridPickerSearch', {
    extend: 'Ext.ux.form.field.GridPicker',
    alias: 'widget.gridpickersearch',
    triggerCls: 'x-form-clear-trigger', // using clear value trigger
    hideTrigger: true,
    triggerAction: 'query',
    /**
     * Handles the trigger click; by clearing value and collapse picker
     * @protected
     */
    onTriggerClick: function() {
        this.clearValue();
        this.collapse();
    },
    //Overridden to collapse on clearVlaue
    afterQuery: function(queryPlan) {
        this.callParent(arguments);
        if (!queryPlan.query) {
            this.collapse();
        }
    },
    onChange: function(newVal, oldVal) {
        if (newVal) {
            this.setHideTrigger(false);
        } else {
            this.setHideTrigger(true);
        }
        this.callParent(arguments);
    }
});
