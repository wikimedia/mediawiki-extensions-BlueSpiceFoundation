/**
 * @class BS.portal.Portlet
 * @extends Ext.panel.Panel
 * A {@link Ext.panel.Panel Panel} class that is managed by {@link BS.portal.PortalPanel}.
 */
Ext.define('BS.portal.Portlet', {
	extend: 'Ext.panel.Panel',
	alias: 'widget.portlet',
	layout: 'fit',
	anchor: '100%',
	frame: true,
	closable: true,
	collapsible: true,
	animCollapse: true,
	draggable: {
		moveOnDrag: false
	},
	cls: 'x-portlet',
	title: '',

	//Custom Settings
	portletConfigClass : 'BS.portal.PortletConfig',
	portletItemCount: 10,
	portletTimeSpan: 0,

	initComponent: function() {
		this.tlSettings = Ext.create('Ext.panel.Tool', {
			type: 'gear'
		});
		this.tlSettings.on( 'click', this.onTlSettingsClick, this );

		this.pcConfig = false; //The config component is instantiated ony if needed within the config tool callback

		this.tools = this.tools || [];
		this.tools.push(this.tlSettings);

		this.afterInitComponent(arguments);
		this.callParent(arguments);
	},

	//Allow subclasses to modify child component configs
	beforeInitComponent: function() {
	},
	//Allow subclasses to change tools or contents
	afterInitComponent: function() {
	},

	onTlSettingsClick: function( tool, event, eventOpts ) {
		if( !this.pcConfig ) {
			this.pcConfig = Ext.create(this.portletConfigClass, {
				portletToConfig: this
			});
		}
		this.pcConfig.show();
	},
	//Subclasses may add further config data to feed their child components
	getPortletConfig: function() {
		//There is no method like Panel::getTitle()!
		return {
			title: this.title,
			height: this.height || 0,
			portletItemCount: this.portletItemCount,
			portletTimeSpan: this.portletTimeSpan,
			collapsed: this.getCollapsed()
		};
	},
	//Subclasses can use this to refresh their content!
	setPortletConfig: function( oConfig ) {
		this.setTitle( oConfig.title );
		this.setHeight( oConfig.height );
		this.portletItemCount = oConfig.portletItemCount;
		this.portletTimeSpan = oConfig.portletTimeSpan;
		this.fireEvent( 'configchange', this, oConfig );
	},
	// Override Panel's default doClose to provide a custom fade out effect
	// when a portlet is removed from the portal
	doClose: function() {
		if (!this.closing) {
			this.closing = true;
			this.el.animate({
				opacity: 0,
				callback: function(){
					var closeAction = this.closeAction;
					this.closing = false;
					this.fireEvent('close', this);
					this[closeAction]();
					if (closeAction === 'hide') {
						this.el.setOpacity(1);
					}
				},
				scope: this
			});
		}
	}
});