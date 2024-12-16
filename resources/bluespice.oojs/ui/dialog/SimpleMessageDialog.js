bs.ui.dialog.SimpleMessageDialog = function ( config ) {
	const params = {};

	if ( config.hasOwnProperty( 'name' ) ) {
		bs.ui.dialog.SimpleMessageDialog.static.name = this.name;
	} else {
		bs.ui.dialog.SimpleMessageDialog.static.name = 'SimpleMessageDialog';
	}

	if ( config.hasOwnProperty( 'id' ) ) {
		this.id = config.id;
		params.id = this.id;
	}

	if ( config.hasOwnProperty( 'idPrefix' ) ) {
		this.idPrefix = config.idPrefix;
	} else if ( this.hasOwnProperty( 'id' ) ) {
		this.idPrefix = this.id;
	} else {
		this.idPrefix = '';
	}

	if ( config.hasOwnProperty( 'size' ) ) {
		bs.ui.dialog.SimpleMessageDialog.static.size = config.size;
	} else {
		bs.ui.dialog.SimpleMessageDialog.static.size = 'small';
	}

	if ( config.hasOwnProperty( 'titleMsg' ) ) {
		bs.ui.dialog.SimpleMessageDialog.static.title = mw.message( config.titleMsg ).plain();
	} else if ( config.hasOwnProperty( 'title' ) ) {
		bs.ui.dialog.SimpleMessageDialog.static.title = config.title;
	}

	if ( config.hasOwnProperty( 'textMsg' ) ) {
		bs.ui.dialog.SimpleMessageDialog.static.message =
			new OO.ui.HtmlSnippet( mw.message( config.textMsg ).plain() );
	} else if ( config.hasOwnProperty( 'text' ) ) {
		bs.ui.dialog.SimpleMessageDialog.static.message =
			new OO.ui.HtmlSnippet( config.text );
	}

	if ( config.hasOwnProperty( 'callback' ) ) {
		this.callback = config.callback;
	} else {
		this.callback = {};
	}

	if ( config.callback.hasOwnProperty( 'scope' ) ) {
		this.callback.scope = config.callback.scope;
	} else {
		this.callback.scope = {};
	}

	bs.ui.dialog.SimpleMessageDialog.prototype.makeActionAccept = function () {
		const params = {
			action: 'ok',
			label: mw.message( 'ooui-dialog-message-accept' ).plain()
		};
		if ( this.hasOwnProperty( 'id' ) ) {
			params.id = this.idPrefix + '-btn-ok';
		}
		return params;
	};

	bs.ui.dialog.SimpleMessageDialog.prototype.makeActionReject = function () {
		const params = {
			action: 'cancel',
			label: mw.message( 'ooui-dialog-message-reject' ).plain()
		};
		if ( this.hasOwnProperty( 'id' ) ) {
			params.id = this.idPrefix + '-btn-cancel';
		}
		return params;
	};

	bs.ui.dialog.SimpleMessageDialog.super.call( this, params );
};

OO.inheritClass( bs.ui.dialog.SimpleMessageDialog, OO.ui.MessageDialog );

bs.ui.dialog.SimpleMessageDialog.prototype.initialize = function () {
	bs.ui.dialog.SimpleMessageDialog.super.prototype.initialize.call( this );
};

bs.ui.dialog.SimpleMessageDialog.prototype.getActionProcess = function ( action ) {
	if ( ( action === 'ok' ) && this.callback.hasOwnProperty( 'ok' ) ) {
		this.callback.ok.call( this.callback.scope );
	}
	if ( ( action === 'cancel' ) && this.callback.hasOwnProperty( 'cancel' ) ) {
		this.callback.cancel.call( this.callback.scope );
	}
	return bs.ui.dialog.SimpleMessageDialog.super.prototype.getActionProcess.call( this, action );
};
