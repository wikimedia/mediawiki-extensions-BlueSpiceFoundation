/*
 * Core BlueSpice JavaScript Library
 */

var bs = ( function ( mw, $, undefined ) {

	var bs = {
		//TODO: maybe own file "bluespice.ns.js"?
		ns: {
			filter: {
				NO_TALK: [],
				ONLY_CONTENT_NS: [],
				ONLY_CUSTOM_NS: []
			}
		}
	};
	var namespaceIds = mw.config.get('wgNamespaceIds');
	for( var lcNamespaceName in namespaceIds ) {

		var namespaceId = namespaceIds[lcNamespaceName];
		var ucNamespaceName = lcNamespaceName.toUpperCase();
		if( namespaceId === 0 ) {
			ucNamespaceName = 'MAIN';
		}
		bs.ns['NS_'+ucNamespaceName] = namespaceId;

		//TODO: Known issue: Some NSIDs are duplicates: i.e. NS_FILE ad NS_IMAGE
		if( namespaceId < 0 ) {
			bs.ns.filter.ONLY_CONTENT_NS.push( namespaceId );
		}

		if( namespaceId > 0 && namespaceId % 2 !== 0 ) {
			bs.ns.filter.NO_TALK.push( namespaceId );
		}

		if( namespaceId < 100 ) {
			bs.ns.filter.ONLY_CUSTOM_NS.push( namespaceId );
		}
	}

	bs.ns.filter.allBut = function( excludeIds ) {
		var namespaceIds = mw.config.get('wgNamespaceIds');
		var includeIds = [];
		for( var lcNamespaceName in namespaceIds ) {
			var namespaceId = namespaceIds[lcNamespaceName];
			if( excludeIds.indexOf(namespaceId) !== -1 ) {
				continue;
			}
			includeIds.push(namespaceId);
		}
		return includeIds;
	};

	return bs;

}( mediaWiki, jQuery ) );

// Attach to window and globally alias
window.bs = window.blueSpice = bs;