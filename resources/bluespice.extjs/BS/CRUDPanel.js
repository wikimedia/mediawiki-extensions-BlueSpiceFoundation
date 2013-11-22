/**
 * CRUDPanel
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

Ext.define( 'BS.CRUDPanel', {
	extend: 'Ext.Panel',
	border: false,
	hideBorder: true,
	tbarHeight: 44,

	//Custom Settings
	currentData: {},

	initComponent: function() {
		this.btnAdd = Ext.create( 'Ext.Button', {
			id: this.getId()+'-btn-add',
			icon: mw.config.get( 'wgScriptPath') + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-m_add.png',
			iconCls: 'btn'+this.tbarHeight,
			tooltip: mw.message('bs-extjs-tooltip-add').plain(),
			height: 50,
			width: 52
		});
		this.btnAdd.on( 'click', this.onBtnAddClick, this );

		this.btnEdit = Ext.create( 'Ext.Button', {
			id: this.getId()+'-btn-edit',
			icon: mw.config.get( 'wgScriptPath') + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-um_config.png',
			iconCls: 'btn'+this.tbarHeight,
			tooltip: mw.message('bs-extjs-tooltip-edit').plain(),
			height: 50,
			width: 52,
			disabled: true
		});
		this.btnEdit.on( 'click', this.onBtnEditClick, this );

		this.btnRemove = Ext.create( 'Ext.Button', {
			id: this.getId()+'-btn-remove',
			icon: mw.config.get( 'wgScriptPath') + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-m_delete.png',
			iconCls: 'btn'+this.tbarHeight,
			tooltip: mw.message('bs-extjs-tooltip-remove').plain(),
			height: 50,
			width: 52,
			disabled: true
		});
		this.btnRemove.on( 'click', this.onBtnRemoveClick, this );

		this.tbar = Ext.create( 'Ext.Toolbar', {
			style: {
				backgroundColor: '#FFFFFF',
				backgroundImage: 'none'
			},
			items: this.makeTbarItems()
		});

		this.addEvents( 'button-add','button-edit','button-delete' );

		this.afterInitComponent( arguments );

		this.callParent(arguments);
	},
	
	makeTbarItems: function() {
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