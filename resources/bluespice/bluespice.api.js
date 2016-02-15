/*
 * Implementation for bs.api
 */

( function ( mw, bs, $, undefined ) {

	/**
	 * e.g. bs.api.tasks.exec(
			'wikipage',
			'setCategories',
			{ categories: [ 'C1', 'C2' ] }
		)
		.done(...);
	 * @param string module
	 * @param string taskname
	 * @param object data
	 * @returns jQuery.Promise
	 */
	function _execTask( module, task, data, cfg ) {
		cfg = cfg || {};
		cfg = $.extend( {
			token: 'edit',
			context: {}, //TODO: Implement context as in CommonAjaxInterface
			success: _msgSuccess,
			failure: _msgFailure
		}, cfg );

		var $dfd = $.Deferred();

		var api = new mw.Api();
		//api.postWithToken( cfg.token, { //If Change https://gerrit.wikimedia.org/r/#/c/127589/ gets merged this needs to be enabled
		api.post( {
			action: 'bs-'+ module +'-tasks',
			task: task,
			taskData: JSON.stringify( data )
		})
		.done(function( response ){
			if ( response.success === true ) {
				cfg.success( response, module, task, $dfd, cfg );
			} else {
				cfg.failure( response, module, task, $dfd, cfg );
			}
		})
		.fail( function( code, errResp ) { //Server error like FATAL
			var dummyResp = {
				success: false,
				message: errResp.exception,
				errors: [{
					message: code
				}]
			};
			cfg.failure( dummyResp, module, task, $dfd, cfg );
		});
		return $dfd.promise();
	}

	function _msgSuccess( response, module, task, $dfd, cfg ) {
		if ( response.message.length ) {
			//TODO: Dependency to 'ext.bluespice.extjs'?
			bs.util.alert(
				module + '-' + task + '-success',
				{

					titleMsg: 'bs-extjs-title-success',
					text: response.message
				},
				{
					ok: function() {
						$dfd.resolve( response );
					}
				}
			);
		}
		else {
			$dfd.resolve( response );
		}
	}

	function _msgFailure( response, module, task, $dfd, cfg ) {
		var message = response.message || '';
		if ( response.errors.length > 0 ) {
			for ( var i in response.errors ) {
				if ( typeof( response.errors[i].message ) !== 'string' ) continue;
				message = message + '<br />' + response.errors[i].message;
			}
		}
		bs.util.alert(
			module + '-' + task + '-fail',
			{
				titleMsg: 'bs-extjs-title-warning',
				text: message
			},
			{
				ok: function() {
					$dfd.reject( response );
				}
			}
		);
	}

	bs.api = {
		tasks: {
			exec: _execTask
		}
	};

}( mediaWiki, blueSpice, jQuery ) );
