( function ( mw, $, bs, d, undefined ){

	//We only want the code to run (and therefore the resources to be loaded)
	//when a user actually hovers an appropriate element!
	$(d).on( 'mouseover', '.bs-tooltip-link', function(){
		var conf = {
			target: this,
			title: $(this).data('bs-tt-title') || '&#160;',
			html: $(this).data('bs-tt-html') || '',
			contentEl: $(this).data('bs-tt-target') || document,
			anchor: $(this).data('bs-tt-anchor') || 'top',
			autoHide: $(this).data('bs-tt-autohide') || false,
			maxHeight: $(this).data('bs-tt-maxheight'),
			minWidth: $(this).data('bs-tt-maxheight') || 100,
			overflowX: 'auto',
			overflowY: 'auto'
		};

		var me = this;
		//Make sure there is only one instance of the tooltip build from the data
		//attributes of this particular element. Otherwise everytime a user hovers
		//the element a new tooltip instance (with all the associated DOM elements)
		//would be created.
		if( !me.bsToolTip ) {
			mw.loader.using( 'ext.bluespice.extjs' ).done(function() {
				me.bsToolTip = Ext.create( 'Ext.tip.ToolTip', conf );
				me.bsToolTip.show(); //We need to show the tooltip manually at
				//the time we create it, because Ext.tip.Tooltip does not
				//recognize the mouseover event it got created in.
			});
		}
	});
}( mediaWiki, jQuery, blueSpice, document ) );
