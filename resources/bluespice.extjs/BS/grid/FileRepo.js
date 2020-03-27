Ext.define( 'BS.grid.FileRepo', {
	extend: 'Ext.grid.Panel',
	requires: [
		'BS.store.BSApi', 'MWExt.form.field.Search', 'BS.model.File',
		'BS.dialog.Upload'
	],
	cls: 'bs-filerepo-grid',
	pageSize : 50,

	//Custom settings
	uploaderCfg: null,

	initComponent: function() {
		this.store = new BS.store.BSApi({
			apiAction: 'bs-filebackend-store',
			model: 'BS.model.File',
			sorters: [{
				property: 'file_timestamp',
				direction: 'DESC'
			}],
			proxy:{
				extraParams: {
					limit: this.pageSize
				}
			},
			pageSize: this.pageSize
		});

		this.features = this.makeFeatures();
		this.plugins = this.makePlugins();
		this.dockedItems = this.makeDockedItems();
		this.columns = this.makeColumns();
		this.items = [];

		$(document).trigger('BSGridFileRepoInitComponent', [ this, this.items ]);
		$(document).trigger('BS.grid.FileRepo.initComponent', [ this, this.items ]);

		this.callParent(arguments);
	},

	renderFilesize: function( val ){
		return Ext.util.Format.fileSize( val );
	},

	renderThumb: function( value, meta, record ) {
		var title = new mw.Title(
			record.get( 'page_title' ),
			record.get( 'page_namespace' )
		);
		var attr = {
			style: 'background-image:url('+value+'); display:block; height: 120px; background-position: center center; background-repeat: no-repeat;',
			href: record.get( 'file_url' ),
			target: '_blank',
			class: 'bs-thumb-link',
			'data-bs-title': title.getPrefixedText()
		};
		var attrImg = {
			src: record.get( 'file_thumbnail_url' ),
			style: 'max-height: 100%; max-width: 100%; width: auto; height: auto; position: absolute; top: 0; bottom: 0; left: 0; right: 0; margin: auto;',
			'data-file-width': record.get( 'file_width' ),
			'data-file-height': record.get( 'file_height' )
		};
		// is thumb an image or a fileicon?
		var ret = mw.html.element( 'a', attr ); // thumb is a fileicon
		if( record.get( 'file_height' ) !== 0 ) {
			// thumb is an image
			attr.class = 'bs-thumb-link image';
			attr.style = 'display: block; height: 120px; width: 80px; position:relative';
			var img = mw.html.element( 'img', attrImg );
			ret = mw.html.element( 'a', attr, new mw.html.Raw( img ) );
		}
		return ret;
	},

	renderBool: function( value ){
		if( value === true){
			return mw.message('bs-filerepo-yes').plain();
		} else {
			return mw.message('bs-filerepo-no').plain();
		}
	},

	renderUser: function( value, meta, record ) {
		return record.get( 'file_user_link' );
	},

	renderCategories: function( value, meta, record ) {
		return record.get( 'page_categories_links' ).join( ', ' );
	},

	renderFileName: function( value, meta, record ) {
		return record.get( 'page_link' );
	},

	btnUploadClick: function(sender, event) {
		this.dlgUpload.show();
	},

	onUploadComplete: function(sender, event){
		this.store.reload();
	},

	onSelectPageSize: function (sender, event){
		var pageSize = this.cbPageSize.getValue();
		this.store.pageSize = pageSize;
		this.store.proxy.extraParams.limit = pageSize;
		this.store.reload();
	},

	makeColumns: function() {
		this.colFileThumb = Ext.create( 'Ext.grid.column.Column', {
			sortable: false,
			filterable: false,
			dataIndex: 'file_thumbnail_url',
			renderer: this.renderThumb,
			width: 60,
			header: mw.message('bs-filerepo-headerfilethumbnail').plain()
		});

		this.colPageCategories = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filter: {
				type: 'string'
			},
			dataIndex: 'page_categories',
			renderer: this.renderCategories,
			hidden: true,
			header: mw.message('bs-filerepo-headerpagecategories').plain()
		});

		this.colFileWidth = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filter: {
				type: 'numeric'
			},
			dataIndex: 'file_width',
			hidden: true,
			header: mw.message('bs-filerepo-headerfilewidth').plain()
		});

		this.colFileHeight = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filter: {
				type: 'numeric'
			},
			dataIndex: 'file_height',
			hidden: true,
			header: mw.message('bs-filerepo-headerfileheight').plain()
		});

		this.colFileMimetype = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filter: {
				type: 'string'
			},
			dataIndex: 'file_mimetype',
			hidden: true,
			header: mw.message('bs-filerepo-headerfilemimetype').plain()
		});

		this.colFileUserText = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filter: {
				type: 'string'
			},
			dataIndex: 'file_user_display_text',
			renderer: this.renderUser,
			header: mw.message('bs-filerepo-headerfileusertext').plain()
		});

		this.colFileExtension = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filter: {
				type: 'string'
			},
			dataIndex: 'file_extension',
			header: mw.message('bs-filerepo-headerfileextension').plain()
		});

		this.colFileTimestamp = Ext.create( 'Ext.grid.column.Date', {
			sortable: true,
			filter: {
				type: 'date'
			},
			dateFormat: 'Y-m-d H:i:s',
			dataIndex: 'file_timestamp',
			width:100,
			header: mw.message('bs-filerepo-headerfiletimestamp').plain()
		});

		this.colFileMediaType = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filter: {
				type: 'string'
			},
			dataIndex: 'file_mediatype',
			hidden: true,
			header: mw.message('bs-filerepo-headerfilemediatype').plain()
		});

		this.colFileDescription = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filter: {
				type: 'string'
			},
			dataIndex: 'file_description',
			header: mw.message('bs-filerepo-headerfiledescription').plain()
		});

		this.colFilename = Ext.create( 'Ext.grid.column.Column', {
			header: mw.message('bs-filerepo-headerfilename').plain(),
			sortable: true,
			filter: {
				type: 'string'
			},
			dataIndex: 'file_name',
			flex:3,
			renderer: this.renderFileName,
		});

		this.colFilesize = Ext.create( 'Ext.grid.column.Column', {
			header: mw.message('bs-filerepo-headerfilesize').plain(),
			sortable: true,
			filter: {
				type: 'numeric'
			},
			dataIndex: 'file_size',
			renderer: this.renderFilesize,
			width: 100
		});

		return {
			items: [
				this.colFileThumb,
				this.colFilename,
				this.colFilesize,
				this.colFileUserText,
				this.colFileDescription,
				this.colPageCategories,
				this.colFileExtension,
				this.colFileMimetype,
				this.colFileWidth,
				this.colFileHeight,
				this.colFileTimestamp,
				this.colFileMediaType
			],
			defaults: {
				flex: 1
			}
		};
	},

	makeFeatures: function() {},

	makePlugins: function() {
		return [
			'gridfilters'
		];
	},

	makeDockedItems: function() {
		var items = [];

		this.makeTopToolbar( items );
		this.makePagingToolbar( items );

		return items;
	},

	makeTopToolbar: function( items ) {
		this.sfFilter = new MWExt.form.field.Search({
			fieldLabel: mw.message( 'bs-filerepo-labelfilter' ).plain(),
			labelAlign: 'right',
			flex: 3,
			store: this.store,
			paramName: 'file_name',
			listeners: {
				change: function ( field, newValue, oldValue, eOpts ) {
					field.onTrigger2Click();
					return true;
				}
			}
		});

		var toolBarItems = [
			this.sfFilter
		];

		if( this.uploaderCfg ) {
			toolBarItems.push(
				this.makeUploader( this.uploaderCfg )
			);
		}

		this.tbTop = new Ext.toolbar.Toolbar({
			dock: 'top',
			items: toolBarItems
		});

		items.push(
			this.tbTop
		);
	},

	makePagingToolbar: function( items ) {
		this.cbPageSize = new Ext.form.ComboBox({
			fieldLabel: mw.message ( 'bs-filerepo-pagesize' ).plain(),
			labelAlign: 'right',
			autoSelect: true,
			forceSelection: true,
			triggerAction: 'all',
			mode: 'local',
			store: new Ext.data.SimpleStore({
				fields: ['text', 'value'],
				data: [
					['20', 20],
					['50', 50],
					['100', 100],
					['200', 200],
					['500', 500]
				]
			}),
			value: this.pageSize,
			labelWidth: 120,
			flex: 2,
			valueField: 'value',
			displayField: 'text'
		});

		this.cbPageSize.on ('select', this.onSelectPageSize, this);

		items.push( new Ext.PagingToolbar({
				dock: 'bottom',
				store: this.store,
				displayInfo: true,
				items: [
					this.cbPageSize
				]
			})
		);
	},

	makeUploader: function( cfg ) {
		this.btnUpload = new Ext.Button({
			iconCls: 'bs-icon-upload',
			tooltip: mw.message( 'bs-filerepo-labelupload' ).plain()
		});

		this.btnUpload.on('click', this.btnUploadClick, this);

		this.dlgUpload = new BS.dialog.Upload({
			allowedFileExtensions: mw.config.get( 'bsFileExtensions' ).concat(
				mw.config.get( 'bsImageExtensions' )
			),
			uploadPanelCfg: cfg
		});

		this.dlgUpload.on ( 'ok', this.onUploadComplete, this );

		return this.btnUpload;
	}
});