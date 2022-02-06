bs.ui.dialog.AlertDialog = function ( config ) {
	bs.ui.dialog.AlertDialog.super.call( this, config );

	this.name = config.name || 'AlertDialog';

	bs.ui.dialog.AlertDialog.static.actions = [
		bs.ui.dialog.SimpleMessageDialog.prototype.makeActionAccept.call( this )
	];
};

OO.inheritClass( bs.ui.dialog.AlertDialog, bs.ui.dialog.SimpleMessageDialog );

bs.ui.dialog.AlertDialog.prototype.initialize = function () {
	bs.ui.dialog.AlertDialog.super.prototype.initialize.call( this );
};
