Ext.define( 'BS.flyout.TwoColumnsBase', {
	extend: 'Ext.panel.Panel',
	mixin: [
		'Ext.mixin.Responsive'
	],
	plugins: 'responsive',
	responsiveConfig: {
		'width < 1024': {
			layout: {
				type: 'vbox',
				vertical: true,
				align: 'stretch'
			}
		},
		'width >= 1024': {
			layout: 'border',
			height: 600
		}
	},
	cls: 'bs-flyout-two-columns',

	initComponent: function() {
		this.items = [];

		this.addTopPanel();
		this.addCenterPanels();
		this.addBottomPanel();

		return this.callParent( arguments );
	},

	addTopPanel: function() {
		var items = this.makeTopPanelItems();
		if( items.length > 0 ) {
			this.items.push( this.makeTopPanel( items ) );
		}
	},

	makeTopPanel: function( items ) {
		return {
			region: 'north',
			cls: 'panel-top',
			items: items
		};
	},

	addBottomPanel: function() {
		var items = this.makeBottomPanelItems();
		if( items.length > 0 ) {
			this.items.push( this.makeBottomPanel( items ) );
		}
	},

	makeBottomPanel: function( items ) {
		return {
			region: 'south',
			cls: 'panel-bottom',
			items: items
		};
	},

	addCenterPanels: function() {
		var centerOneItems = this.makeCenterOneItems();
		var centerTwoItems = this.makeCenterTwoItems();

		if( centerOneItems.length > 0 ) {
			this.items.push( this.makeCenterOnePanel( centerOneItems ) );
		}

		if( centerTwoItems.length > 0 ) {
			this.items.push( this.makeCenterTwoPanel( centerTwoItems ) );
		}
	},

	makeCenterOnePanel: function( items ) {
		return {
			region: 'west',
			width: '40%',
			bodyPadding: 5,
			cls: 'panel-center-one',
			items: items
		};
	},

	makeCenterTwoPanel: function( items ) {
		return {
			region: 'center',
			bodyPadding: 5,
			cls: 'panel-center-two',
			items: items
		};
	},

	makeTopPanelItems: function() {
		return [];
	},

	makeBottomPanelItems: function() {
		return [];
	},

	/**
	 * HINT: It's intentional "CenterOne" and "CenterTwo", instead of "Left"
	 * and "Right". This is to maybe add RTL support in future.
	 *
	 * Should be overwritten by subclass
	 * @returns {Array}
	 */
	makeCenterOneItems: function() {
		return [];
	/* DEMO
		return [ {
			title: 'Some form',
			xtype: 'form',
			bodyPadding: 10,
			fieldDefaults: {
				labelAlign: 'right',
			},
			items: [ {
				fieldLabel: 'From',
				xtype: 'datefield',
				value: new Date()
			}, {
				fieldLabel: 'To',
				xtype: 'datefield',
				value: new Date( (new Date()).getTime() + 7 * 24 * 60 * 60 * 1000)
			}, {
					xtype: 'checkboxgroup',
					fieldLabel: 'Some checkbox button group',
					labelAlign: 'top',
					vertical: true,
					columns: 1,
					items: [
						{ boxLabel: 'Lorem ipsum dolor ist amet', name: 'rb', inputValue: '1', checked: true },
						{ boxLabel: 'Consectetuer adipiscing elit, aenean commodo ligula eget dolor.', name: 'rb', inputValue: '2' },
						{ boxLabel: 'Cum sociis natoque penatibus et magnis dis parturient montes', name: 'rb', inputValue: '3', checked: true }
					]
			}],
			buttonAlign: 'center',
			buttons: [ {
				text: 'Save'
			}, {
				text: 'Export'
			}, {
				text: 'Delete'
			}, {
				text: 'Cancel'
			} ]
		} ];
	*/
	},

	/**
	 * Should be overwritten by subclass
	 * @returns {Array}
	 */
	makeCenterTwoItems: function() {
		return [];
	/* DEMO
		return [ {
			title: 'Some other form',
			xtype: 'form',
			bodyPadding: 10,
			items: [ {
				xtype: 'fieldcontainer',
				layout: 'hbox',
				items: [{
					xtype: 'textfield',
					emptyText: 'Enter something',
					flex: 3,
					padding: '0 5 0 0'
				}, {
					xtype: 'button',
					flex: 1,
					iconCls: 'bs-icon-upload',
				}, {
					xtype: 'combobox',
					flex: 3,
					padding: '0 0 0 5',
					queryMode: 'local',
					displayField: 'name',
					valueField: 'abbr',
					emptyText: 'Select something',
					store: {
						fields: ['abbr', 'name'],
						data : [
							{"abbr":"AL", "name":"Alabama"},
							{"abbr":"AK", "name":"Alaska"},
							{"abbr":"AZ", "name":"Arizona"}
						]
					}
				}]
			}, {
				xtype: 'checkboxfield',
				boxLabel: 'Public',
				inputValue: '1'
			}, {
				xtype: 'combobox',
				flex: 1,
				queryMode: 'local',
				displayField: 'name',
				valueField: 'abbr',
				emptyText: 'Select something',
				store: {
					fields: ['abbr', 'name'],
					data : [
						{"abbr":"AL", "name":"Alabama"},
						{"abbr":"AK", "name":"Alaska"},
						{"abbr":"AZ", "name":"Arizona"}
					]
				}
			}]
		}, {
			title: 'Some grid',
			xtype: 'gridpanel',
			bodyPadding: 10,
			columns: [
				{ text: 'Name', dataIndex: 'name' },
				{ text: 'Email', dataIndex: 'email', flex: 1 },
				{ text: 'Phone', dataIndex: 'phone' }
			],
			store: {
				fields:[ 'name', 'email', 'phone'],
				data: [
					{ name: 'Lisa', email: 'lisa@simpsons.com', phone: '555-111-1224' },
					{ name: 'Bart', email: 'bart@simpsons.com', phone: '555-222-1234' },
					{ name: 'Homer', email: 'homer@simpsons.com', phone: '555-222-1244' },
					{ name: 'Marge', email: 'marge@simpsons.com', phone: '555-222-1254' }
				]
			}
		} ];
	*/
	}
});
