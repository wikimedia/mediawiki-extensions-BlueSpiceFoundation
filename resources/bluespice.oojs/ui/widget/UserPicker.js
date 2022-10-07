( function ( mw, $, bs ) {
	bs.ui.widget.UserPickerWidget = function ( cfg ) {
		// Deprecated since 4.2.3 - Use OOJSPlus.ui.widget.UserPickerWidget instead
		bs.ui.widget.UserPickerWidget.parent.call( this, cfg );
	};

	OO.inheritClass( bs.ui.widget.UserPickerWidget, OOJSPlus.ui.widget.UserPickerWidget );
}( mediaWiki, jQuery, blueSpice ) );
