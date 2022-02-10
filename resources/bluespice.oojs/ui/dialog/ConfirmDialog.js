bs.ui.dialog.ConfirmDialog = function ( config ) {
	bs.ui.dialog.ConfirmDialog.super.call( this, config );

	this.name = config.name || 'ConfirmDialog';

	bs.ui.dialog.ConfirmDialog.static.actions = [
		bs.ui.dialog.SimpleMessageDialog.prototype.makeActionReject.call( this ),
		bs.ui.dialog.SimpleMessageDialog.prototype.makeActionAccept.call( this )
	];
};

OO.inheritClass( bs.ui.dialog.ConfirmDialog, bs.ui.dialog.SimpleMessageDialog );

bs.ui.dialog.ConfirmDialog.prototype.initialize = function () {
	bs.ui.dialog.ConfirmDialog.super.prototype.initialize.call( this );
};
