Ext.define('BS.portal.ChartPortlet', {

	extend: 'BS.portal.Portlet',

	requires: [
		'Ext.data.JsonStore',
		'Ext.chart.theme.Base',
		'Ext.chart.series.Series',
		'Ext.chart.series.Line',
		'Ext.chart.axis.Numeric'
	],
	
	layout: 'fit',
	height: 300,

	initComponent: function(){
		this.beforeInitCompontent();
		this.ctMainConfig = this.ctMainConfig || {
			store: Ext.create('Ext.data.JsonStore', {
				fields: ['name', 'sp500', 'djia'],
				data: (function(){
					var data = [{ name: 0, djia: 10000, sp500: 1100 }], i;
					for (i = 1; i < 50; i++) {
						data.push({
							name: i,
							sp500: data[i - 1].sp500 + ((Math.floor(Math.random() * 2) % 2) ? -1 : 1) * Math.floor(Math.random() * 7),
							djia: data[i - 1].djia + ((Math.floor(Math.random() * 2) % 2) ? -1 : 1) * Math.floor(Math.random() * 7)
						});
					}
					return data;
				})()
			}),
			axes: [{
				type: 'Numeric',
				position: 'left',
				fields: ['djia'],
				title: 'Dow Jones Average',
				label: {
					font: '11px Arial'
				}
			}, {
				type: 'Numeric',
				position: 'right',
				grid: false,
				fields: ['sp500'],
				title: 'S&P 500',
				label: {
						font: '11px Arial'
					}
			}],
			series: [{
				type: 'line',
				lineWidth: 1,
				showMarkers: false,
				fill: true,
				axis: 'left',
				xField: 'name',
				yField: 'djia',
				style: {
					'stroke-width': 1,
					stroke: 'rgb(148, 174, 10)'

				}
			}, {
				type: 'line',
				lineWidth: 1,
				showMarkers: false,
				axis: 'right',
				xField: 'name',
				yField: 'sp500',
				style: {
					'stroke-width': 1,
					 stroke: 'rgb(17, 95, 166)'

				}
			}]
		};
		
		this.ctMain = Ext.create('Ext.chart.Chart', {
			animate: false,
			shadow: false,
			legend: {
				position: 'bottom'
			},
			store: this.ctMainConfig.store,
			axes: this.ctMainConfig.axes,
			series: this.ctMainConfig.series
		});
		
		this.items = [
			this.ctMain
		];

		this.callParent(arguments);
	}
});
