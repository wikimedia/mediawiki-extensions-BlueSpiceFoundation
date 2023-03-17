( function ( mw, $, d, undefined ) {
	$( d ).on( 'click', '.bs-treenodeitem', function ( e ) {
		var $parentAnchor = $( e.target ).parentsUntil( '.bs-treenodeitem', 'a' );
		if ( e.target.nodeName.toUpperCase() === 'A' || $parentAnchor.length !== 0 ) {
			return; // Don't prevent clicks on anchor elements
		}
		if ( $( this ).hasClass( 'leaf' ) || $( this ).attr( 'aria-haspopup' ) !== 'true' ) {
			return;
		}
		if ( $( this ).attr( 'aria-expanded' ) === 'true' ) {
			$( this ).removeAttr( 'aria-expanded' );
		} else {
			$( this ).attr( 'aria-expanded', 'true' );
		}
		_updatePathCookie( $( this ) );
		e.preventDefault();
		return false;
	} );

	/**
	 *
	 * @param {jQuery} $node
	 * @return {undefined}
	 */
	function _updatePathCookie( $node ) {
		var $root = $node.parents( '.bs-tree-root' ).first(),
			// A visible leaf is any .bs-treenodeitem that as a parent with no
			// '.collaped' and is either self '.collapsed' or has no <ul>
			$visibleLeafs = $root.find( '.bs-treenodeitem' ).filter( function () {
				var $treeNode = $( this );
				if ( $treeNode.parents( 'li' ).hasClass( 'collapsed' ) ) {
					return false;
				}

				if ( $treeNode.hasClass( 'collapsed' ) || $treeNode.hasClass( 'leaf' ) ) {
					return true;
				}

				return false;
			} ),
			paths = [];
		$visibleLeafs.each( function () {
			paths.push( $( this ).data( 'bs-nodedata' ).path );
		} );

		mw.cookie.set(
			$root.attr( 'id' ),
			JSON.stringify( paths ),{
			expires: 14
		} );
	}
}( mediaWiki, jQuery, document ) );
