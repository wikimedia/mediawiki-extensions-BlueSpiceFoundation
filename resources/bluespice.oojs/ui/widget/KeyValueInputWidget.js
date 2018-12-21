( function( mw, bs, $, document ) {
	bs.ui.widget.KeyValueInputWidget = function ( cfg ) {
		bs.ui.widget.KeyValueInputWidget.parent.call( this, cfg );

		OO.EventEmitter.call( this );

		this.labelOnlyOnFirst = cfg.labelOnlyOnFirst || true;
		this.valueRequired = cfg.valueRequired || false;
		this.keyLabel = cfg.keyLabel || mw.message( 'bs-ooui-key-value-input-widget-key-label' ).plain();
		this.valueLabel = cfg.valueLabel || mw.message( 'bs-ooui-key-value-input-widget-value-label' ).plain();
		this.addNewFormLabel =  cfg.addNewFormLabel || mw.message( 'bs-ooui-key-value-input-widget-add-form-label' ).plain();
		this.keyReadOnly = cfg.keyReadOnly || false;
		this.allowAdditions = cfg.allowAdditions || false;
		this.$separator = $( '<div>' ).addClass( 'bs-ooui-keyValueInputWidget-separator' );

		this.$valueContainer = $( '<div>' ).addClass( 'bs-ooui-keyValueInputWidget-value-container' );
		this.$element.addClass( 'bs-ooui-widget-keyValueInputWidget' );

		this.addedWidgets = [];
		if( cfg.value ) {
			this.setValue( cfg.value );
		} else {
			this.setNoValueMessage();
		}

		this.$element.append( this.$valueContainer );

		if( this.allowAdditions ) {
			this.addNewValueForm();
		}
	};

	OO.inheritClass( bs.ui.widget.KeyValueInputWidget, OO.ui.Widget );
	OO.mixinClass( bs.ui.widget.KeyValueInputWidget, OO.EventEmitter );

	bs.ui.widget.KeyValueInputWidget.prototype.setNoValueMessage = function() {
		this.$valueContainer.append(
			new OO.ui.LabelWidget( {
				label: mw.message( "bs-ooui-key-value-input-widget-no-values-label" ).plain()
			} ).$element.addClass( 'bs-ooui-keyValueInputWidget-no-value-label' )
		);
	};

	bs.ui.widget.KeyValueInputWidget.prototype.setValue = function( values ) {
		var first = true;
		for( var key in values ) {
			var value = values[key];

			var cfg = $.extend( {
				keyValue: key,
				valueValue: value
			}, this.getDefaultLayoutConfig() );
			if( first ) {
				cfg.keyLabel = this.keyLabel;
				cfg.valueLabel = this.valueLabel;
				cfg.align = 'top';
			}

			var deleteButton = this.getDeleteButtonWidget();
			cfg.deleteWidget = deleteButton;

			this.addEntry( this.getLayouts( cfg ), deleteButton.$element );
			first = false;
		}
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getDefaultLayoutConfig = function() {
		var cfg = {
			keyReadOnly: this.keyReadOnly,
			addToWidgets: true,
			valueRequired: this.valueRequired
		};
		if( this.labelOnlyOnFirst === false ) {
			cfg.keyLabel = this.keyLabel;
			cfg.valueLabel = this.valueLabel;
			cfg.align = 'top';
		}
		return cfg;
	};

	bs.ui.widget.KeyValueInputWidget.prototype.addEntry = function( layouts, deleteWidgetElement ) {
		this.$valueContainer.append( layouts );
		if( deleteWidgetElement ) {
			this.$valueContainer.append( deleteWidgetElement );
		}
		this.$valueContainer.append( this.$separator.clone() );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getDeleteButtonWidget = function() {
		var deleteButton = new OO.ui.ButtonWidget( {
				framed: false,
				indicator: 'clear'
		} );
		deleteButton.$element.addClass( 'bs-ooui-widget-keyValueInputWidget-remove-btn' );
		return deleteButton;
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getLayouts = function( cfg ) {
		var keyInput = new OO.ui.TextInputWidget( {
			value: cfg.keyValue,
			readOnly: cfg.keyReadOnly,
			required: !cfg.keyReadOnly
		} );
		keyInput.$element.addClass( 'bs-ooui-widget-keyValueInputWidget-key-input' );

		var valueInput = new OO.ui.TextInputWidget( {
			value: cfg.valueValue,
			required: cfg.valueRequired
		} );
		valueInput.$element.addClass( 'bs-ooui-widget-keyValueInputWidget-value-input' );

		var layoutCfg = {};
		if( cfg.align ) {
			layoutCfg.align = cfg.align;
		}

		var keyLayout = new OO.ui.FieldLayout( keyInput, layoutCfg );
		if( cfg.keyLabel ) {
			keyLayout.setLabel( cfg.keyLabel );
		}

		var valueLayout = new OO.ui.FieldLayout( valueInput, layoutCfg );
		if( cfg.valueLabel ) {
			valueLayout.setLabel( cfg.valueLabel );
		}

		if( cfg.addToWidgets ) {
			this.addedWidgets.push( {
				keyWidget: keyInput,
				valueWidget: valueInput
			} );

			cfg.deleteWidget.$element.on( 'click', {
				keyWidget: keyInput,
				deleteWidget: cfg.deleteWidget
			}, this.onDeleteClick.bind( this ) );
		}

		return [ keyLayout.$element, valueLayout.$element ];
	};

	bs.ui.widget.KeyValueInputWidget.prototype.addNewValueForm = function() {
		this.$addContainer = $( '<div>' ).addClass( 'bs-ooui-widget-keyValueInputWidget-add-container' );

		if( this.addNewFormLabel !== '' ) {
			this.$addContainer.append( new OO.ui.LabelWidget( {
				label: this.addNewFormLabel
			} ).$element );
		}

		var layouts = this.getLayouts( {
			keyClass: 'bs-ooui-widget-keyValueInputWidget-key-input',
			valueClass: 'bs-ooui-widget-keyValueInputWidget-value-input',
			keyLabel: this.keyLabel,
			valueLabel: this.valueLabel,
			align: 'top',
			keyReadOnly: false,
			valueRequired: this.valueRequired,
			addToWidgets: false
		} );

		this.addButton = new OO.ui.ButtonWidget( {
			framed: false,
			icon: 'check',
			flags: [
				'progressive'
			],
			title: mw.message( 'bs-ooui-key-value-input-widget-add-button-label' ).plain()
		} );
		this.addButton.$element.addClass( 'bs-ooui-widget-keyValueInputWidget-add-btn' );
		this.addButton.on( 'click', this.onAddClick.bind( this ) );

		this.$addContainer.append( layouts, this.addButton.$element );
		this.$element.append( this.$addContainer );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.onAddClick = function( e ) {
		var $keyInput = this.$addContainer.find( '.bs-ooui-widget-keyValueInputWidget-key-input' ).find( 'input' );
		var $valueInput = this.$addContainer.find( '.bs-ooui-widget-keyValueInputWidget-value-input' ).find( 'input' );

		var keyValue = $keyInput.val();
		var valueValue = $valueInput.val();

		if( this.validate( keyValue, valueValue ) === false ) {
			return;
		}

		valueValue = valueValue || keyValue;

		var deleteButton = this.getDeleteButtonWidget();

		var layoutCfg = $.extend( {
			keyValue: keyValue,
			valueValue: valueValue,
			deleteWidget: deleteButton
		}, this.getDefaultLayoutConfig() );
		var layouts = this.getLayouts( layoutCfg );

		this.addEntry( layouts, deleteButton.$element );
		this.resetInputs();

		this.emit( 'change', this );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.resetInputs = function() {
		this.$addContainer.find( '.bs-ooui-widget-keyValueInputWidget-key-input' ).find( 'input' ).val( '' );
		this.$addContainer.find( '.bs-ooui-widget-keyValueInputWidget-value-input' ).find( 'input' ).val( '' );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getValue = function() {
		var value = {};
		for( var idx in this.addedWidgets ) {
			var keyWidget = this.addedWidgets[idx].keyWidget;
			var valueWidget = this.addedWidgets[idx].valueWidget;
			var keyValue = keyWidget.getValue();

			var valueValue = valueWidget.getValue();

			if( this.validate( keyValue, valueValue ) === false ) {
				continue;
			}

			valueValue = valueValue || keyValue;
			value[keyValue] = valueValue;
		}

		return value;
	};

	bs.ui.widget.KeyValueInputWidget.prototype.validate = function( keyValue, valueValue ) {
		if( keyValue === '' || ( this.valueRequired && valueValue === '') ) {
			this.makeErrorMessage();
			return false;
		}
		return true;
	};

	bs.ui.widget.KeyValueInputWidget.prototype.makeErrorMessage = function( message ) {
		message = message || mw.message( "bs-ooui-key-value-input-widget-error-message" ).plain();

		if( this.$errorBox ) {
			return;
		}

		this.$errorBox = $( '<div>' ).addClass( 'bs-ooui-keyValueInputWidget-error-box' );
		this.$errorBox.append( new OO.ui.IconWidget( {
			icon: 'alert',
			flags: [ 'warning' ]
		} ).$element );
		this.$errorBox.append( new OO.ui.LabelWidget( {
			label: message,
		} ).$element );
		this.$addContainer.append( this.$errorBox );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.onDeleteClick = function( e ) {
		e.data.deleteWidget.$element.remove();
		this.removeFromAddedWidgets( e.data.keyWidget );
		var currentValue = this.getValue();
		this.addedWidgets = [];
		this.$valueContainer.html( '' );

		if( $.isEmptyObject( currentValue ) === false ) {
			this.setValue( currentValue );
		} else {
			this.setNoValueMessage();
		}
		this.emit( 'change', this );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.removeFromAddedWidgets = function( keyWidget ) {
		for( var idx in this.addedWidgets ) {
			var widgets = this.addedWidgets[idx];
			if( widgets.keyWidget.$element.is( keyWidget.$element ) ) {
				this.addedWidgets.splice( idx, 1 );
				return true;
			}
		}
		return false;
	};

} )( mediaWiki, blueSpice, jQuery, undefined );
