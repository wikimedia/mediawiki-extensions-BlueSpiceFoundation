/**
 * Key navigation for {@link Ext.ux.form.field.GridPicker}.
 */
Ext.define('Ext.ux.form.field.GridPickerKeyNav', {
    extend: 'Ext.util.KeyNav',
    constructor: function(config) {
        this.pickerField = config.pickerField;
        this.grid = config.grid;
        this.callParent([config.target, Ext.apply({}, config, this.defaultHandlers)]);
    },
    defaultHandlers: {
        up: function() {
            this.goUp(1);
        },
        down: function() {
            this.goDown(1);
        },
        pageUp: function() {
            this.goUp(10);
        },
        pageDown: function() {
            this.goDown(10);
        },
        home: function() {
            this.highlightAt(0);
        },
        end: function() {
            var count = this.getGrid().getStore().getCount();
            if (count > 0) {
                this.highlightAt(count - 1);
            }
        },
        tab: function(e) {
            var pickerField = this.getPickerField();
            if (pickerField.selectOnTab) {
                this.selectHighlighted(e);
                pickerField.triggerBlur();
            }
            // Tab key event is allowed to propagate to field
            return true;
        },
        enter: function(e) {
            var pickerField = this.getPickerField();
            var selModel = this.getGrid().getSelectionModel(),
                count = selModel.getCount();
            this.selectHighlighted(e);
            if (!pickerField.multiSelect && count) {
                pickerField.collapse();
            }
        }
    },
    goUp: function(n) {
        var grid = this.getGrid(),
            store = grid.getStore(),
            sm = grid.getSelectionModel(),
            lastSelected = sm.lastSelected,
            count = store.getCount(),
            nextIndex = count - n;

        if (count > 0) {
            if (lastSelected) {
                nextIndex = store.indexOf(lastSelected) - n;
                if (nextIndex < 0) {
                    nextIndex = count - 1;
                }
            }

            this.highlightAt(nextIndex);
        }
    },
    goDown: function(n) {
        var grid = this.getGrid(),
            store = grid.getStore(),
            sm = grid.getSelectionModel(),
            lastSelected = sm.lastSelected,
            count = store.getCount(),
            nextIndex = 0;

        if (count > 0) {
            if (lastSelected) {
                nextIndex = store.indexOf(lastSelected) + n;
                if (nextIndex >= count) {
                    nextIndex = 0;
                }
            }

            this.highlightAt(nextIndex);
        }
    },
    getPickerField: function() {
        return this.pickerField;
    },
    getGrid: function() {
        return this.grid;
    },
    highlightAt: function(index) {
        this.getPickerField().highlightAt(index);
    },
    selectHighlighted: function(e) {
        var selection = this.getGrid().getSelectionModel().getSelection(),
            //selected = selection && selection[0],
            pickerField = this.pickerField;
        if (selection.length) {
            pickerField.select(selection);
            pickerField.fireEvent('select', pickerField, selection);
        }
    }
});
