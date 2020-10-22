( function( mw, bs, $, document ) {
	bs.ui.widget.ObjectInputWidget = function ( cfg ) {
		this.inputs = cfg.inputs || {};
		this.values = cfg.values || {};
		this.widgets = {};
		this.layouts = [];
		bs.ui.widget.ObjectInputWidget.parent.call( this, cfg );

		this.$input.remove();
		this.createInputs();
	};

	OO.inheritClass( bs.ui.widget.ObjectInputWidget, OO.ui.InputWidget );

	bs.ui.widget.ObjectInputWidget.prototype.createInputs = function() {

		for( var key in this.inputs ) {
			if ( !this.inputs.hasOwnProperty( key ) ) {
				continue;
			}
			var input = this.inputs[key];
			switch ( input.type ) {
				case 'text':
					this.widgets[key] = new OO.ui.TextInputWidget( input.widget || {} );
					break;
				case 'bool':
					this.widgets[key] = new OO.ui.CheckboxInputWidget( input.widget || {} );
					break;
				case 'json':
					this.widgets[key] = new bs.ui.widget.JsonArrayInputWidget( input.widget || {} );
					break;
				case 'number':
					this.widgets[key] = new OO.ui.NumberInputWidget( input.widget || {} );
					break;
				default:
					continue;
			}

			if ( this.values.hasOwnProperty( key ) ) {
				this.setWidgetValue( this.widgets[key], this.values[key] );
			}
			this.layouts.push( new OO.ui.FieldLayout( this.widgets[key], {
				label: input.label || '',
				align: 'top'
			} ) );
		}
	};

	bs.ui.widget.ObjectInputWidget.prototype.getLayouts = function() {
		return this.layouts;
	};

	bs.ui.widget.ObjectInputWidget.prototype.setValue = function( value ) {
		if ( !value ) {
			value = '';
		}
		for( var key in this.widgets ) {
			if ( !this.widgets.hasOwnProperty( key ) ) {
				continue;
			}
			if ( $.type( value ) === 'object' && value.hasOwnProperty( key ) ) {
				this.setWidgetValue( this.widgets[key], value[key] );
			} else {
				this.setWidgetValue( this.widgets[key], '' );
			}

		}

		return value;
	};

	bs.ui.widget.ObjectInputWidget.prototype.setWidgetValue = function( widget, value ) {

		if ( widget instanceof OO.ui.CheckboxInputWidget ) {
			widget.setSelected( !!value );
			return;
		}

		widget.setValue( value );
	};

	bs.ui.widget.ObjectInputWidget.prototype.getWidgetValue = function( widget ) {
		if ( widget instanceof OO.ui.CheckboxInputWidget ) {
			return !!widget.isSelected();
		}

		return widget.getValue();
	};

	bs.ui.widget.ObjectInputWidget.prototype.getValue = function() {
		var value = {};
		for( var key in this.widgets ) {
			if ( !this.widgets.hasOwnProperty( key ) ) {
				continue;
			}
			var widgetValue = this.getWidgetValue( this.widgets[key] );
			if ( widgetValue ) {
				value[key] = widgetValue;
			}
		}

		return value;
	};

} )( mediaWiki, blueSpice, jQuery, undefined );
