( function ( mw, $, bs ) {
	bs.ui.widget.util.UserMenuOptionWidget = function ( user ) {
		var config = {};

		config.data = user;
		bs.ui.widget.util.UserMenuOptionWidget.parent.call( this, config );

		this.$element.children().remove();
		if ( user.user_image !== '' ) {
			var $userImage = $( user.user_image );
			this.$element.append( $userImage.find( 'img' ).addClass( 'user-image' ) );
		}

		var $nameBox = $( '<div>' ).addClass( 'user-name-cnt' );
		$nameBox.append( $( '<span>' ).addClass( 'user-display' ).text( user.display_name ) );
		if ( user.display_name !== user.user_name ) {
			$nameBox.append( $( '<span>' ).addClass( 'user-username' ).text( user.user_name ) );
		}

		this.$element.append( $nameBox );
		this.$element.addClass( 'bs-ui-widget-UserPicker-menu-option' );
		$( '.bs-banner' ).append( this.$element );
	};

	OO.inheritClass( bs.ui.widget.util.UserMenuOptionWidget, OO.ui.MenuOptionWidget );

}( mediaWiki, jQuery, blueSpice ) );
