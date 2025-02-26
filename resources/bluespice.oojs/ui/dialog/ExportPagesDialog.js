/**
 * @param {Object} cfg {
 *     'pages': Array of { dbkey: string, display: string },
 *     'listName': string Default name for the export list
 * }
 * @constructor
 */
bs.ui.dialog.ExportPagesDialog = function ( cfg ) {
	cfg = cfg || {};
	cfg.size = 'medium';
	bs.ui.dialog.ExportPagesDialog.parent.call( this, cfg );

	this.pages = cfg.pages || [];
	this.listName = cfg.listName || '';
};

OO.inheritClass( bs.ui.dialog.ExportPagesDialog, OO.ui.ProcessDialog );

bs.ui.dialog.ExportPagesDialog.static.name = 'BSExportPagesDialog';
bs.ui.dialog.ExportPagesDialog.static.title = mw.message( 'bs-exportpages-dialog-title' ).text();
bs.ui.dialog.ExportPagesDialog.static.actions = [
	{
		action: 'export',
		label: mw.message( 'bs-ui-generic-save' ).text(),
		flags: [ 'primary', 'progressive' ],
		modes: [ 'export' ]
	},
	{
		action: 'close',
		flags: [ 'safe' ],
		label: mw.message( 'bs-ui-generic-close' ).text(),
		modes: [ 'export', 'result' ]
	}
];

bs.ui.dialog.ExportPagesDialog.prototype.initialize = function () {
	bs.ui.dialog.ExportPagesDialog.parent.prototype.initialize.call( this );
	this.actions.setMode( 'export' );
	this.panel = new OO.ui.PanelLayout( {
		expanded: false,
		padded: true
	} );

	this.nameInput = new OO.ui.TextInputWidget( {
		value: this.listName || '',
		required: true
	} );
	this.nameInput.connect( this, { change: 'getValidity' } );
	this.typeSelector = new OO.ui.RadioSelectInputWidget( {
		value: 'plain',
		options: [
			{
				data: 'plain',
				label: mw.msg( 'bs-pageexport-list-format-plain-label' )
			},
			{
				data: 'links',
				label: mw.msg( 'bs-pageexport-list-format-link-label' )
			}
		]
	} );
	this.overwriteCheck = new OO.ui.CheckboxInputWidget( {
		selected: false
	} );

	this.panel.$element.append( new OO.ui.FieldsetLayout( {
		items: [
			new OO.ui.FieldLayout( this.nameInput, {
				label: mw.message( 'bs-pageexport-list-name-label' ).text(),
				align: 'left'
			} ),
			new OO.ui.FieldLayout( this.typeSelector, {
				label: mw.message( 'bs-pageexport-list-format-label' ).text(),
				align: 'left'
			} ),
			new OO.ui.FieldLayout( this.overwriteCheck, {
				label: mw.message( 'bs-pageexport-overwrite-label' ).text(),
				align: 'left'
			} )
		]
	} ).$element );

	this.store = new OOJSPlus.ui.data.store.Store( {
		data: this.pages,
		pageSize: 25
	} );
	this.grid = new OOJSPlus.ui.data.GridWidget( {
		store: this.store,
		multiSelect: true,
		multiSelectSelectedByDefault: true,
		noHeader: true,
		classes: [ 'bs-exportpages-grid' ],
		columns: {
			dbkey: {
				valueParser: function ( value, row ) {
					if ( row.hasOwnProperty( 'display' ) && row.display ) {
						return row.display;
					}
					return value;
				}
			}
		}
	} );
	this.panel.$element.append( this.grid.$element );

	this.$body.append( this.panel.$element );
	this.getValidity();
};

bs.ui.dialog.ExportPagesDialog.prototype.getValidity = async function () {
	const dfd = $.Deferred();
	try {
		await this.nameInput.getValidity();
		if ( !this.grid.getSelectedRows().length ) {
			throw new Error();
		}
		this.actions.setAbilities( { export: true } );
		dfd.resolve();
	} catch ( e ) {
		this.actions.setAbilities( { export: false } );
		dfd.reject();
	}

	return dfd.promise();
};

