/**
 * Filter by a configurable Ext.form.field.Text
 * <p><b><u>Example Usage:</u></b></p>
 * <pre><code>
 var filters = Ext.create('Ext.ux.grid.GridFilters', {
 ...
 filters: [{
 // required configs
 type: 'string',
 dataIndex: 'name',
 
 // optional configs
 value: 'foo',
 active: true, // default is false
 iconCls: 'ux-gridfilter-text-icon' // default
 // any Ext.form.field.Text configs accepted
 }]
 });
 * </code></pre>
 */
Ext.define('Ext.ux.grid.filter.StringFilter', {
    extend: 'Ext.ux.grid.filter.Filter',
    alias: 'gridfilter.string',
    uses: ['Ext.form.field.Text'],
    /**
     * @private @override
     * Creates the Menu for this filter.
     * @param {Object} config Filter configuration
     * @return {Ext.menu.Menu}
     */
    createMenu: function(config) {
        var me = this,
                menu;
        menu = Ext.create('Ext.ux.grid.menu.StringMenu', config);
        menu.on('update', me.fireUpdate, me);
        return menu;
    },
    /**
     * @private
     * Template method that is to get and return the value of the filter.
     * @return {String} The value of this filter
     */
    getValue: function() {
        return this.menu.getValue();
    },
    /**
     * @private
     * Template method that is to set the value of the filter.
     * @param {Object} value The value to set the filter
     */
    setValue: function(value) {
        this.menu.setValue(value);
    },
    /**
     * Template method that is to return <tt>true</tt> if the filter
     * has enough configuration information to be activated.
     * @return {Boolean}
     */
    isActivatable: function() {
        var values = this.getValue(),
                key;
        for (key in values) {
            if (values[key] !== undefined) {
                return true;
            }
        }
        return false;
    },
    /**
     * @private
     * Template method that is to get and return serialized filter data for
     * transmission to the server.
     * @return {Object/Array} An object or collection of objects containing
     * key value pairs representing the current configuration of the filter.
     */
    getSerialArgs: function() {
        var key,
                args = [],
                values = this.menu.getValue();
        for (key in values) {
            if (values[key] !== '') {
                args.push({
                    type: 'string',
                    comparison: key,
                    value: values[key]
                });
            }
        }
        return args;
    },
    /**
     * Template method that is to validate the provided Ext.data.Record
     * against the filters configuration.
     * @param {Ext.data.Record} record The record to validate
     * @return {Boolean} true if the record is valid within the bounds
     * of the filter, false otherwise.
     */
    validateRecord: function(record) {
        var val = record.get(this.dataIndex),
                values = this.getValue();
        if (values.eq !== undefined && val != values.eq) {
            return false;
        }
        if (values.lt !== undefined && val >= values.lt) {
            return false;
        }
        if (values.gt !== undefined && val <= values.gt) {
            return false;
        }
        return true;
    }
});
