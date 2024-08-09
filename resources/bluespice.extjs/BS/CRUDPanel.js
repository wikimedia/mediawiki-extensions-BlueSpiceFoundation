/**
 * CRUDPanel
 *
 * Part of BlueSpice MediaWiki
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @author     Stephan Muggli <muggli@hallowelt.com>
 * @package    Bluespice_Extensions
 * @subpackage Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */

Ext.define( 'BS.CRUDPanel', {
	extend: 'Ext.Panel',
	border: false,
	hideBorder: true,

	cls: 'bs-crud-panel',

	operationPermissions: {
		create: true, // should be connected to mw.config.get('bsgTaskAPIPermissions').extension_xyz.task1 = boolean in derived class
		update: true, // ...
		delete: true // ...
	},

	tbarHeight: 32,

	constructor: function ( config ) {
		config = config || {};
		// Custom Settings
		this.currentData = {};
		if ( config.renderTo && Ext.isString( config.renderTo ) ) {
			config.id = config.id || config.renderTo + '-crud';
		}
		this.callParent( arguments );
	},

	initComponent: function () {
		this.tbar = this.makeTbar();
		this.items = this.makeItems();

		$( document ).trigger( 'BSCRUDPanelInitComponent', [ this ] );

		this.afterInitComponent( arguments );

		this.callParent( arguments );
	},

	makeItems: function () {
		return [];
	},

	makeTbar: function () {
		return new Ext.Toolbar( {
			cls: 'bs-crud-panel-toolbar',
			items: this.makeTbarItems()
		} );
	},

	makeTbarItems: function () {
		var arrItems = [];
		if ( this.opPermitted( 'create' ) ) {
			this.btnAdd = new Ext.Button( {
				id: this.getId() + '-btn-add',
				cls: 'bs-extjs-btn',
				icon: mw.config.get( 'wgScriptPath' ) + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-btn_add.png',
				iconCls: 'btn' + this.tbarHeight,
				tooltip: mw.message( 'bs-extjs-add' ).plain(),
				ariaLabel: mw.message('bs-extjs-add').plain(),
				height: 50,
				width: 52
			} );
			this.setBtnAria( this.btnAdd );
			this.btnAdd.on( 'click', this.onBtnAddClick, this );
			arrItems.push( this.btnAdd );
		}

		if ( this.opPermitted( 'update' ) ) {
			this.btnEdit = new Ext.Button( {
				id: this.getId() + '-btn-edit',
				cls: 'bs-extjs-btn',
				icon: mw.config.get( 'wgScriptPath' ) + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-btn_config.png',
				iconCls: 'btn' + this.tbarHeight,
				tooltip: mw.message( 'bs-extjs-edit' ).plain(),
				ariaLabel: mw.message('bs-extjs-edit').plain(),
				height: 50,
				width: 52,
				disabled: true
			} );
			this.setBtnAria( this.btnEdit );
			this.btnEdit.on( 'click', this.onBtnEditClick, this );
			arrItems.push( this.btnEdit );
		}

		if ( this.opPermitted( 'delete' ) ) {
			this.btnRemove = new Ext.Button( {
				id: this.getId() + '-btn-remove',
				cls: 'bs-extjs-btn',
				icon: mw.config.get( 'wgScriptPath' ) + '/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-btn_delete.png',
				iconCls: 'btn' + this.tbarHeight,
				tooltip: mw.message( 'bs-extjs-remove' ).plain(),
				ariaLabel: mw.message('bs-extjs-remove').plain(),
				height: 50,
				width: 52,
				disabled: true
			} );
			this.setBtnAria( this.btnRemove );
			this.btnRemove.on( 'click', this.onBtnRemoveClick, this );
			arrItems.push( this.btnRemove );
		}

		return arrItems;
	},

	setBtnAria: function ( btn ) {
		btn.on( 'afterrender', function() {
			this.el.dom.setAttribute( 'tabindex', 0 );
			this.el.dom.setAttribute( 'role', 'button' );
		}.bind( btn ) );
	},

	afterInitComponent: function ( arguments ) {
		// This get's overridden by subclasses to add subcomponents
	},

	onBtnAddClick: function ( oButton, oEvent ) {
		this.fireEvent( 'button-add', this, this.getData() );
	},

	onBtnEditClick: function ( oButton, oEvent ) {
		this.fireEvent( 'button-edit', this, this.getData() );
	},

	onBtnRemoveClick: function ( oButton, oEvent ) {
		this.fireEvent( 'button-delete', this, this.getData() );
	},

	getData: function () {
		return this.currentData;
	},

	setData: function ( obj ) {
		this.currentData = obj;
	},

	/**
	 * @param operation
	 * @return boolean if param exists, otherwise null
	 */
	opPermitted: function ( operation ) {
		if ( operation in this.operationPermissions ) {
			return this.operationPermissions[ operation ];
		} else {
			return null;
		}
	},

	makeId: function ( part ) {
		return this.getId() + '-' + part;
	}
} );
