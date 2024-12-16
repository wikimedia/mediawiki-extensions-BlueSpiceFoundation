( function ( mw, bs, $, document ) {
	bs.ui.widget.KeyObjectInputWidget = function ( cfg ) {
		this.objectConfiguration = cfg.objectConfiguration || {};
		bs.ui.widget.KeyObjectInputWidget.parent.call( this, cfg );

		this.$element.addClass( 'bs-ooui-widget-keyObjectInputWidget' );
	};

	OO.inheritClass( bs.ui.widget.KeyObjectInputWidget, bs.ui.widget.KeyValueInputWidget );

	bs.ui.widget.KeyObjectInputWidget.prototype.getValue = function () {
		const value = {};
		for ( const idx in this.addedWidgets ) {
			const keyWidget = this.addedWidgets[ idx ].keyWidget,
				valueWidget = this.addedWidgets[ idx ].valueWidget,
				keyValue = keyWidget.getValue();
			let valueValue = valueWidget.getValue();

			valueValue = valueValue || keyValue;
			value[ keyValue ] = valueValue;
		}

		return value;
	};

	bs.ui.widget.KeyObjectInputWidget.prototype.getValueLayout = function ( valueInput, cfg ) {
		return new OO.ui.FieldsetLayout( {
			items: valueInput.getLayouts(),
			classes: [ 'keyObjectInputWidget-value-layout' ]
		} );
	};

	bs.ui.widget.KeyObjectInputWidget.prototype.getDeleteButtonWidget = function () {
		return new OO.ui.ButtonWidget( {
			framed: false,
			icon: 'close',
			classes: [ 'bs-ooui-widget-keyObjectInputWidget-remove-btn' ]
		} );
	};

	bs.ui.widget.KeyObjectInputWidget.prototype.getAddButtonWidget = function () {
		return new OO.ui.ButtonWidget( {
			framed: false,
			icon: 'check',
			flags: [
				'progressive'
			],
			label: mw.message( 'bs-ooui-key-value-input-widget-add-button-label' ).plain(),
			classes: [ 'keyObjectInputWidget-addbutton' ]
		} );
	};

	bs.ui.widget.KeyObjectInputWidget.prototype.getValueInput = function ( cfg ) {
		return new bs.ui.widget.ObjectInputWidget( {
			values: cfg.valueValue,
			inputs: this.objectConfiguration,
			classes: [ 'bs-ooui-widget-keyValueInputWidget-value-input' ]
		} );
	};
}( mediaWiki, blueSpice, jQuery, undefined ) );
