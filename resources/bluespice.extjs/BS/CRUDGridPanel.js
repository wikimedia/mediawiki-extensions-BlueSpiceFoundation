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
	extend: 'BS.CRUDPanel',
	requires: [ 'Ext.PagingToolbar', 'Ext.selection.RowModel',
		'Ext.grid.GridPanel', 'Ext.grid.column.Action',
		'Ext.ux.grid.FiltersFeature'
	],

	//Custom
	pageSize: 20,

	constructor: function() {
		this.colMainConf = {
			columns: [],
			actions: [] //Custom; Used for ActionColumn
		};

		this.smMain = null;
		this.bbMain = null;
		this.colMain = null;
		this.gpMainConf = {};

		this.callParent(arguments);
	},

	makeItems: function() {
		return [
			this.makeMainGrid()
		];
	},

	makeMainStore: function() {
		this.strMain.on( 'load', this.onStrMainLoadBase, this );
		return this.strMain;
	},

	makeBBar: function() {
		this.bbMain = this.bbMain || new Ext.PagingToolbar({
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

		return this.bbMain;
	},

	makeMainGrid: function() {

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
			store: this.makeMainStore(),
			columns: this.makeGridColumns(),
			selModel: this.makeSelModel(),
			features: this.makeFeatures(),
			bbar: this.makeBBar()
		};

		var gridConf = Ext.applyIf( this.gpMainConf, gridDefaultConf );
		this.grdMain = new Ext.grid.GridPanel( gridConf );
		this.grdMain.on( 'select', this.onGrdMainRowClick, this );

		return this.grdMain;
	},

	makeGridColumns: function(){
		this.colActions = this.makeActionColumn( this.colMainConf.columns );

		return {
			items: this.colMainConf.columns,
			defaults: {
				flex: 1
			}
		};
	},

	makeActionColumn: function( cols ) {
		var actionColumn = new Ext.grid.column.Action({
			header: mw.message('bs-extjs-actions-column-header').plain(),
			flex: 0,
			//width: 120,
			//cls: 'hideAction',
			items: this.makeRowActions(),
			menuDisabled: true,
			hideable: false,
			sortable: false
		});

		cols.push( actionColumn );
		return actionColumn;
	},

	makeRowActions: function() {
		this.colMainConf.actions.unshift({
			icon: mw.config.get( 'wgScriptPath') + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-m_delete_tn.png',
			iconCls: 'bs-extjs-actioncolumn-icon',
			tooltip: mw.message('bs-extjs-delete').plain(),
			handler: this.onActionRemoveClick,
			scope: this
		});

		this.colMainConf.actions.unshift({
			icon: mw.config.get( 'wgScriptPath') + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-um_config_tn.png',
			iconCls: 'bs-extjs-actioncolumn-icon',
			tooltip: mw.message('bs-extjs-edit').plain(),
			handler: this.onActionEditClick,
			scope: this
		});

		return this.colMainConf.actions;
	},

	makeSelModel: function(){
		this.smMain = this.smMain || new Ext.selection.RowModel({
			mode: "SINGLE"
		});
		//TODO: Fix Pagesize
		this.smMain.pageSize = this.pageSize;
		return this.smMain;
	},

	makeFeatures: function() {
		return [
			new Ext.ux.grid.FiltersFeature({
				encode: true
			})
		];
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