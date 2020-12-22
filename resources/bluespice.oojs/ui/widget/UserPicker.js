( function( mw, $, bs ) {
	bs.ui.widget.UserPickerWidget = function( cfg ) {
		cfg = cfg || {};

		this.selectedUser = null;
		this.ignoreChange = false;

		bs.ui.widget.UserPickerWidget.parent.call( this, $.extend( {}, cfg, { autocomplete: false } ) );

		this.$element.addClass( 'bs-ui-widget-UserPicker' );

		this.connect( this, { change: 'unselectUser' } );
	};

	OO.inheritClass( bs.ui.widget.UserPickerWidget, mw.widgets.UserInputWidget );

	bs.ui.widget.UserPickerWidget.prototype.getSelectedUser = function () {
		return this.selectedUser;
	};

	bs.ui.widget.UserPickerWidget.prototype.unselectUser = function () {
		if ( this.ignoreChange ) {
			return;
		}
		this.selectedUser = null;
	};

	bs.ui.widget.UserPickerWidget.prototype.getValidity = function () {
		var dfd = $.Deferred();

		if ( this.getSelectedUser() !== null ) {
			dfd.resolve();
		} else {
			dfd.reject();
		}
		return dfd.promise();
	};

	bs.ui.widget.UserPickerWidget.prototype.onLookupMenuItemChoose = function ( item ) {
		this.closeLookupMenu();
		this.setLookupsDisabled( true );

		this.selectedUser = item.getData();
		this.ignoreChange = true;
		this.setValue( this.selectedUser.display_name );
		this.ignoreChange = false;
		this.setLookupsDisabled( false );
	};

	bs.ui.widget.UserPickerWidget.prototype.getLookupRequest = function () {
		var inputValue = this.value;

		return new mw.Api().get( {
			action: 'bs-user-store',
			limit: this.limit,
			query: inputValue
		} );
	};

	bs.ui.widget.UserPickerWidget.prototype.getLookupCacheDataFromResponse = function ( response ) {
		return response.results || {};
	};

	bs.ui.widget.UserPickerWidget.prototype.getLookupMenuOptionsFromData = function ( data ) {
		var len, i, user,
			items = [];

		for ( i = 0, len = data.length; i < len; i++ ) {
			user = data[ i ] || {};
			items.push( new bs.ui.widget.util.UserMenuOptionWidget ( user ) );
		}

		return items;
	};
} ) ( mediaWiki, jQuery, blueSpice );
