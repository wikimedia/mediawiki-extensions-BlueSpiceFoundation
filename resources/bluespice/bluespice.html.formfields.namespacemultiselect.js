mw.loader.using( 'ext.bluespice.extjs' ).done( function() {
	Ext.require( 'BS.model.Namespace', function() {
		$( '.bs-html-formfield-namespacemultiselect' ).each( function() {
			//TODO: Implement dedicated ExtJS component or OOJS UI widget
			var $assocField = $( this ).next( 'input.bs-html-formfield-hidden' );
			var storeData = $( this ).data( 'bs-store-data' );
			var currentNamespaceIds = $assocField.val().split( '|' );

			var field = new Ext.form.field.Tag({
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
			});

			field.on( 'change', function( sender, newValue, oldValue, eOpts ) {
				$assocField.val( newValue.join( '|' ) );

				//We need to explicitly trigger 'change', as otherwise the
				//submit button will not be released on the first change in
				//'mediawiki.special.preferences.confirmClose.js'
				$assocField.trigger( 'change' );
			} );
		});
	});
});