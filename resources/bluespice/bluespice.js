/*
 * Core BlueSpice JavaScript Library
 */

var bs = ( function ( mw, $, undefined ) {

	const bs = {
		// TODO: maybe own file "bluespice.ns.js"?
			ns: {
				filter: {
					NO_TALK: [],
					ONLY_CONTENT_NS: [],
					ONLY_CUSTOM_NS: []
				}
			}
		},
		namespaceIds = mw.config.get( 'wgNamespaceIds' );
	for ( const lcNamespaceName in namespaceIds ) {
		const namespaceId = namespaceIds[ lcNamespaceName ];
		let ucNamespaceName = lcNamespaceName.toUpperCase();
		if ( namespaceId === 0 ) {
			ucNamespaceName = 'MAIN';
		}
		bs.ns[ 'NS_' + ucNamespaceName ] = namespaceId;

		// TODO: Known issue: Some NSIDs are duplicates: i.e. NS_FILE ad NS_IMAGE
		if ( namespaceId < 0 ) {
			bs.ns.filter.ONLY_CONTENT_NS.push( namespaceId );
		}

		if ( namespaceId > 0 && namespaceId % 2 !== 0 ) {
			bs.ns.filter.NO_TALK.push( namespaceId );
		}

		if ( namespaceId < 100 ) {
			bs.ns.filter.ONLY_CUSTOM_NS.push( namespaceId );
		}
	}

	bs.ns.filter.allBut = function ( excludeIds ) {
		const namespaceIds = mw.config.get( 'wgNamespaceIds' ),
			includeIds = [];
		for ( const lcNamespaceName in namespaceIds ) {
			const namespaceId = namespaceIds[ lcNamespaceName ];
			if ( excludeIds.indexOf( namespaceId ) !== -1 ) {
				continue;
			}
			includeIds.push( namespaceId );
		}
		return includeIds;
	};

	return bs;

}( mediaWiki, jQuery ) );

// Attach to window and globally alias
window.bs = window.blueSpice = bs;

mw.hook( 'importOffice.collectionPrefix' ).add( function ( params ) {
	const text = mw.config.get( 'bsgPageCollectionPrefix' );
	params.prefix = text;
} );
