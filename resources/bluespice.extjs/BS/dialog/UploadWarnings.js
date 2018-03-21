Ext.define( 'BS.dialog.UploadWarnings', {
	extend: 'MWExt.Dialog',
	minHeight: 50,
	minWidth: 500,
	padding: null,
	title: mw.message( 'bs-upload-uploadwarningdialog-title' ).plain(),
	closeAction: 'destroy',
	cls: 'bs-extjs-dlg-upload-warning',

	apiUpload: {},

	afterInitComponent: function() {
		this.items = [];
		this.addIntro();
		this.addWarnings();
		this.addOutro();

		this.callParent(arguments);
	},

	addIntro: function() {
		this.items.push({
			cls: 'intro',
			html: mw.message( 'bs-upload-uploadwarningdialog-intro' ).plain()
		});
	},

	addOutro: function() {
		this.items.push({
			cls: 'outro',
			html: mw.message( 'bs-upload-uploadwarningdialog-outro' ).plain()
		});
	},

	/**
	 * Example:
	 * this.apiUpload = {
	 *   "result":"Warning",
	 *   "warnings": {
	 *     "duplicate": [
	 *       "000_BlueSpice-Chili.jpg"
	 *     ],
	 *   "exists": "BlueSpice-Chili.jpg"
	 *   },
	 *   "filekey":"14tu4hqpqdhs.1765v2.1.jpg",
	 *   "sessionkey":"14tu4hqpqdhs.1765v2.1.jpg"
	 * }
	 * @returns {undefined}
	 */
	addWarnings: function() {
		for ( var warning in this.apiUpload.warnings ) {
			var warnVal = this.apiUpload.warnings[warning];
			if( warning === 'exists' ) {
				this.addExistsWarning( warnVal );
				continue;
			}
			if( warning === 'duplicate' ) {
				this.addDuplicateWarning( warnVal );
				continue;
			}

			this.addUnknownWarning( warnVal );
		}
	},

	addExistsWarning: function( fileName ) {
		this.items.push({
			cls: 'warning',
			html: mw.message( 'bs-upload-uploadwarningdialog-warning-exists', fileName ).plain()
		});
	},

	addDuplicateWarning: function( dups ) {
		this.dupPanel = new Ext.Panel( {
			cls: 'warning',
			items:[{
				html: mw.message('bs-upload-uploadwarningdialog-warning-duplicate', dups.length ).text()
			}]
		});

		this.items.push( this.dupPanel );
		this.fetchDuplicates( dups );
	},

	addUnknownWarning: function( warning ) {
		this.items.push({
			cls: 'warning',
			html: mw.message( 'bs-upload-uploadwarningdialog-warning-unknown', warning ).plain()
		});
	},

	fetchDuplicates: function( dups ) {
		var api = new mw.Api();
		var me = this;
		var params = {
			action: 'query',
			titles: 'File:' + dups.join( '|File:' ),
			prop: 'imageinfo',
			iiprop: 'url',
			iiurlwidth: 64,
			indexpageids: ''
		};

		var item = '<a href="{0}" data-bs-title="{1}" target="_blank"><img alt="{1}" src="{2}" width="64" class="thumbimage">{1}</a>';

		api.get( params ).done( function ( response ) {
			var $container = $( '<div>' );
			for( var i in response.query.pages ) {
				if( i < 0 ) {
					continue;
				}
				var $item = $( '<div>' );
				$container.append( $item );

				var href = response.query.pages[i].imageinfo[0].descriptionurl;
				var src = response.query.pages[i].imageinfo[0].thumburl;
				var title = response.query.pages[i].title;

				$item.append( item.format( href, title, src ) );
			}
			me.dupPanel.add( {
				autoScroll: true,
				html: $container.html()
			});
		});
	}
});