/**
 * @class BS.portal.PortalColumn
 * @extends Ext.container.Container
 * A layout column class used internally be {@link BS.portal.PortalPanel}.
 */
Ext.define('BS.portal.PortalColumn', {
	extend: 'Ext.container.Container',
	alias: 'widget.portalcolumn',

	requires: [
		'Ext.layout.container.Anchor',
		'BS.portal.Portlet'
	],

	layout: 'anchor',
	defaultType: 'portlet',
	cls: 'x-portal-column'

	// This is a class so that it could be easily extended
	// if necessary to provide additional behavior.
});