Ext.define( 'BS.flyout.TabbedDataViewBase', {
	extend: 'Ext.tab.Panel',
	requires: [ 'BS.store.BSApi', 'BS.model.Title' ],
	cls: 'bs-flyout-tabbed-data-view',

	/* component specific */
	commonStore: null,
	commonStoreApiAction: 'bs-wikipage-store',
	pageSize: 25,
	defaultTab: '',

	constructor: function( cfg ) {
		cfg.height = cfg.height || this.calcDefaultHeight( cfg.renderTo );
		this.callParent( arguments );
	},

	calcDefaultHeight: function ( target ) {
		return $(target).height();
	},

	initComponent: function() {
		this.commonStore = this.makeCommonStore();
		this.items = this.makeTabs();

		return this.callParent( arguments );
	},

	onRender: function ( parentNode, containerIdx ) {
		//TODO: This can probably be done with ExtJS too
		$( this.getEl().dom ).on( 'click', '.actions a', $.proxy( function( e ) {
			e.preventDefault();

			var recordIdx = $( e.target ).parents( '.thumbnail' )
				.data( 'recordindex' );

			var record = this.commonStore.getAt( recordIdx );
			this.showItemMenu( record, e.target );

			return false;
		}, this ) );

		$( this.getEl().dom ).on( 'click', '.thumb-add a', $.proxy( function( e ) {

			this.onThumbAddClicked();

			if( $( e.target ).attr( 'href' ) === '#' ) {
				return false;
			}
		}, this ) );

		if( this.getInitialActiveTab() ) {
			this.setActiveTab( this.getInitialActiveTab() );
		}
		return this.callParent( arguments );
	},

	makeTabs: function() {
		this.tabDataView = this.makeDataViewPanel();
		this.tabGrid = this.makeGridPanel();

		return [
			this.tabDataView,
			this.tabGrid
		];
	},

	makeDataViewPanel: function() {
		return {
			iconCls: 'icon-thumbs',
			id: 'dataviewpanel',
			title: mw.message( 'bs-extjs-flyout-tab-thumbs-label' ).text(),
			tooltip: mw.message( 'bs-extjs-flyout-tab-thumbs-title' ).text(),
			cls: 'preview',
			items: [ new Ext.view.View( {
				store: this.commonStore,
				tpl: this.makeDataViewPanelTemplate(),
				itemSelector: '.storeitem'
			} ) ],
			dockedItems: [
				new Ext.toolbar.Paging( {
					pageSize: this.pageSize,
					store: this.commonStore,
					dock: 'bottom',
					displayInfo : true
				} )
			]
		};
	},

	makeDataViewPanelTemplate: function() {
		var me = this;
		var addIcon = this.makeDataViewAddIcon();

		return new Ext.XTemplate(
			addIcon,
			'<tpl for=".">',
				'<div class="thumbnail storeitem <tpl if="this.hasMenu(values)">has-actions</tpl>">',
					'<div class="caption">',
						'<a style="display:block;" href="{[this.makeMainLinkUrl(values)]}" title="Read">',
							'<span class="title">{[this.makeThumbnailCaptionTitle(values)]}</span>',
							'<div class="image">',
								'<img src="{[this.makeThumbnailImageSrc(values)]}" title="{[this.makeThumbnailImageTitle(values)]}" alt="{[this.makeThumbnailImageAlt(values)]}" onload="$(this).parent().addClass( \'loaded\' );">',
							'</div>',
							'<ul class="meta">',
								'<tpl for="this.makeMetaItems(values)">',
									'<li>{itemHtml}</li>',
								'</tpl>',
							'</ul>',
						'</a>',
					'</div>',
					'<tpl if="this.hasMenu(values)">',
						'<div class="actions">',
							'<a class="menu-trigger" href="#" title="{[this.makeToolsLinkTitle()]}">{[this.makeToolsLinkText()]}</a>',
						'</div>',
					'</tpl>',
				'</div>',
			'</tpl>',
			'<div class="x-clear"></div>',
			{
				disableFormats: true,
				makeToolsLinkText: function() {
					return me.makeToolsLinkText();
				},
				makeToolsLinkTitle: function() {
					return me.makeToolsLinkTitle();
				},
				makeMainLinkUrl: function( values ) {
					return me.makeDataViewItemLinkUrl( values );
				},
				makeThumbnailImageSrc: function( values ) {
					return me.makeDataViewThumbnailImageSrc( values );
				},
				makeThumbnailImageTitle: function( values ) {
					return me.makeDataViewThumbnailImageTitle( values );
				},
				makeThumbnailImageAlt: function( values ) {
					return me.makeDataViewThumbnailImageAlt( values );
				},
				makeThumbnailCaptionTitle: function( values ) {
					return me.makeDataViewThumbnailCaptionTitle( values );
				},
				hasMenu: function( values ) {
					return me.dataViewItemHasMenu( values );
				},
				makeMetaItems: function( values ) {
					return me.makeDataViewItemMetaItems( values );
				}
			}
		);
	},

	makeToolsLinkText: function() {
		return mw.message( 'bs-extjs-tools-trigger-text' ).text();
	},

	makeToolsLinkTitle: function() {
		return mw.message( 'bs-extjs-tools-trigger-title' ).text();
	},

	makeDataViewItemLinkUrl: function( values ) {
		return mw.util.getUrl( values.page_prefixedtext );
	},

	/**
	 * E.g. <scriptpath>/dynamic_file.php?module=articlepreviewimage&width=160px&titletext=Main Page
	 * @param {object} values
	 * @returns {String}
	 */
	makeDataViewThumbnailImageSrc: function( values ) {
		var query =  {
			module: this.makeDataViewThumbImageModuleName(),
			width: '320px',
			titletext: this.makeDataViewThumbImageTitletextValue( values )
		};

		var revid = this.makeDataViewThumbImageRevidValue( values );
		if( revid > 0 ) {
			query.revid = revid;
		}

		//TODO: Create and use abtraction for 'dynamic_file' in 'bs.util';
		var url = mw.util.wikiScript( 'dynamic_file' ) + '?' + $.param( query );
		return url;
	},

	makeDataViewThumbImageModuleName: function() {
		return 'articlepreviewimage';
	},

	makeDataViewThumbImageTitletextValue: function( values ) {
		return values.prefixedText;
	},

	makeDataViewThumbImageRevidValue: function( values ) {
		return 0;
	},

	makeDataViewThumbnailImageTitle: function( values ) {
		return values.displayText;
	},

	makeDataViewThumbnailImageAlt: function( values ) {
		return values.displayText;
	},

	makeDataViewThumbnailCaptionTitle: function( values ) {
		return values.displayText;
	},

	dataViewItemHasMenu: function( values ) {
		return false;
	},

	makeDataViewItemMetaItems: function( values ) {
		return [];
	},

	makeDataViewAddIcon: function() {
		if( this.showAddIcon() ) {
			return this.makeAddIconHTML();
		}
		return '';
	},

	showAddIcon: function() {
		return true;
	},

	makeAddIconHTML: function() {
		var html =
'<div class="thumbnail thumb-add">' +
'	<div class="caption first">' +
'		<a href="' + this.makeAddIconHref() + '" title="' + this.makeAddIconTooltip() + '">' +
'			<span>' + this.makeAddIconLabel() + '</span>' +
'		</a>' +
'	</div>' +
'</div>';

		return html;

	},

	makeAddIconHref: function() {
		return '#';
	},

	makeAddIconTooltip: function() {
		return mw.message( 'bs-extjs-flyout-add-title' ).text();
	},

	makeAddIconLabel: function() {
		return mw.message( 'bs-extjs-flyout-add-label' ).text();
	},

	onThumbAddClicked: function() {
		//Should be implemented by subclass
	},

	makeGridPanel: function() {
		var dockedItems = [];
		dockedItems.push( new Ext.toolbar.Paging( {
			pageSize: this.pageSize,
			store: this.commonStore,
			dock: 'bottom',
			displayInfo : true
		} ) );

		if( this.showAddIcon() ) {
			this.btnAdd = new Ext.Button({
				id: this.getId()+'-btn-add',
				iconCls: 'btn32',
				tooltip: mw.message( 'bs-extjs-add' ).plain(),
				height: 50,
				width: 52
			});
			this.btnAdd.on( 'click', function() {
				this.onThumbAddClicked();
			}, this );

			dockedItems.push( new Ext.Toolbar({
				dock: 'top',
				items: [
					this.btnAdd
				]
			} ) );
		}

		return new Ext.grid.Panel( {
			id: 'gridviewpanel',
			iconCls: 'icon-grid',
			cls: 'list',
			title: mw.message( 'bs-extjs-flyout-tab-grid-label' ).text(),
			tooltip: mw.message( 'bs-extjs-flyout-tab-grid-title' ).text(),
			store: this.commonStore,
			columns: this.makeGridPanelColums(),
			dockedItems: dockedItems,
			plugins: this.makeGridPlugins()
		} );
	},

	makeGridPlugins: function() {
		return [
			'gridfilters'
		];
	},

	makeGridPanelColums: function() {
		return [{
			text: 'Title',
			dataIndex: 'prefixed_text',
			flex: 1,
			filter: {
				type: 'string'
			},
			renderer: function( value, metadata, record ) {
				return record.get( 'page_link' );
			}
		}];
	},

	showItemMenu: function( record, toolsTriggerEl ) {
		var toolsMenu = this.makeTooleMenu( record );
		if( toolsMenu ) {
			var offset = $(toolsTriggerEl).offset();
			toolsMenu.showAt( offset.left, offset.top );
		}
	},

	/**
	 * Must be implemented by a subclass if a menu should be available
	 * E.g.
	 * return new Ext.menu.Menu( {
	 *		items: [{
	 *			text: 'regular item 1'
	 *		},{
	 *			text: 'regular item 2'
	 *		},{
	 *			text: 'regular item 3'
	 *		}]
	 *	} );
	 * @param {object} record
	 * @returns {Ext.menu.Menu}
	 */
	makeTooleMenu: function( record ) {
		return null;
	},

	/**
	 * This is just dummy data! Override in subclass!
	 * @returns Ext.data.Store
	 */
	makeCommonStore: function() {
		return new BS.store.BSApi( {
			model: 'BS.model.Title',
			apiAction: this.commonStoreApiAction
		} );
	},

	getInitialActiveTab: function() {
		if( !this.defaultTab || this.defaultTab === '' ) {
			return null;
		}
		return this.defaultTab;
	}
});