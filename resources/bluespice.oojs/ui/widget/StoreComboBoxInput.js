/**
 * @class
 * @abstract
 * @extends OO.ui.ComboBoxInputWidget
 * @constructor
 * @param {Object} [cfg] Configuration options
 * @cfg {Object[]} [options=[]] Array of menu options in the format `{ data: …, label: … }`
 * @cfg {Object} [menu] Configuration options to pass to the {@link OO.ui.FloatingMenuSelectWidget menu select widget}.
 * @cfg {jQuery} [$overlay] Render the menu into a separate layer. This configuration is useful in cases where
 * the expanded menu is larger than its containing `<div>`. The specified overlay layer is usually on top of the
 * containing `<div>` and has a larger area. By default, the menu uses relative positioning.
 */
bs.ui.widget.StoreComboBoxInput = function ( cfg ) {
	cfg = cfg || {};

	this.selectedItem = null;

	this.displayField = cfg.displayField || this.displayField;
	this.valueField = cfg.valueField || '';
	this.localData = cfg.localData || [];

	OO.ui.ComboBoxInputWidget.call( this, cfg );
	OO.ui.mixin.LookupElement.call( this, cfg );
};

OO.inheritClass( bs.ui.widget.StoreComboBoxInput, OO.ui.ComboBoxInputWidget );
OO.mixinClass( bs.ui.widget.StoreComboBoxInput, OO.ui.mixin.LookupElement );

/**
 * @inheritdoc
 */
bs.ui.widget.StoreComboBoxInput.prototype.getLookupRequest = function () {
	const value = this.getValue(),
		deferred = $.Deferred(),
		promise = deferred.promise( { abort: function () {} } );

	if ( !value ) {
		deferred.resolve( this.localData );
	} else {
		const filteredData = [];
		for ( let i = 0; i < this.localData.length; i++ ) {
			const record = this.localData[ i ];
			if ( this.displayField && record[ this.displayField ].indexOf( value ) !== -1 ) {
				filteredData.push( record );
				continue;
			}
		}

		deferred.resolve( filteredData );
	}

	return promise;
};

/**
 * @inheritdoc
 */
bs.ui.widget.StoreComboBoxInput.prototype.getLookupMenuOptionsFromData = function ( data ) {
	const items = [];
	for ( let i = 0; i < data.length; i++ ) {
		const record = data[ i ];
		items.push( new OO.ui.MenuOptionWidget( {
			data: record,
			label: record[ this.displayField ]
		} ) );
	}

	return items;
};

/**
 * @inheritdoc
 */
bs.ui.widget.StoreComboBoxInput.prototype.getLookupCacheDataFromResponse = function ( response ) {
	return response || [];
};

/**
 * @inheritdoc
 */
bs.ui.widget.StoreComboBoxInput.prototype.setValue = function ( value ) {
	this.selectedItem = null;
	const displayValue = value;
	if ( typeof value === 'object' ) {
		this.selectedItem = value;
	}

	bs.ui.widget.StoreComboBoxInput.super.prototype.setValue.apply( this, arguments );

	if ( typeof displayValue === 'string' && this.$input.val() !== displayValue ) {
		this.$input.val( displayValue );
	}

	return this;
};

/**
 * @inheritdoc
 */
bs.ui.widget.StoreComboBoxInput.prototype.cleanUpValue = function ( value ) {
	if ( typeof value === 'object' ) {
		value = value[ this.displayField ];
	}
	return bs.ui.widget.StoreComboBoxInput.super.prototype.cleanUpValue( value );
};

/**
 * @inheritdoc
 */
bs.ui.widget.StoreComboBoxInput.prototype.findSelectedItem = function () {
	return this.selectedItem;
};

/**
 * Returns the data object of the currently selected option or null if
 * noting is selected
 */
bs.ui.widget.StoreComboBoxInput.prototype.getSelectedValue = function () {
	if ( this.valueField === '' ) {
		return this.getValue();
	}
	const item = this.findSelectedItem();
	return item ? item[ this.valueField ] : null;
};

/**
 * Sets the data for the local store
 *
 * @param {Object[]} data
 */
bs.ui.widget.StoreComboBoxInput.prototype.setRawData = function ( data ) {
	this.localData = data;
};
