/**
 *
 * @class
 * @abstract
 * @extends OO.ui.ComboBoxInputWidget
 *
 * @constructor
 * @param {Object} [config] Configuration options
 * @cfg {Object[]} [options=[]] Array of menu options in the format `{ data: …, label: … }`
 * @cfg {Object} [menu] Configuration options to pass to the {@link OO.ui.FloatingMenuSelectWidget menu select widget}.
 * @cfg {jQuery} [$overlay] Render the menu into a separate layer. This configuration is useful in cases where
 *  the expanded menu is larger than its containing `<div>`. The specified overlay layer is usually on top of the
 *  containing `<div>` and has a larger area. By default, the menu uses relative positioning.
 */
bs.ui.widget.StoreApiBoundComboBoxInput = function ( cfg ) {
	cfg = cfg || {};

	this.apiAction = cfg.apiAction || this.apiAction;
	bs.ui.widget.StoreComboBoxInput.call( this, cfg );
};

OO.inheritClass(
	bs.ui.widget.StoreApiBoundComboBoxInput,
	bs.ui.widget.StoreComboBoxInput
);

/**
 *
 * @inheritdoc
 */
bs.ui.widget.StoreApiBoundComboBoxInput.prototype.getLookupRequest = function () {
	var value = this.getValue();
	var deferred = $.Deferred();

	if( value === '' ) {
		deferred.resolve( [] );
	}

	var api = new mw.Api();
	api.get({
		action: this.apiAction,
		query: value
	})
	.done( function( response, jqXHR ){
		deferred.resolve( response.results );
	});

	return deferred.promise( { abort: function () {} } );
};
