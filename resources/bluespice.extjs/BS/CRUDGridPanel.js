/**
 * CRUDGridPanel
 *
 * Part of BlueSpice for MediaWiki
 *
 * @author     Robert Vogel <vogel@hallowelt.biz>
 * @author     Stephan Muggli <muggli@hallowelt.biz>
 * @package    Bluespice_Extensions
 * @subpackage Foundation
 * @copyright  Copyright (C) 2013 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

Ext.define( 'BS.CRUDGridPanel', {
	//Custom
	extend: 'BS.CRUDPanel',
	pageSize: 20,
	smMain: null,
	bbMain: null,
	colMain: null,
	gpMainConf: {},
	colMainConf: {
		columns: [],
		actions: [] //Custom; Used for ActionColumn
	},

	afterInitComponent: function( arguments ) {

		this.smMain = this.smMain || Ext.create( 'Ext.selection.RowModel', {
			mode: "SINGLE"
		});
				
		this.colMainConf.actions.unshift({
			icon: mw.config.get( 'wgScriptPath') + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-m_delete_tn.png',
			iconCls: 'bs-extjs-actioncloumn-icon',
			tooltip: mw.message('bs-extjs-action-remove-tooltip').plain(),
			handler: this.onActionRemoveClick,
			scope: this
		});

		this.colMainConf.actions.unshift({
			icon: mw.config.get( 'wgScriptPath') + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-um_config_tn.png',
			iconCls: 'bs-extjs-actioncloumn-icon',
			tooltip: mw.message('bs-extjs-action-edit-tooltip').plain(),
			handler: this.onActionEditClick,
			scope: this
		});
		
		this.colActions = Ext.create( 'Ext.grid.column.Action', {
			header: mw.message('bs-extjs-actions-column-header').plain(),
			flex: 0,
			width: 120,
			//cls: 'hideAction',
			items: this.colMainConf.actions,
			menuDisabled: true,
			sortable: false
		});

		this.colMainConf.columns.push( this.colActions );
/*
		this.tfPageSize = Ext.create( 'Ext.form.TextField', {
			width: 30,
			style: 'text-align: right',
			value: this.pageSize,
			enableKeyEvents: true
		});
		this.tfPageSize.on( 'keydown', this.onTfPageSizeKeyDown, this );
*/
		this.bbMain = this.bbMain || Ext.create( 'Ext.PagingToolbar', {
			store : this.strMain,
			displayInfo : true//,
			//displayMsg    : mw.message('bs-extjs-displayMsg').plain(),
			//emptyMsg      : mw.message('bs-extjs-emptyMsg').plain(),
			//beforePageText: mw.message('bs-extjs-beforePageText').plain(),
			//afterPageText : mw.message('bs-extjs-afterPageText').plain(),
			/*items: [
				'-',
				mw.message('bs-extjs-pageSize').plain()+':',
				this.tfPageSize
			]*/
		});

		//TODO: Fix Pagesize
		this.smMain.pageSize = this.pageSize;

		var gridDefaultConf = {
			cls: 'bs-extjs-crud-grid',
			//Simple
			border: false,
			enableHdMenu: false,
			//hideHeaders: true,
			loadMask: true,
			autoHeight: true,
			//region: 'center',
			stripeRows: true,
			autoExpandColumn: 'title', //ignored if viewConfig.forceFit == true
			//Complex
			viewConfig: {
				//forceFit: true,
				scrollOffset: 1
			},
			store: this.strMain,
			columns: { 
				items: this.colMainConf.columns,
				defaults: {
					flex: 1
				}
			},
			sm:       this.smMain,
			bbar:     this.bbMain
		};

		var gridConf = Ext.applyIf( this.gpMainConf, gridDefaultConf );

		this.grdMain = Ext.create( 'Ext.grid.GridPanel', gridConf );
		this.grdMain.on( 'select', this.onGrdMainRowClick, this );
		this.strMain.on( 'load', this.onStrMainLoadBase, this );

		this.items = [
			this.grdMain
		];

		this.callParent(arguments);
	},

	getSingleSelection: function() {
		var selectedRecords = this.grdMain.getSelectionModel().getSelection();
		if( selectedRecords.length > 0) {
			return selectedRecords[0];
		}
		return null;
	},

	onTfPageSizeKeyDown: function( oSender, oEvent ) {
		//HINT: http://ssenblog.blogspot.de/2009/12/extjs-grid-dynamic-page-size.html
		if( oEvent.getKey() !== 13) return;

		this.bbMain.cursor = 0;
		this.smMain.pageSize = parseInt(oSender.getValue()); //TODO: Fix me
		this.strMain.load({
			params: {
				start: 0,
				limit: parseInt( oSender.getValue() )
			}
		});
	},

	onGrdMainRowClick: function( oSender, iRowIndex, oEvent ) {
		this.btnEdit.enable();
		this.btnRemove.enable();

		var selectedRecords = this.grdMain.getSelectionModel().getSelection();
		if( selectedRecords.length > 1 ) {
			this.btnEdit.disable();
		}
	},
	onStrMainLoadBase: function() {
		// All selections have to be deselected otherwise double editing of the same row won't work
		this.grdMain.getSelectionModel().deselectAll();
	},

	onActionEditClick:function(grid, rowIndex, colIndex) {
		this.grdMain.getSelectionModel().select(
			this.grdMain.getStore().getAt( rowIndex )
		);
		this.onBtnEditClick( this.btnEdit, {} );
	},

	onActionRemoveClick:function(grid, rowIndex, colIndex) {
		this.grdMain.getSelectionModel().select(
			this.grdMain.getStore().getAt( rowIndex )
		);
		this.onBtnRemoveClick( this.btnRemove, {} );
	},

	onBtnAddClick: function( oButton, oEvent ) {
		this.callParent(arguments);
	},

	onBtnEditClick: function(  oButton, oEvent  ) {
		this.callParent(arguments);
	},

	onBtnRemoveClick: function( oButton, oEvent ) {
		this.callParent(arguments);
	}
});