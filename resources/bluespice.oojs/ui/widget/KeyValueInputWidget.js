( function ( mw, bs, $, document ) {
	bs.ui.widget.KeyValueInputWidget = function ( cfg ) {
		bs.ui.widget.KeyValueInputWidget.parent.call( this, cfg );

		OO.EventEmitter.call( this );

		this.labelOnlyOnFirst = cfg.labelOnlyOnFirst || true;
		this.valueRequired = cfg.valueRequired || false;
		this.keyLabel = cfg.keyLabel || mw.message( 'bs-ooui-key-value-input-widget-key-label' ).text();
		this.valueLabel = cfg.valueLabel || mw.message( 'bs-ooui-key-value-input-widget-value-label' ).text();
		this.addNewFormLabel = cfg.addNewFormLabel || mw.message( 'bs-ooui-key-value-input-widget-add-form-label' ).text();
		this.keyReadOnly = cfg.keyReadOnly || false;
		this.allowAdditions = cfg.allowAdditions || false;
		this.$separator = $( '<div>' ).addClass( 'bs-ooui-keyValueInputWidget-separator' );

		this.$valueContainer = $( '<div>' ).addClass( 'bs-ooui-keyValueInputWidget-value-container' );
		this.$element.addClass( 'bs-ooui-widget-keyValueInputWidget' );

		this.addedWidgets = [];
		if ( cfg.value && !$.isEmptyObject( cfg.value ) ) {
			this.setValue( cfg.value );
		} else {
			this.setNoValueMessage();
		}

		this.$element.append( this.$valueContainer );

		if ( this.allowAdditions ) {
			this.addNewValueForm();
		}
	};

	OO.inheritClass( bs.ui.widget.KeyValueInputWidget, OO.ui.Widget );
	OO.mixinClass( bs.ui.widget.KeyValueInputWidget, OO.EventEmitter );

	bs.ui.widget.KeyValueInputWidget.prototype.setNoValueMessage = function () {
		this.$valueContainer.append(
			new OO.ui.LabelWidget( {
				label: mw.message( 'bs-ooui-key-value-input-widget-no-values-label' ).text(),
				classes: [ 'bs-ooui-keyValueInputWidget-no-value-label' ]
			} ).$element
		);
	};

	bs.ui.widget.KeyValueInputWidget.prototype.setValue = function ( values ) {
		let first = true;
		for ( const key in values ) {
			const value = values[ key ],
				cfg = $.extend( {
					keyValue: key,
					valueValue: value
				}, this.getDefaultLayoutConfig() );
			if ( first ) {
				cfg.keyLabel = this.keyLabel;
				cfg.valueLabel = this.valueLabel;
				cfg.align = 'top';
			}

			const deleteButton = this.getDeleteButtonWidget();
			cfg.deleteWidget = deleteButton;

			this.addEntry( this.getLayouts( cfg ), deleteButton.$element );
			first = false;
		}
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getDefaultLayoutConfig = function () {
		const cfg = {
			keyReadOnly: this.keyReadOnly,
			addToWidgets: true,
			valueRequired: this.valueRequired
		};
		if ( this.labelOnlyOnFirst === false ) {
			cfg.keyLabel = this.keyLabel;
			cfg.valueLabel = this.valueLabel;
			cfg.align = 'top';
		}
		return cfg;
	};

	bs.ui.widget.KeyValueInputWidget.prototype.addEntry = function ( layouts, deleteWidgetElement ) {
		const $blockWrapper = $( '<div>' ).addClass( 'instance-block' );
		this.$valueContainer.append( $blockWrapper.append( layouts ) );
		if ( deleteWidgetElement ) {
			$blockWrapper.append( deleteWidgetElement );
		}
		this.$valueContainer.append( this.$separator.clone() );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getDeleteButtonWidget = function () {
		const deleteButton = new OO.ui.ButtonWidget( {
			framed: false,
			indicator: 'clear'
		} );
		deleteButton.$element.addClass( 'bs-ooui-widget-keyValueInputWidget-remove-btn' );
		return deleteButton;
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getLayouts = function ( cfg, form ) {
		form = form || {};
		const layouts = form.layouts || this.getForm( cfg ).layouts,
			layoutElements = [];

		for ( let i = 0; i < layouts.length; i++ ) {
			layoutElements.push( layouts[ i ].$element );
		}

		return layoutElements;
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getForm = function ( cfg ) {
		const keyInput = this.getKeyInput( cfg ),
			valueInput = this.getValueInput( cfg ),
			keyLayout = this.getKeyLayout( keyInput, cfg ),
			valueLayout = this.getValueLayout( valueInput, cfg );

		if ( cfg.addToWidgets ) {
			this.addedWidgets.push( {
				keyWidget: keyInput,
				valueWidget: valueInput
			} );

			cfg.deleteWidget.$element.on( 'click', {
				keyWidget: keyInput,
				deleteWidget: cfg.deleteWidget
			}, this.onDeleteClick.bind( this ) );
		}

		return {
			inputs: { key: keyInput, value: valueInput },
			layouts: [ keyLayout, valueLayout ]
		};
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getKeyLayout = function ( keyInput, cfg ) {
		return new OO.ui.FieldLayout( keyInput, {
			align: cfg.align || 'left',
			label: cfg.keyLabel || ''
		} );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getValidity = function () {
		const toCheck = [],
			dfd = $.Deferred();

		for ( let i = 0; i < this.addedWidgets.length; i++ ) {
			if ( typeof this.addedWidgets[ i ].keyWidget.getValidity === 'function' ) {
				toCheck.push( this.addedWidgets[ i ].keyWidget );
			}
			if ( typeof this.addedWidgets[ i ].valueWidget.getValidity === 'function' ) {
				toCheck.push( this.addedWidgets[ i ].valueWidget );
			}
		}

		this.doCheckValidity( toCheck, dfd );

		return dfd.promise();
	};

	bs.ui.widget.KeyValueInputWidget.prototype.setValidityFlag = function ( valid ) {
		// NOOP since flags will be set already by internal validation
	};

	bs.ui.widget.KeyValueInputWidget.prototype.doCheckValidity = function ( inputs, dfd ) {
		if ( inputs.length === 0 ) {
			return dfd.resolve();
		}

		const current = inputs.shift();
		current.getValidity().done( function () {
			this.doCheckValidity( inputs, dfd );
		}.bind( this ) ).fail( function () {
			current.setValidityFlag( false );
			dfd.reject();
		} );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getValueLayout = function ( valueInput, cfg ) {
		return new OO.ui.FieldLayout( valueInput, {
			align: cfg.align || 'left',
			label: cfg.valueLabel || ''
		} );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getKeyInput = function ( cfg ) {
		const keyInput = new OO.ui.TextInputWidget( {
			value: cfg.keyValue,
			readOnly: cfg.keyReadOnly,
			required: !cfg.keyReadOnly,
			validity: !cfg.keyReadOnly ? 'non-empty' : false
		} );
		keyInput.$element.addClass( 'bs-ooui-widget-keyValueInputWidget-key-input' );

		return keyInput;
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getValueInput = function ( cfg ) {
		const valueInput = new OO.ui.TextInputWidget( {
			value: cfg.valueValue,
			required: cfg.valueRequired,
			validity: cfg.valueRequired ? 'non-empty' : false
		} );
		valueInput.$element.addClass( 'bs-ooui-widget-keyValueInputWidget-value-input' );

		return valueInput;
	};

	bs.ui.widget.KeyValueInputWidget.prototype.addNewValueForm = function () {
		this.$addContainer = $( '<div>' ).addClass( 'bs-ooui-widget-keyValueInputWidget-add-container' );

		if ( this.addNewFormLabel !== '' ) {
			this.$addContainer.append( new OO.ui.LabelWidget( {
				label: this.addNewFormLabel
			} ).$element );
		}

		this.addForm = this.getForm( {
			keyClass: 'bs-ooui-widget-keyValueInputWidget-key-input',
			valueClass: 'bs-ooui-widget-keyValueInputWidget-value-input',
			keyLabel: this.keyLabel,
			valueLabel: this.valueLabel,
			align: 'top',
			keyReadOnly: false,
			valueRequired: this.valueRequired,
			addToWidgets: false
		} );

		const layouts = this.getLayouts( {}, this.addForm );

		this.addButton = this.getAddButtonWidget();
		this.addButton.$element.addClass( 'bs-ooui-widget-keyValueInputWidget-add-btn' );
		this.addButton.on( 'click', this.onAddClick.bind( this ) );

		this.$addContainer.append( layouts, this.addButton.$element );
		this.$element.append( this.$addContainer );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getAddButtonWidget = function () {
		return new OO.ui.ButtonWidget( {
			framed: false,
			icon: 'check',
			flags: [
				'progressive'
			],
			title: mw.message( 'bs-ooui-key-value-input-widget-add-button-label' ).text()
		} );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.onAddClick = function ( e ) {
		if ( !this.addForm ) {
			return;
		}
		const keyValue = this.addForm.inputs.key.getValue();
		let valueValue = this.addForm.inputs.value.getValue();

		this.validateAddNewForm().done( function () {
			valueValue = valueValue || keyValue;

			const deleteButton = this.getDeleteButtonWidget(),
				layoutCfg = $.extend( {
					keyValue: keyValue,
					valueValue: valueValue,
					deleteWidget: deleteButton
				}, this.getDefaultLayoutConfig() ),
				layouts = this.getLayouts( layoutCfg );

			this.addEntry( layouts, deleteButton.$element );
			this.$element.find( '.bs-ooui-keyValueInputWidget-no-value-label' ).remove();

			this.emit( 'change', this );
			this.resetAddForm();
		}.bind( this ) );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.validateAddNewForm = function () {
		const dfd = $.Deferred(),
			toCheck = [];

		if ( typeof this.addForm.inputs.key.getValidity === 'function' ) {
			toCheck.push( this.addForm.inputs.key );
		}
		if ( typeof this.addForm.inputs.value.getValidity === 'function' ) {
			toCheck.push( this.addForm.inputs.value );
		}
		this.doCheckValidity( toCheck, dfd );

		return dfd.promise();
	};

	bs.ui.widget.KeyValueInputWidget.prototype.resetAddForm = function () {
		this.addForm.inputs.key.setValue( '' );
		this.addForm.inputs.value.setValue( '' );

		if ( typeof this.addForm.inputs.key.setValidityFlag === 'function' ) {
			this.addForm.inputs.key.setValidityFlag( true );
		}
		if ( typeof this.addForm.inputs.value.setValidityFlag === 'function' ) {
			this.addForm.inputs.value.setValidityFlag( true );
		}
	};

	bs.ui.widget.KeyValueInputWidget.prototype.getValue = function () {
		const value = {};
		for ( const idx in this.addedWidgets ) {
			if ( !this.addedWidgets.hasOwnProperty( idx ) ) {
				continue;
			}
			const keyWidget = this.addedWidgets[ idx ].keyWidget,
				valueWidget = this.addedWidgets[ idx ].valueWidget,
				keyValue = keyWidget.getValue();
			let valueValue = valueWidget.getValue();

			valueValue = valueValue || keyValue;
			value[ keyValue ] = valueValue;
		}

		return value;
	};

	bs.ui.widget.KeyValueInputWidget.prototype.onDeleteClick = function ( e ) {
		e.data.deleteWidget.$element.remove();
		this.removeFromAddedWidgets( e.data.keyWidget );
		const currentValue = this.getValue();
		this.addedWidgets = [];
		this.$valueContainer.html( '' );

		if ( $.isEmptyObject( currentValue ) === false ) {
			this.setValue( currentValue );
		} else {
			this.setNoValueMessage();
		}
		this.emit( 'change', this );
	};

	bs.ui.widget.KeyValueInputWidget.prototype.removeFromAddedWidgets = function ( keyWidget ) {
		for ( const idx in this.addedWidgets ) {
			const widgets = this.addedWidgets[ idx ];
			if ( widgets.keyWidget.$element.is( keyWidget.$element ) ) {
				this.addedWidgets.splice( idx, 1 );
				return true;
			}
		}
		return false;
	};

}( mediaWiki, blueSpice, jQuery, undefined ) );
