bs.ui.dialog.PromptDialog = function ( config ) {
	bs.ui.dialog.PromptDialog.super.call( this, config );

	this.name = config.name || 'PromptDialog';
	this.textInput = {};

	bs.ui.dialog.PromptDialog.static.actions = [
		bs.ui.dialog.SimpleMessageDialog.prototype.makeActionReject.call( this ),
		bs.ui.dialog.SimpleMessageDialog.prototype.makeActionAccept.call( this )
	];
};

OO.inheritClass( bs.ui.dialog.PromptDialog, bs.ui.dialog.SimpleMessageDialog );

bs.ui.dialog.PromptDialog.prototype.initialize = function () {
	bs.ui.dialog.PromptDialog.super.prototype.initialize.call( this );

	this.textInput = new OO.ui.TextInputWidget( {
		value: ''
	} );

	this.text.$element.append( this.textInput.$element );
};

bs.ui.dialog.PromptDialog.prototype.getActionProcess = function ( action ) {
	if ( ( action === 'ok' ) && this.callback.hasOwnProperty( 'ok' ) ) {
		this.callback.ok.call(
			this.callback.scope,
			{
				value: this.textInput.getValue()
			}
		);
	}
	if ( ( action === 'cancel' ) && this.callback.hasOwnProperty( 'cancel' ) ) {
		this.callback.cancel.call( this.callback.scope );
	}
	return bs.ui.dialog.SimpleMessageDialog.super.prototype.getActionProcess.call( this, action );
};
