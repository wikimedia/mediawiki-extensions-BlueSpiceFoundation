Ext.define( 'BS.action.Base', {
	extend: 'Ext.mixin.Observable',
	statics: {
		STATUS_ERROR: -1,
		STATUS_PENDING: 0,
		STATUS_RUNNING: 1,
		STATUS_DONE: 2
	},

	actionStatus: 0, // BS.action.Base.STATUS_PENDING

	/**
	 *
	 * @return jQuery.Promise
	 */
	execute: function () {

	},

	getDescription: function () {
		return Ext.getClassName( this );
	},

	getErrors: function () {
		var errors = [];
		if ( this.actionErrors ) {
			this.actionErrors.forEach( function ( msg ) {
				errors.push( { message: msg[ '*' ] } );
			} );
		}
		return errors;

	},

	getStatus: function () {
		return this.actionStatus;
	},

	getStatusText: function () {
		var status = this.getStatus(),
		 message = '';
		if ( status === BS.action.Base.STATUS_PENDING ) {
			message = mw.message( 'bs-deferred-action-status-pending' ).plain();
		} else if ( status === BS.action.Base.STATUS_RUNNING ) {
			message = mw.message( 'bs-deferred-action-status-running' ).plain();
		} else if ( status === BS.action.Base.STATUS_DONE ) {
			message = mw.message( 'bs-deferred-action-status-done' ).plain();
		} else if ( status === BS.action.Base.STATUS_ERROR ) {
			message = mw.message( 'bs-deferred-action-status-error' ).plain();
		}

		return message;
	}
} );
