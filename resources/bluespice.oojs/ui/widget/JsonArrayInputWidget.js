( function( mw, bs, $, document ) {
	bs.ui.widget.JsonArrayInputWidget = function ( cfg ) {
		this.type = cfg.type || 'text';
		bs.ui.widget.JsonArrayInputWidget.parent.call( this, cfg );

		this.$input.remove();

		if ( this.type === 'multiline' ) {
			this.widget = new OO.ui.MultilineTextInputWidget( cfg );
		} else {
			this.widget = new OO.ui.TextInputWidget( cfg );
		}

		this.$element.addClass( 'bs-ooui-widget-jsonArrayInputWidget' );
		this.$element.append( this.widget.$element );
	};

	OO.inheritClass( bs.ui.widget.JsonArrayInputWidget, OO.ui.InputWidget );

	bs.ui.widget.JsonArrayInputWidget.prototype.getValue = function() {
		var val = this.widget.getValue();
		if ( val ) {
			try {
				return JSON.parse( val );
			} catch ( e ) {
				return '';
			}
		}

		return '';
	};

	bs.ui.widget.JsonArrayInputWidget.prototype.setValue = function( val ) {
		if ( !val ) {
			return;
		}

		this.widget.setValue( JSON.stringify( val ) );
	};


} )( mediaWiki, blueSpice, jQuery, undefined );