bs.ui.dialog.ExportPagesDialog.prototype.getActionProcess = function ( action ) {
	return bs.ui.dialog.ExportPagesDialog.parent.prototype.getActionProcess.call( this, action ).next( async function () {
		if ( action === 'export' ) {
			const dfd = $.Deferred();
			try {
				await this.getValidity();
				const selected = this.grid.getSelectedRows();
				const pages = [];
				selected.forEach( function ( item ) {
					pages.push( item.dbkey );
				} );
				if ( pages.length === 0 ) {
					throw new Error( 'Empty selection' );
				} else {
					await this.export( {
						pages: pages,
						listName: this.nameInput.getValue(),
						format: this.typeSelector.getValue(),
						overwrite: this.overwriteCheck.isSelected()
					} );
				}
			} catch ( e ) {
				console.error( e );
				dfd.reject();
			}
			return dfd.promise();
		}
		if ( action === 'cancel' || action === 'close' ) {
			this.close();
		}
	}, this );
};

bs.ui.dialog.ExportPagesDialog.prototype.export = async function ( data ) {
	const dfd = $.Deferred();
	try {
		let text = mw.config.get( 'bsgPageCollectionPrefix' );
		text += '/';
		text += data.listName;
		const targetTitle = mw.Title.newFromText( text, 8 );
		const pageContent = await this.getTargetPageContent( targetTitle );
		const formatted = this.formatExportContent( pageContent, data );
		if ( !formatted ) {
			dfd.reject();
		}
		await this.savePage( targetTitle, formatted );
		this.panel.$element.html(
			new OO.ui.LabelWidget( {
				label: new OO.ui.HtmlSnippet(
					mw.message(
						'bs-exportpages-success-text',
						targetTitle.getPrefixedDb(),
						targetTitle.getPrefixedText()
					).parse()
				)
			} ).$element
		);
		this.popPending();
		this.actions.setMode( 'result' );
		this.updateSize();
		dfd.resolve();
	} catch ( e ) {
		dfd.reject( e );
	}

	return dfd.promise();
};

bs.ui.dialog.ExportPagesDialog.prototype.getTargetPageContent = async function ( targetTitle ) {
	const dfd = $.Deferred();
	new mw.Api().get( {
		action: 'query',
		titles: targetTitle.getPrefixedText(),
		prop: 'revisions',
		rvprop: 'content',
		indexpageids: ''
	} ).fail( function ( code, errResp ) {
		dfd.reject( code, errResp );
	} ).done( function ( response ) {
		const pageId = response.query.pageids[ 0 ],
			pageInfo = response.query.pages[ pageId ];
		if ( !pageInfo.missing && pageInfo.revisions && pageInfo.revisions[ 0 ] ) {
			dfd.resolve( pageInfo.revisions[ 0 ][ '*' ] );
		} else {
			dfd.resolve( '' );
		}
	} );
	return dfd.promise();
};

bs.ui.dialog.ExportPagesDialog.prototype.formatExportContent = function ( content, data ) {
	if ( data.pages.length === 0 ) {
		return;
	}

	let formatted = '';
	for ( let i = 0; i < data.pages.length; i++ ) {
		const page = data.pages[ i ];
		if ( data.format === 'links' ) {
			formatted += '* [[' + page + ']]\n';
		} else {
			formatted += '* ' + page + '\n';
		}
	}
	if ( data.overwrite === false ) {
		return content + '\n' + formatted;
	}
	return formatted;
};

bs.ui.dialog.ExportPagesDialog.prototype.savePage = async function ( targetTitle, formatted ) {
	const dfd = $.Deferred();
	new mw.Api().postWithToken( 'csrf', {
		action: 'edit',
		title: targetTitle.getPrefixedText(),
		summary: mw.message( 'bs-export-search-summary-text' ).plain(),
		text: formatted
	} ).done( function ( response ) {
		dfd.resolve( response );
	} ).fail( function ( code, err ) {
		dfd.reject( code, err );
	} );

	return dfd.promise();
};
