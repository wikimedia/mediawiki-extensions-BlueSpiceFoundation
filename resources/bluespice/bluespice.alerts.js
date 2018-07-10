(function( mw, $, bs, d, undefined ) {

	var _alerts = {};

	/**
	 * Types and markup inspired by
	 * https://getbootstrap.com/docs/3.3/components/#alerts
	 * @param string id
	 * @param jQuery $elem
	 * @param string type May be 'success', 'info', 'warning', 'danger'
	 * @returns jQuery The actual alert wrapper element
	 */
	function _add( id, $elem, type ) {
		type = type || bs.alerts.TYPE_WARNING;

		if( _alerts[id] ) {
			var $oldAlert = _alerts[id];
			$oldAlert.remove();
		}

		var $box = $( '<div class="alert alert-' + type + '" role="alert">' );
		$box.append( $elem );
		var $container = _getContainer();
		$container.append( $box );

		_alerts[id] = $box;

		return $box;
	}

	function _remove( id ) {
		var $box = _alerts[id];
		if( $box ) {
			$box.remove();
			delete( _alerts[id] );
		}
	}

	function _getContainer() {
		var $container = $( '#bs-alert-container' );
		return $container;
	}

	//Init server-side generated alerts
	$(function() {
		var $container = _getContainer();
		var $boxes = $container.find( '[data-bs-alert-id]' );
		$boxes.each( function() {
			var $box = $(this);
			var id = $box.data( 'bs-alert-id' );
			_alerts[id] = $box;
		} );
	});

	bs.alerts = {
		add: _add,
		remove: _remove,

		//Keep in sync with IAlertProvider constants
		TYPE_SUCCESS: 'success',
		TYPE_INFO: 'info',
		TYPE_WARNING: 'warning',
		TYPE_DANGER: 'danger'
	};
})( mediaWiki, jQuery, blueSpice, document );