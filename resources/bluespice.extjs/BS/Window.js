/**
 * Window
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

Ext.define( 'BS.Window', {
	extend: 'Ext.Window',
	requires: [
		'Ext.Button',
		'Ext.form.Label'
	],
	/*
		mixins: {
		mediaWiki: 'BS.mixins.MediaWiki'
	},*/
	width: 450,
	minHeight: 120,
	closeAction: 'hide',
	layout: 'form',
	title: '',
	fieldDefaults: {
		labelAlign: 'right'
	},
	bodyPadding:5,

	//Custom Setting
	currentData: {},

	initComponent: function() {
		this.btnOK = Ext.create( 'Ext.Button', {
			text: mw.message('bs-extjs-ok').plain(),
			id: this.getId()+'-btn-ok'
		});
		this.btnOK.on( 'click', this.onBtnOKClick, this );

		this.btnCancel = Ext.create( 'Ext.Button', {
			text: mw.message('bs-extjs-cancel').plain(),
			id: this.getId()+'-btn-cancel'
		});
		this.btnCancel.on( 'click', this.onBtnCancelClick, this );

		this.items = [];

		this.buttons = [
			this.btnOK,
			this.btnCancel
		];

		this.addEvents( 'ok', 'cancel' );

		this.afterInitComponent( arguments );

		this.callParent( arguments );
	},
	afterInitComponent: function() {
		
	},
	onBtnOKClick: function() {
		this.fireEvent( 'ok', this, this.getData() );
		this.close();
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
	},
	makeId: function( part ) {
		return this.getId() + '-' + part;
	}
	/*,
	
	statics: {
		instances: {},
		getInstance: function( key ) {
			
		}
	}*/
});