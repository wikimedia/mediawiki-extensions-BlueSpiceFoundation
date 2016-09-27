Ext.define( 'BS.grid.FileRepo', {
	extend: 'Ext.grid.Panel',
	requires: [
		'BS.store.BSApi', 'Ext.ux.form.SearchField', 'BS.model.File',
		'BS.dialog.Upload', 'Ext.ux.grid.FiltersFeature'
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
		this.dockedItems = this.makeDockedItems();
		this.columns = this.makeColumns();
		this.items = [];

		$(document).trigger('BSGridFileRepoInitComponent', [ this, this.items ]);
		$(document).trigger('BS.grid.FileRepo.initComponent', [ this, this.items ]);

		this.callParent(arguments);

		//Bugfix for filters are not bein renderec in hidden columns
		//https://www.sencha.com/forum/showthread.php?268893
		this.on( 'columnshow', function() {
			this.filters.createFilters();
		}, this );
	},

	renderFilesize: function( val ){
		return Ext.util.Format.fileSize( val );
	},

	renderThumb: function( value, meta, record ) {
		var attr = {
			style: 'background-image:url('+value+'); display:block; height: 120px;background-position: center center; background-repeat: no-repeat;',
			href: record.get( 'file_url' ),
			target: '_blank'
		};
		return mw.html.element( 'a', attr );
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
			filterable: true,
			filter: {
				menuItems: ['ct' ] //Other types are not supported in bs-filebackend-store at the moment
			},
			dataIndex: 'page_categories',
			renderer: this.renderCategories,
			hidden: true,
			header: mw.message('bs-filerepo-headerpagecategories').plain()
		});

		this.colFileWidth = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filterable: true,
			dataIndex: 'file_width',
			hidden: true,
			header: mw.message('bs-filerepo-headerfilewidth').plain()
		});

		this.colFileHeight = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filterable: true,
			dataIndex: 'file_height',
			hidden: true,
			header: mw.message('bs-filerepo-headerfileheight').plain()
		});

		this.colFileMimetype = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filterable: true,
			dataIndex: 'file_mimetype',
			hidden: true,
			header: mw.message('bs-filerepo-headerfilemimetype').plain()
		});

		this.colFileUserText = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filterable: true,
			dataIndex: 'file_user_display_text',
			renderer: this.renderUser,
			header: mw.message('bs-filerepo-headerfileusertext').plain()
		});

		this.colFileExtension = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filterable: true,
			dataIndex: 'file_extension',
			header: mw.message('bs-filerepo-headerfileextension').plain()
		});

		this.colFileTimestamp = Ext.create( 'Ext.grid.column.Date', {
			sortable: true,
			filterable: true,
			dateFormat: 'Y-m-d H:i:s',
			dataIndex: 'file_timestamp',
			width:100,
			header: mw.message('bs-filerepo-headerfiletimestamp').plain()
		});

		this.colFileMediaType = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filterable: true,
			dataIndex: 'file_mediatype',
			hidden: true,
			header: mw.message('bs-filerepo-headerfilemediatype').plain()
		});

		this.colFileDescription = Ext.create( 'Ext.grid.column.Column', {
			sortable: true,
			filterable: true,
			dataIndex: 'file_description',
			header: mw.message('bs-filerepo-headerfiledescription').plain()
		});

		this.colFilename = Ext.create( 'Ext.grid.column.Column', {
			header: mw.message('bs-filerepo-headerfilename').plain(),
			sortable: true,
			dataIndex: 'file_name',
			flex:3,
			filterable: true,
			renderer: this.renderFileName,
		});

		this.colFilesize = Ext.create( 'Ext.grid.column.Column', {
			header: mw.message('bs-filerepo-headerfilesize').plain(),
			sortable: true,
			dataIndex: 'file_size',
			renderer: this.renderFilesize,
			width: 100,
			filterable: true
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

	makeFeatures: function() {
		return [
			new Ext.ux.grid.FiltersFeature({
				encode: true
			})
		];
	},

	makeDockedItems: function() {
		var items = [];

		this.makeTopToolbar( items );
		this.makePagingToolbar( items );

		return items;
	},

	makeTopToolbar: function( items ) {
		this.sfFilter = new Ext.ux.form.SearchField({
			fieldLabel: mw.message( 'bs-filerepo-labelfilter' ).plain(),
			labelAlign: 'right',
			flex: 3,
			store: this.store,
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
			glyph: true,
			iconCls: 'bs-icon-upload',
			tooltip: mw.message( 'bs-filerepo-labelupload' ).plain()
		});

		this.btnUpload.on('click', this.btnUploadClick, this);

		this.dlgUpload = new BS.dialog.Upload({
			allowedFileExtensions: mw.config.get( 'bsFileExtensions' )
				.concat( mw.config.get( 'bsImageExtensions' ) )
		});

		this.dlgUpload.on ( 'ok', this.onUploadComplete, this );

		return this.btnUpload;
	}
});