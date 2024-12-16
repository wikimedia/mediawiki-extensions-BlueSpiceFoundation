( function ( mw, bs, $, undefined ) {
	/**
	 * BlueSpice client-side config
	 *
	 * See doc/bs.config.md
	 *
	 * @param {Object} base Default configs
	 * @param {boolean} [strict=false] Do not try to infer variable names
	 */
	const BlueSpiceConfig = function ( base, strict ) {
		this.strict = strict || false;
		this.base = base || {};
		this.vars = {};
		this.api = new mw.Api();

		this.initialized = false;
	};

	/**
	 * Get the value of locally stored variable
	 *
	 * @param {string} name
	 * @return {*}
	 */
	BlueSpiceConfig.prototype.get = function ( name ) {
		if ( !this.initialized ) {
			this.init();
		}

		name = this.normalizeName( name );
		return this.vars.hasOwnProperty( name ) ?
			this.vars[ name ] :
			null;
	};

	/**
	 * Load BS vars from main config to this config
	 */
	BlueSpiceConfig.prototype.init = function () {
		let parsedName;
		const base = $.extend( {}, this.base );

		for ( const name in base ) {
			if ( !base.hasOwnProperty( name ) ) {
				continue;
			}
			parsedName = this.parseName( name );
			if ( !parsedName ) {
				continue;
			}
			this.vars[ parsedName ] = base[ name ];
		}

		this.initialized = true;
	};

	/**
	 * Get the value of remote variable(s)
	 *
	 * @param {string|Array} name
	 * @param {boolean} [forceRemote=false] If false, will return local value, if present
	 * @param {Object} [context={}] Optional context for retrieving the variable
	 * @return {Promise}
	 */
	BlueSpiceConfig.prototype.getDeferred = function ( name, forceRemote, context ) {
		forceRemote = forceRemote || false;
		context = context || {};

		const dfd = $.Deferred(),
			normalized = [];
		let i = 0,
			finalValues = {},
			missing = [];

		if ( Array.isArray( name ) ) {
			for ( i = 0; i < name.length; i++ ) {
				normalized.push( this.normalizeName( name[ i ] ) );
			}
		} else {
			normalized.push( this.normalizeName( name ) );
		}

		if ( !forceRemote ) {
			finalValues = this.doGatherLocal( normalized );
		}

		missing = this.getMissing( normalized, finalValues );
		if ( missing.length === 0 ) {
			this.doResolve( dfd, finalValues );
			return dfd.promise();
		}

		bs.api.config.get( missing, context )
			.done( function ( value ) {
				finalValues = $.extend( finalValues, value );
				this.vars = $.extend( this.vars, finalValues );
				this.doResolve( dfd, finalValues );
			}.bind( this ) ).fail( function ( error ) {
				dfd.reject( error );
			} );

		return dfd.promise();
	};

	/**
	 * Check if variable is present in config
	 *
	 * @param {string} name
	 * @param {boolean} [checkRemote=false] Checks remote variables. If true returns a promise
	 * @return {(boolean|Promise)}
	 */
	BlueSpiceConfig.prototype.has = function ( name, checkRemote ) {
		checkRemote = checkRemote || false;
		name = this.normalizeName( name );

		const existsLocally = this.vars.hasOwnProperty( name );
		if ( !checkRemote ) {
			return existsLocally;
		}

		return bs.api.config.has( name );
	};

	/**
	 * Try to infer correct name from the given name
	 * This will recognize common name prefixes and strip them
	 *
	 * @param {string} name
	 * @return {string} Parsed name, or name as-is, if config is in strict mode or name cannot be parsed
	 */
	BlueSpiceConfig.prototype.normalizeName = function ( name ) {
		if ( this.strict ) {
			return name;
		}
		return this.parseName( name ) || name;
	};

	/**
	 * Get normalized name of a variable
	 *
	 * @param {string} name
	 * @return {(string|null)}
	 */
	BlueSpiceConfig.prototype.parseName = function ( name ) {
		const prefix = name.slice( 0, 3 );
		let varName = null;
		if ( prefix === 'bsg' ) {
			varName = name.slice( 3 );
		} else if ( prefix.slice( 0, 2 ).toLowerCase() === 'bs' ) {
			varName = name.slice( 2 );
		}

		return varName;
	};

	BlueSpiceConfig.prototype.doGatherLocal = function ( normalized ) {
		const result = {};
		let i = 0,
			name = '';
		if ( !this.initialized ) {
			this.init();
		}
		for ( i; i < normalized.length; i++ ) {
			name = normalized[ i ];
			if ( this.has( name ) ) {
				result[ name ] = this.get( name );
			}
		}

		return result;
	};

	BlueSpiceConfig.prototype.getMissing = function ( normalized, final ) {
		const missing = [];
		let i = 0;
		for ( i; i < normalized.length; i++ ) {
			if ( final.hasOwnProperty( normalized[ i ] ) ) {
				continue;
			}
			missing.push( normalized[ i ] );
		}

		return missing;
	};

	BlueSpiceConfig.prototype.doResolve = function ( dfd, final ) {
		const objectKeys = Object.keys( final );
		if ( objectKeys.length === 1 ) {
			const firstObjectKey = objectKeys[ 0 ];
			dfd.resolve( final[ firstObjectKey ] );
			return;
		}
		dfd.resolve( final );
	};

	bs.config = new BlueSpiceConfig( mw.config.values );
}( mediaWiki, blueSpice, jQuery ) );
