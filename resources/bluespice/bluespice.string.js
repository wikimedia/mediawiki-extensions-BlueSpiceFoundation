//String functions
String.prototype.ellipse = function ( maxLength ) {
	if ( this.length > maxLength ) {
		return this.substr( 0, maxLength-3 ) + '...';
	}
	return this;
};

String.prototype.trim = function () {
	// TODO MRG (21.09.10 16:29): warum \xA0? könnte man auch einen u-flag verwenden?
	var newString = this.replace(/^[\s\xA0]+/, "");
	newString = this.replace(/[\s\xA0]+$/, "");
	return newString;
};

// taken from http://stackoverflow.com/questions/646628/how-to-check-if-a-string-startswith-another-string
String.prototype.startsWith = function ( startString ) {
	return this.slice( 0, startString.length ) === startString;
};

String.prototype.endsWith = function ( endString ) {
	return endString === '' || this.slice( -endString.length ) === endString;
};

String.prototype.format = function () {
	var args = arguments;
	return this.replace(/{(\d+)}/g, function(match, number) {
		return typeof args[ number ] !== 'undefined' ? args[number] : match;
	});
};
//hint: http://stackoverflow.com/questions/3629183/why-doesnt-indexof-work-on-an-array-ie8/3629211#3629211
if ( !Array.prototype.indexOf ) {
	Array.prototype.indexOf = function( elt /*, from*/ )
	{
		var len = this.length >>> 0;

		var from = Number( arguments[ 1 ] ) || 0;
		from = ( from < 0 ) ? Math.ceil( from ) : Math.floor( from );
		if ( from < 0 ) {
			from += len;
		}

		for ( ; from < len; from++ ) {
			if ( from in this &&
					this[from] === elt ) {
				return from;
			}
		}
		return -1;
	};
}