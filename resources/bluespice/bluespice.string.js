//String functions
String.prototype.ellipse = function( maxLength ) {
	if ( this.length > maxLength ) {
		return this.substr( 0, maxLength-3 ) + '...';
	}
	return this;
};

String.prototype.trim = function() {
	// TODO MRG (21.09.10 16:29): warum \xA0? könnte man auch einen u-flag verwenden? 
	var newString = this.replace(/^[\s\xA0]+/, "");
	newString = this.replace(/[\s\xA0]+$/, "");
	return newString;
}

String.prototype.startsWith = function( startString ) {
	var doesStartWith = ( this.match( "^" + startString ) == startString );
	return doesStartWith;
}

String.prototype.endsWith = function( endString ) {
	var doesEndWith = ( this.match( endString + "$" ) == endString );
	return doesEndWith;
}

String.prototype.format = function() {
	var args = arguments;
	return this.replace(/{(\d+)}/g, function(match, number) { 
		return typeof args[number] != 'undefined' ? args[number] : match;
	});
};
