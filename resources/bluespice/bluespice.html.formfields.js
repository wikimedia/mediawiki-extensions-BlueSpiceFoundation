mw.loader.using( 'ext.bluespice.extjs' ).done( function() {
	Ext.require( 'BS.model.Namespace', function() {
		$( '.bs-html-formfield-namespacemultiselect' ).each( function() {
			//TODO: Implement dedicated ExtJS component or OOJS UI widget
			var $assocField = $( this ).next( 'input[type=hidden]' );
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
			} );
		});
	});
});