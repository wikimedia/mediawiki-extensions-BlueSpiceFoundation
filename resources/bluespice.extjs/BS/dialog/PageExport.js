Ext.define( 'BS.dialog.PageExport', {
	extend: 'MWExt.Dialog',
	requires: [],
	width: 350,
	title: mw.message( 'bs-pagecollection-prefix' ).plain(),
	pages: [],
	prefixMessageKey: 'bs-pagecollection-prefix',
	namespaceKey: 0,
	defaultName: '',
	targetTitle: null,
	targetPageContent: '',
	formattedExportContent: '',

	afterInitComponent: function() {
		var namespaceIds = mw.config.get( 'wgNamespaceIds' );
		this.namespaceKey = namespaceIds.mediawiki;

		this.tbName = Ext.create( 'Ext.form.field.Text', {
			fieldLabel: mw.message( 'bs-extjs-pageexport-list-name-label' ).plain(),
			labelAlign: 'right',
			value: this.defaultName
		});
		this.rgFormat = Ext.create('Ext.form.RadioGroup', {
			value: 'plain',
			flex: 1,
			items: [
				{
					boxLabel: mw.message( 'bs-extjs-pageexport-list-format-plain-label' ).plain(),
					id: 'export-format-plain',
					name: 'export-format',
					inputValue: 'plain',
					checked: true
				},
				{
					boxLabel: mw.message('bs-extjs-pageexport-list-format-link-label').plain(),
					id: 'export-format-link',
					name: 'export-format',
					inputValue: 'link'
				}
			]
		});

		this.cbxOverwrite = Ext.create( 'Ext.form.field.Checkbox', {
			value: '0',
			fieldLabel: mw.message( 'bs-extjs-pageexport-overwrite-label' ).plain(),
			labelAlign: 'right'
		});

		this.preparePages();
		this.makePagesStore();
		this.makePagesGrid();

		this.items = [
			this.tbName,
			{
				xtype: 'fieldcontainer',
				fieldLabel: mw.message( 'bs-extjs-pageexport-list-format-label' ).plain(),
				labelAlign: 'right',
				layout: 'hbox',
				items: [
					this.rgFormat
				]
			},
			this.cbxOverwrite,
			this.gdPages
		];
		this.callParent(arguments);
	},

	preparePages: function() {
		var finalPages = [];
		$.each( this.pages, function( idx, page ) {
			finalPages.push({
				included: true,
				pageTitle: page
			});
		});
		this.pages = finalPages;
	},

	makePagesStore: function() {
		this.store = Ext.create( 'Ext.data.Store', {
			fields: [ 'included', 'pageTitle' ],
			data: { 'pages': this.pages },
			proxy: {
				type: 'memory',
				reader: {
					type: 'json',
					rootProperty: 'pages'
				}
			}
		} );
	},

	makePagesGrid: function() {
		this.gdPages = Ext.create( 'Ext.grid.Panel', {
			store: this.store,
			height: 500,
			scrollable: true,
			columns: [
				{
					header: '',
					dataIndex: 'included',
					xtype: 'checkcolumn',
					width: 40,
					resizeable: false,
					groupable: false,
					sortable: false
				},
				{
					header: 'Page title',
					dataIndex: 'pageTitle',
					width: 300
				}
			]
		} );
	},

	onBtnOKClick: function () {
		var me = this;

		me.data = me.getData();
		if( me.data.name === '' || me.data.pages.length === 0 ) {
			bs.util.alert(
				'bs-pageexport-alert-required',
				{
					labelMsg: 'bs-extjs-pageexport-general-error',
					text: mw.message( 'bs-extjs-pageexport-required-text').plain()
				}
			);
			return;
		}

		me.setTargetTitle();
		if( this.targetTitle === null ) {
			bs.util.alert(
				'bs-pageexport-alert-invalid-title',
				{
					labelMsg: 'bs-extjs-pageexport-general-error',
					text: mw.message( 'bs-extjs-pageexport-error-invalid-title').plain()
				}
			);
			return;
		}

		me.setLoading( true );
		var exportPromise = me.doExport();

		exportPromise.fail( function( code, errResponse ) {
			me.setLoading( false );
			bs.util.alert(
				'bs-pageexport-alert-fail',
				{
					titleMsg: 'bs-extjs-pageexport-general-error',
					text: errResponse.error.info
				}
			);
			me.close();
		} );

		exportPromise.done( function( response ) {
			me.setLoading( false );
			var link = $('<a></a>').attr( 'href', me.targetTitle.getUrl() );
			link.attr( 'title', me.targetTitle.getPrefixedText() );
			link.html( me.targetTitle.getPrefixedText() );

			//trick to get markup of anchor tag
			linkHtml = $( '<div>' ).html( link ).html();

			bs.util.alert(
				'bs-pageexport-alert-success',
				{
					titleMsg: 'bs-extjs-pageexport-success',
					text: mw.message(
						'bs-extjs-pageexport-success-text',
						linkHtml
					).plain()
				}
			);
			me.close();
		} );
	},

	setTargetTitle: function() {
		var text = mw.message( this.prefixMessageKey ).plain();
		text += "/";
		text += this.data.name;
		this.targetTitle = mw.Title.newFromText( text, this.namespaceKey );
	},

	doExport: function() {
		var me = this,
			getTargetPageInfoAPI = new mw.Api(),
			dfd = $.Deferred();

		getTargetPageInfoAPI.get( {
			action: 'query',
			titles: me.targetTitle.getPrefixedText(),
			prop: 'revisions',
			rvprop: 'content',
			indexpageids : ''
		} )
		.fail( function( code, errResp ) {
			dfd.reject( code, errResp );
		} )
		.done( function( response ) {
			var pageId = response.query.pageids[0];
			var pageInfo = response.query.pages[pageId];
			if( !pageInfo.missing && pageInfo.revisions && pageInfo.revisions[0] ) {
				me.targetPageContent = pageInfo.revisions[0]['*'];
			}

			var savePromise = me.savePage();
			savePromise.fail( function( code, error ) {
				dfd.reject( code, error );
			});
			savePromise.done( function( response ){
				dfd.resolve( response );
			});
		} );

		return dfd.promise();
	},

	savePage: function() {
		var me = this,
			savePageAPI = new mw.Api(),
			dfd = $.Deferred();

		me.formatExportContent();

		if( me.data.overwrite === false ) {
			me.formattedExportContent =
					me.targetPageContent + "\n" + me.formattedExportContent;
		}

		savePageAPI.postWithToken( 'edit', {
			action: 'edit',
			title: me.targetTitle.getPrefixedText(),
			summary: mw.message( 'bs-extjs-pageexport-edit-summary-text' ).plain(),
			text: me.formattedExportContent
		} ).done( function( response ) {
			dfd.resolve( response );
		} ).fail( function( code, err ) {
			dfd.reject( code, err );
		} );

		return dfd.promise();
	},

	formatExportContent: function() {
		var me = this;
		if( me.data.pages.length === 0 ) {
			return;
		}
		me.formattedExportContent = '';

		$.each( me.data.pages, function( idx, page ){
			var exportLine = '* ';
			if( me.data.link ) {
				exportLine += "[[" + page + "]]\n";
			} else {
				exportLine += page + "\n";
			}
			me.formattedExportContent += exportLine;
		} );
	},

	getData: function() {
		var me = this;
		var pages = [];
		var range = this.store.getRange();
		$.each( range, function( idx, item ) {
			if( item.data.included === false ) {
				return;
			}
			pages.push( item.data.pageTitle );
		});

		var data = {
			pages: pages,
			link: false,
			overwrite: false,
			name: me.tbName.getValue()
		};

		if( me.rgFormat.getValue()['export-format'] === 'link' ) {
			data.link = true;
		}

		if( me.cbxOverwrite.getValue() ) {
			data.overwrite = true;
		}

		return data;
	}
});
