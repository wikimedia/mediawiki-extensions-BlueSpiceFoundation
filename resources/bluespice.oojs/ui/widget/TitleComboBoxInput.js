/**
 *
 * @class
 * @abstract
 * @extends bs.ui.widget.StoreApiBoundComboBoxInput
 *
 * @constructor
 * @param {Object} [config] Configuration options
 * @cfg {Object[]} [options=[]] Array of menu options in the format `{ data: …, label: … }`
 * @cfg {Object} [menu] Configuration options to pass to the {@link OO.ui.FloatingMenuSelectWidget menu select widget}.
 * @cfg {jQuery} [$overlay] Render the menu into a separate layer. This configuration is useful in cases where
 *  the expanded menu is larger than its containing `<div>`. The specified overlay layer is usually on top of the
 *  containing `<div>` and has a larger area. By default, the menu uses relative positioning.
 */
bs.ui.widget.TitleComboBoxInput = function ( cfg ) {
	bs.ui.widget.StoreApiBoundComboBoxInput.call( this, cfg );
	this.apiAction = 'bs-titlequery-store';
	this.displayField = 'displayText';
	this.valueField = 'prefixedText';
};

OO.inheritClass( bs.ui.widget.TitleComboBoxInput, bs.ui.widget.StoreApiBoundComboBoxInput );