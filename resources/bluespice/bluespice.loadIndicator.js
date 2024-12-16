( function ( mw, $, bs, d, undefined ) {
	bs.LoadIndicator = function ( cfg ) {
		cfg = cfg || {};
		this.$element = cfg.$element;
		this.highStart = cfg.highStart || false;

		this.queue = 0;
		// Min. time request must take in order to show loading
		this.inTimeout = cfg.inTimeout || 100;
		// Min. time loading indicator can stay on
		this.outTimeout = cfg.outTimeout || 200;
		// Timers to show and hide loader
		this.showTimer = null;
		this.hideTimer = null;

		// Time when the loading started
		this.loadingStart = 0;

		// High start initially keep loading
		// until someone calls popPending()
		if ( this.highStart ) {
			if ( !this.isLoading() ) {
				this.doSetLoading( true );
			}
			this.queue = 1;
		} else {
			this.doSetLoading( false );
		}
	};

	bs.LoadIndicator.prototype.pushPending = function () {
		this.queue++;
		if ( this.queue > 0 ) {
			this.setLoading( true );
		}
	};

	bs.LoadIndicator.prototype.popPending = function () {
		if ( this.queue === 0 ) {
			return;
		}
		this.queue--;
		if ( this.queue === 0 ) {
			this.setLoading( false );
		}
	};

	bs.LoadIndicator.prototype.setLoading = function ( show ) {
		let hideTime;
		if ( show ) {
			clearTimeout( this.hideTimer );
			this.hideTimer = null;
			this.showTimer = setTimeout( function () {
				this.loadingStart = Date.now();
				this.doSetLoading( true );
			}.bind( this ), this.inTimeout );
		} else {
			clearTimeout( this.showTimer );
			this.showTimer = null;
			// If loader has already been visible for more than minimum time
			if ( ( Date.now() - this.loadingStart ) >= this.outTimeout ) {
				this.doSetLoading( false );
			}
			hideTime = this.outTimeout;
			if ( this.loadingStart ) {
				hideTime = this.outTimeout - ( Date.now() - this.loadingStart );
			}
			this.hideTimer = setTimeout( function () {
				this.doSetLoading( false );
			}.bind( this ), hideTime );
		}
	};

	bs.LoadIndicator.prototype.doSetLoading = function ( value ) {
		this.$element.trigger( 'stateChanged', [ value ] );
		if ( value ) {
			return this.$element.addClass( 'loading' );
		}
		return this.$element.removeClass( 'loading' );
	};

	bs.LoadIndicator.prototype.getQueue = function () {
		return this.queue;
	};

	bs.LoadIndicator.prototype.isLoading = function () {
		return this.$element.hasClass( 'loading' );
	};

	$( function () {
		// Call this on DOMReady, to make sure elements are there
		bs.loadIndicator = new bs.LoadIndicator( {
			$element: $( '.loader-indicator.global' )
		} );
	} );

}( mediaWiki, jQuery, blueSpice, document ) );
