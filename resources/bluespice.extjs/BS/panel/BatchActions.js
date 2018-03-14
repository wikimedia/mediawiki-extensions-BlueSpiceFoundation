Ext.define( 'BS.panel.BatchActions', {
	extend: 'Ext.Panel',

	initComponent: function() {
		this.pbActions = new Ext.ProgressBar({
			width: '100%'
		});

		this.gdActions = new Ext.grid.Panel({
			height: 200,
			viewConfig:{
				markDirty: false
			},
			columns: {
				items: [
					{
						text: mw.message('bs-deferred-batch-description').plain(),
						dataIndex: 'description',
						flex: 1
					},
					{
						text: mw.message('bs-deferred-batch-status').plain(),
						dataIndex: 'state',
						width: 120
					}
				]
			},
			store: new Ext.data.JsonStore({
				fields: ['description', 'state' ]
			})
		});

		this.fsActions = new Ext.form.FieldSet({
			title: mw.message('bs-deferred-batch-actions').plain(),
			collapsible:true,
			collapsed: true,
			items: [
				this.gdActions
			]
		});

		this.items = [
			{
				html: mw.message('bs-deferred-batch-progress-desc').plain()
			},
			this.pbActions,
			this.fsActions
		];


		this.callParent( arguments );
	},

	currrentActions: [],
	setData: function( data ) {
		this.currentActions = data;
		var storeData = [];
		for( var i = 0; i < data.length; i++ ) {

			storeData.push({
				description: data[i].getDescription(),
				state: data[i].getStatusText()
			});
		}

		this.gdActions.getStore().loadData( storeData );
		this.processComplete = false;
		this.pbActions.updateProgress( 0, '0%' );
	},

	startProcessing: function() {
		this.fireEvent( 'processtart', this );
		this.processAction( 0 );
	},

	processComplete: false,
	processAction: function( index ) {
		if( index >= this.currentActions.length ) { //End of chain
			this.processComplete = true;
			this.fireEvent( 'processcomplete', this );
			return;
		}
		if( !this.currentActions[index] ) {
			return;
		}
		var me = this;
		var promise = this.currentActions[index].execute();
		var record = me.gdActions.getStore().getAt( index );
		record.set( 'state', me.currentActions[index].getStatusText() );
		promise.done(function() {
			me.actionComplete( index, record );
		})
		.fail(function() {
			me.fsActions.expand();
			//TODO: Show prompt and hold process? Or error report after process?
			me.actionComplete( index, record );
		});
	},

	isProcessComplete: function() {
		return this.processComplete;
	},

	actionComplete: function( index, record ) {
		record.set( 'state', this.currentActions[index].getStatusText() );
		var progress = (index + 1) / this.currentActions.length;
		this.fireEvent( 'actioncomplete', this, progress );
		this.pbActions.updateProgress(
			progress,
			Math.ceil(progress * 100) + '%'
		);
		this.processAction( index + 1 );
	}
});