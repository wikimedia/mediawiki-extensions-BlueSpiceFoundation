/**
 * Window
 *
 * Part of BlueSpice MediaWiki
 *
 * @author     Robert Vogel <vogel@hallowelt.com>
 * @author     Stephan Muggli <muggli@hallowelt.com>
 * @package    Bluespice_Extensions
 * @subpackage Foundation
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */

Ext.define( 'BS.Window', {
	extend: 'Ext.Window',
	requires: [ 'Ext.Button', 'Ext.form.Label', 'Ext.toolbar.Toolbar' ],
	/*
		mixins: {
		mediaWiki: 'BS.mixins.MediaWiki'
	},*/
	width: 450,
	minHeight: 120,
	closeAction: 'hide',
	layout: 'form',
	title: '',

	bodyPadding:5,

	constructor: function(config) {
		var cfg = config || {};
		this.fieldDefaults = cfg.fieldDefaults || {
			labelAlign: 'right'
		};

		//Custom Settings
		this.currentData = {};
		this.callParent(arguments);
	},

	initComponent: function() {
		this.items = this.makeItems();
		this.dockedItems = this.makeDockedItems();

		this.afterInitComponent( arguments );

		this.callParent( arguments );
	},
	afterInitComponent: function() {

	},
	show: function () {
		this.setLoading( false );
		this.callParent( arguments );

		// (re)set position
		if( !this.original_x ){
			this.original_x = this.getX();
		}
		if( !this.original_y ){
			this.original_y = this.getY();
		}
		this.setPosition( this.original_x, this.original_y, false );
	},
	onBtnOKClick: function () {
		this.setLoading( true );
		if ( this.fireEvent( 'ok', this, this.getData() ) ) {
			this.close();
		}
	},
	onBtnCancelClick: function() {
		this.resetData();
		this.fireEvent( 'cancel', this );
		this.close();
	},
	showLoadMask: function() {
		this.getEl().mask(
			mw.message('bs-extjs-loading').plain()/*,
			'x-mask-loading'*/
		);
	},
	hideLoadMask: function() {
		this.getEl().unmask();
	},
	//TODO: Examine Ext.Class config mechanism
	//HINT: http://docs.sencha.com/extjs/4.2.1/#!/api/Ext.Class-cfg-config
	getData: function(){
		return this.currentData;
	},
	setData: function( obj ){
		this.currentData = obj;
	},
	resetData: function() {
	},
	setTitle: function( title ){
		this.title = title;
		this.callParent( arguments );
	},
	makeId: function( part ) {
		return this.getId() + '-' + part;
	},

	/*
	statics: {
		instances: {},
		getInstance: function( key ) {

		}
	}*/

	makeItems: function() {
		return [];
	},

	makeButtons: function() {
		this.btnOK = Ext.create( 'Ext.Button', {
			text: mw.message('bs-extjs-ok').plain(),
			id: this.getId()+'-btn-ok',
			cls: 'bs-extjs-btn-ok'
		});
		this.btnOK.on( 'click', this.onBtnOKClick, this );

		this.btnCancel = Ext.create( 'Ext.Button', {
			text: mw.message('bs-extjs-cancel').plain(),
			id: this.getId()+'-btn-cancel',
			cls: 'bs-extjs-btn-cancel'
		});
		this.btnCancel.on( 'click', this.onBtnCancelClick, this );

		return [
			'->',
			this.btnOK,
			this.btnCancel
		];
	},

	makeDockedItems: function() {
		return [
			new Ext.toolbar.Toolbar({
				dock: 'bottom',
				ui: 'footer',
				defaults: {
					minWidth: this.minButtonWidth
				},
				items: this.makeButtons()
			})
		];
	}
});
