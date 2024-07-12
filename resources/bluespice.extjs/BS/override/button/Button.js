Ext.define( 'BS.override.button.Button', {
	override: 'Ext.button.Button',
	setTooltip: function ( tooltip, initial ) {
		this.callParent( [tooltip, initial] );

		if ( this.rendered && tooltip ) {
			this.el.dom.setAttribute( 'aria-label', tooltip );
		}

		return this;
	}
} );
