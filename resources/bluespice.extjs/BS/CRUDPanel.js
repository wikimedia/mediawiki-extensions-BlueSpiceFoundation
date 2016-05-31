/**
 * CRUDPanel
 *
 * Part of BlueSpice for MediaWiki
 *
 * @author     Robert Vogel <vogel@hallowelt.biz>
 * @author     Stephan Muggli <muggli@hallowelt.biz>
 * @package    Bluespice_Extensions
 * @subpackage Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */
/*jshint -W024 */

Ext.define( 'BS.CRUDPanel', {
	extend: 'Ext.Panel',
	requires: [ 'Ext.Toolbar', 'Ext.Button' ],
	border: false,
	hideBorder: true,
	tbarHeight: 44,

	constructor: function() {
		//Custom Settings
		this.currentData = {};
		this.callParent(arguments);
	},

	initComponent: function() {
		this.tbar = this.makeTbar();
		this.items = this.makeItems();

		$(document).trigger('BSCRUDPanelInitComponent', [this] );

		this.afterInitComponent( arguments );

		this.callParent(arguments);
	},

	makeItems: function() {
		return [];
	},

	makeTbar: function() {
		return new Ext.Toolbar({
			style: {
				backgroundColor: '#FFFFFF',
				backgroundImage: 'none'
			},
			items: this.makeTbarItems()
		});
	},

	makeTbarItems: function() {
		this.btnAdd = new Ext.Button({
			id: this.getId()+'-btn-add',
			icon: mw.config.get( 'wgScriptPath') + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-m_add.png',
			iconCls: 'btn'+this.tbarHeight,
			tooltip: mw.message('bs-extjs-add').plain(),
			height: 50,
			width: 52
		});
		this.btnAdd.on( 'click', this.onBtnAddClick, this );

		this.btnEdit = new Ext.Button({
			id: this.getId()+'-btn-edit',
			icon: mw.config.get( 'wgScriptPath') + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-um_config.png',
			iconCls: 'btn'+this.tbarHeight,
			tooltip: mw.message('bs-extjs-edit').plain(),
			height: 50,
			width: 52,
			disabled: true
		});
		this.btnEdit.on( 'click', this.onBtnEditClick, this );

		this.btnRemove = new Ext.Button({
			id: this.getId()+'-btn-remove',
			icon: mw.config.get( 'wgScriptPath') + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-m_delete.png',
			iconCls: 'btn'+this.tbarHeight,
			tooltip: mw.message('bs-extjs-remove').plain(),
			height: 50,
			width: 52,
			disabled: true
		});
		this.btnRemove.on( 'click', this.onBtnRemoveClick, this );

		this.addEvents( 'button-add','button-edit','button-delete' );

		return [
			this.btnAdd,
			this.btnEdit,
			this.btnRemove
		];
	},

	afterInitComponent: function( arguments ) {
		//This get's overridden by subclasses to add subcomponents
	},

	onBtnAddClick: function( oButton, oEvent ) {
		this.fireEvent( 'button-add', this, this.getData() );
	},

	onBtnEditClick: function(  oButton, oEvent  ) {
		this.fireEvent( 'button-edit', this, this.getData() );
	},

	onBtnRemoveClick: function( oButton, oEvent ) {
		this.fireEvent( 'button-delete', this, this.getData() );
	},

	getData: function() {
		return this.currentData;
	},

	setData: function( obj ) {
		this.currentData = obj;
	}
});