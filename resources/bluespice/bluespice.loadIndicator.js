( function( mw, $, bs, d, undefined ) {
	var LoadIndicator = function( cfg ) {
		cfg = cfg || {};
		this.$element = cfg.$element;

		this.queue = 0;
		// Min. time request must take in order to show loading
		this.inTimeout = 100;
		// Min. time loading indicator can stay on
		this.outTimeout = 200;
		//Timers to show and hide loader
		this.showTimer = null;
		this.hideTimer = null;

		// Time when the loading started
		this.loadingStart = 0;
		// When DOM ready, cancel initial loading
		this.doSetLoading( false );
	};

	LoadIndicator.prototype.pushPending = function() {
		this.queue++;
		if ( this.queue > 0 ) {
			this.setLoading( true );
		}
	};

	LoadIndicator.prototype.popPending = function() {
		if ( this.queue === 0 ) {
			return;
		}
		this.queue--;
		if ( this.queue === 0 ) {
			this.setLoading( false );
		}
	};

	LoadIndicator.prototype.setLoading = function( show ) {
		var hideTime;
		if( show ) {
			clearTimeout( this.hideTimer );
			this.hideTimer = null;
			this.showTimer = setTimeout( function() {
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
			this.hideTimer = setTimeout( function() {
				this.doSetLoading( false );
			}.bind( this ), hideTime );
		}
	};

	LoadIndicator.prototype.doSetLoading = function( value ) {
		if ( value ) {
			return this.$element.addClass( 'loading' );
		}
		return this.$element.removeClass( 'loading' );
	};

	LoadIndicator.prototype.getQueue = function() {
		return this.queue;
	};

	$( function() {
		// Call this on DOMReady, to make sure element is there
		bs.loadIndicator = new LoadIndicator( {
			$element: $( '.loader-indicator' )
		} );
	} );

} )( mediaWiki, jQuery, blueSpice, document );
