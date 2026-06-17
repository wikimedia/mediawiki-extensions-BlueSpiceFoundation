mw.loader.using( 'ext.bluespice.extjs' ).done( () => {
	Ext.require( 'BS.model.Namespace', () => {
		$( '.bs-html-formfield-namespacemultiselect' ).each( function () {
			// TODO: Implement dedicated ExtJS component or OOJS UI widget
			const $assocField = $( this ).next( 'input.bs-html-formfield-hidden' ),
				storeData = $( this ).data( 'bs-store-data' ),
				currentNamespaceIds = $assocField.val().split( '|' ),
				field = new Ext.form.field.Tag( {
					displayField: 'namespaceName',
					valueField: 'namespaceId',
					renderTo: this,
					typeAhead: true,
					queryMode: 'local',
					store: {
						model: 'BS.model.Namespace',
						data: storeData
					},
					value: currentNamespaceIds
				} );

			field.on( 'change', ( sender, newValue, oldValue, eOpts ) => { // eslint-disable-line no-unused-vars
				$assocField.val( newValue.join( '|' ) );

				// We need to explicitly trigger 'change', as otherwise the
				// submit button will not be released on the first change in
				// 'mediawiki.special.preferences.confirmClose.js'
				$assocField.trigger( 'change' );
			} );
		} );
	} );
} );
