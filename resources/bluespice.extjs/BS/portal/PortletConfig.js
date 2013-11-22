Ext.define('BS.portal.PortletConfig', {
	extend: 'BS.Window',
	modal: true,
	defaults: {
		labelAlign: 'right'
	},
	

	//Custom Settings
	portletToConfig: null,
	showItemCount: false,
	showTimeSpan: false,

	afterInitComponent: function() {
		this.tfTitle = Ext.create( 'Ext.form.TextField',{
			fieldLabel: mw.message( 'bs-extjs-portal-title' ).plain(),
			labelAlign: 'right'
		});
		this.sfHeight = Ext.create( 'Ext.form.field.Number',{
			fieldLabel: mw.message( 'bs-extjs-portal-height' ).plain(),
			labelAlign: 'right',
			step: 10
		});
		this.sfCount = Ext.create( 'Ext.form.field.Number',{
			fieldLabel: mw.message( 'bs-extjs-portal-count' ).plain(),
			labelAlign: 'right',
			step: 10
		});
		this.strTime = Ext.create( 'Ext.data.Store', {
			fields: [ 'time', 'label' ],
			data: [
				{ 'time': 7, 'label': mw.message( 'bs-extjs-portal-timespan-week' ).plain() },
				{ 'time': 30, 'label': mw.message( 'bs-extjs-portal-timespan-month' ).plain() },
				{ 'time': 0, 'label': mw.message( 'bs-extjs-portal-timespan-alltime' ).plain() }
			]
		});
		this.cbTimeSpan = Ext.create( 'Ext.form.ComboBox',{
			fieldLabel: mw.message( 'bs-extjs-portal-timespan' ).plain(),
			labelAlign: 'right',
			store: this.strTime,
			queryMode: 'local',
			displayField: 'label',
			valueField: 'time'
		});

		this.items = this.items || [];
		if ( this.showItemCount ) {
			this.items.unshift( this.sfCount );
		}
		if ( this.showTimeSpan ) {
			this.items.unshift( this.cbTimeSpan );
		}

		this.items.unshift( this.sfHeight );
		this.items.unshift( this.tfTitle );
	},

	onBtnOKClick: function() {
		this.portletToConfig.setPortletConfig(
			this.getConfigControlValues()
		);
		this.callParent( arguments );
	},

	show: function() {
		this.callParent(arguments);

		this.setConfigControlValues(
			this.portletToConfig.getPortletConfig()
		);
	},

	//Can be overriden by subclasses to allow additional config data
	setConfigControlValues: function( cfg ) {
		this.setTitle( mw.message('bs-extjs-portal-config').plain()+' '+cfg.title );
		this.tfTitle.setValue( cfg.title );
		this.sfHeight.setValue( cfg.height );
		this.sfCount.setValue( cfg.portletItemCount );
		this.cbTimeSpan.setValue( cfg.portletTimeSpan );
	},

	//Can be overriden by subclasses to allow additional config data
	getConfigControlValues: function() {
		return {
			title: this.tfTitle.getValue(),
			height: this.sfHeight.getValue(),
			portletItemCount: this.sfCount.getValue(),
			portletTimeSpan: this.cbTimeSpan.getValue()
		};
	}
});