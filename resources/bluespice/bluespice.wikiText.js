( function ( mw, bs, $, undefined ) {
	"use strict";
	
	bs.wikiText = {};

	//HINT: http://www.bolinfest.com/javascript/inheritance.php
	//HINT: http://ejohn.org/blog/simple-javascript-inheritance/
	bs.wikiText.Link = function( cfg ) {
		var me = this;
		
		//TODO: make private?
		this.properties = {
			escaped: false,
			title: '',
			prefixedTitle: '',
			nsText: '',
			thumb: false,
			thumbsize: 120,
			right: false,
			left: false,
			center: false,
			align: '',
			none: false,
			frameless: false,
			frame: false,
			border: false,
			upright: false,
			alt: '',
			displayText: '',
			link: '',
			sizewidth: false,
			sizeheight: false
		}
		
		if( typeof(cfg) == 'object' ) {
			this.properties = $.extend( this.properties, cfg );
		}
		else{
			parsePropertiesFromString(cfg);
		}
		
		function parseTitle( title ) {
			if( title.charAt( 0 ) == ':' ) {
				me.properties.escaped = true;
				title = title.substring( 1, title.length ); //remove leading ":""
			}
			
			me.properties.title = title;
			
			var titleParts = title.split( ':' );
			if( titleParts.length > 1 ) {
				me.properties.nsText = titleParts.shift();
				me.properties.title = titleParts.join(':');
			}
		}
		
		function parsePropertiesFromString( wikiText ) {
			wikiText = wikiText.substring(2, wikiText.length -2 ); //trim "[[" and "]]"

			var parts = wikiText.split("|");
			parseTitle( parts[0] );

			for( var i = 1; i < parts.length; i++ ) {
				var part = parts[i];
				if( part.endsWith('px') ) { //Dependency BlueSpiceFramework.js
					var unsuffixedValue = part.substr( 0, part.length - 2 ); //"100x100px" --> "100x100"
					me.properties.sizewidth= unsuffixedValue;
					var dimensions = unsuffixedValue.split('x'); //"100x100"
					if( dimensions.length == 2 ) {
						me.properties.sizewidth = dimensions[0] == '' ? false : dimensions[0]; //"x100"
						me.properties.sizeheight= dimensions[1];
					}
					me.properties.frame = false; //Only size _or_ frame: see MW doc
					continue;
				}

				if( $.inArray( part, ['thumb', 'mini', 'miniatur'] ) != -1 ) {
					me.properties.thumb = true;
					continue;
				}
				if( $.inArray( part, ['right', 'rechts'] ) != -1 ) {
					me.properties.right = true;
					me.properties.align = 'right';
					continue;
				}
				if( $.inArray( part, ['left', 'links'] ) != -1 ) {
					me.properties.left = true;
					me.properties.align = 'left';
					continue;
				}
				if( $.inArray( part, ['center', 'zentriert'] ) != -1 ) {
					me.properties.center = true;
					me.properties.align = 'center';
					continue;
				}
				if( $.inArray( part, ['none', 'ohne'] ) != -1 ) {
					me.properties.none = true;
					continue;
				}
				if( $.inArray( part, ['frame', 'gerahmt'] ) != -1 ) {
					me.properties.frame = true;
					me.properties.sizewidth  = false;
					me.properties.sizeheight = false; //Only size _or_ frame: see MW doc
					continue;
				}
				if( $.inArray( part, ['frameless', 'rahmenlos'] ) != -1 ) {
					me.properties.frameless = true;
					continue;
				}
				if( $.inArray( part, ['border', 'rand'] ) != -1 ) {
					me.properties.border = true;
					continue;
				}

				var kvpair = part.split('=');
				if( kvpair.length == 1 ) {
					me.properties.displayText = part; //hopefully
					continue;
				}

				var key   = kvpair[0];
				var value = kvpair[1];

				if( $.inArray( key, ['link', 'verweis'] ) != -1 ) {
					me.properties.link = value;
					continue;
				}

				if( $.inArray( key, ['upright', 'hochkant'] ) != -1 ) {
					me.properties.upright = value;
					continue;
				}

				if( key == 'alt' ) {
					me.properties.alt = value;
					continue;
				}
			}
		}

		this.toString = function() {
			//Build wikitext
			var wikiText = [];
			var prefix = '';
			if( this.properties.escaped ) {
				prefix += ':';
			}
			if( this.properties.nsText != '' ) {
				prefix += this.properties.nsText + ':';
			}
			wikiText.push( prefix + this.properties.title );
			for( var property in this.properties ) {
				if( $.inArray(property, ['title','thumbsize'])  != -1 ) continue; //Filter non-wiki data
				if( $.inArray(property, ['left','right', 'center']) != -1 ) continue; //Not used stuff
				var value = this.properties[property];
				if( value == "" || typeof value == "undefined" ) continue;

				if( property == 'sizewidth' ) {
					var size = '';
					if( this.properties.sizewidth && this.properties.sizewidth != "false" ) {
						size = this.properties.sizewidth;
					}
					if( this.properties.sizeheight && this.properties.sizeheight != "false" ) {
						size += 'x' + this.properties.sizeheight;
					}
					if( size.length > 0 ) size += 'px';
					wikiText.push(size);
					continue;
				}
				if( $.inArray( property, [ 'alt', 'link' ] ) != -1 ) {
					wikiText.push(property +'='+value);
					continue;
				}
				if( $.inArray( property, ['caption', 'align'] ) != -1 ) {
					wikiText.push( value );
					continue;
				}
				if( value == "true" ) {
					wikiText.push( property ); //frame, border, thumb, left, right...
				}
			}

			return '[[' + wikiText.join('|') + ']]';
		}
		
		this.isEscaped = function( newValue ){
			if( newValue == undefined ) { //Getter
				return this.properties.escaped;
			}
			this.properties.escaped = newValue ? true : false; //Setter
			return this.properties.escaped;
		}
		this.getTitle = function(){
			return this.properties.title;
		}
		this.setTitle = function(){
			return this.properties.title;
		}
		this.getPrefixedTitle = function(){
			return this.properties.prefixedTitle;
		}
		this.setPrefixedTitle = function(){
			return this.properties.prefixedTitle;
		}
		this.getNsText = function(){
			return this.properties.nsText;
		}
		this.setNsText = function(){
			return this.properties.nsText;
		}
		this.isThumb = function( newValue ){
			if( newValue == undefined ) { //Getter
				return this.properties.thumb;
			}
			this.properties.thumb = newValue ? true : false; //Setter
			return this.properties.thumb;
		}
		this.getThumbsize = function(){
			return this.properties.thumbsize;
		}
		this.setThumbsize = function(){
			return this.properties.thumbsize;
		}
		this.getPosition = function(){
			return this.properties.title;
		}
		this.setPosition = function(){
			return this.properties.title;
		}
		this.getAlign = function(){
			return this.properties.align;
		}
		this.setAlign = function(){
			return this.properties.align;
		}
		this.isNone = function( newValue ){
			if( newValue == undefined ) { //Getter
				return this.properties.none;
			}
			this.properties.none = newValue ? true : false; //Setter
			return this.properties.none;
		}
		this.isFrameless = function( newValue ){
			if( newValue == undefined ) { //Getter
				return this.properties.frameless;
			}
			this.properties.frameless = newValue ? true : false; //Setter
			return this.properties.frameless;
		}
		this.hasFrame = function( newValue ){
			if( newValue == undefined ) { //Getter
				return this.properties.frame;
			}
			this.properties.frame = newValue ? true : false; //Setter
			return this.properties.frame;
		}
		this.hasBorder = function( newValue ){
			if( newValue == undefined ) { //Getter
				return this.properties.border;
			}
			this.properties.border = newValue ? true : false; //Setter
			return this.properties.border;
		}
		this.isUpright = function( newValue ){
			if( newValue == undefined ) { //Getter
				return this.properties.upright;
			}
			this.properties.upright = newValue ? true : false; //Setter
			return this.properties.upright;
		}
		this.getAlt = function(){
			return this.properties.alt;
		}
		this.setAlt = function(){
			return this.properties.alt;
		}
		this.getDisplayText = function(){
			return this.properties.displaytext;
		}
		this.setDisplayText = function(){
			return this.properties.displaytext;
		}
		this.getCaption = function(){
			return this.getDisplayText();
		}
		this.setCaption = function( newValue ){
			return this.setDisplayText( newValue );
		}
		this.getLink = function(){
			return this.properties.title;
		}
		this.setLink = function(){
			return this.properties.title;
		}
		this.getSize = function(){
			return this.properties.title;
		}
		this.setSize = function(){
			return this.properties.title;
		}
	}

	bs.wikiText.Template = function( cfg, title ) {
		var me = this;
		
		this.params = {};
		this.title = '';

		if( typeof(cfg) == 'object' ) { //"{ with: 'param' }"
			this.title = title; //"Some Template"
			this.params = $.extend( this.params, cfg );
		}
		else{ //WikiText "{{Some Template|with=param}}"
			parseParamsFromString(cfg);
		}
		
		function parseParamsFromString( wikiText ) {
			wikiText = wikiText.substring(2, wikiText.length -2 ); //trim "{{" and "}}"
			//TODO: What about linebreaks?
			var parts = wikiText.split("|");
			this.title = parts[0];
			
			for( var i = 1; i < parts.length; i++ ) {
				//TODO: implement
			}
		}
		
		this.toString = function() {
			//Build wikitext
			var wikiText = [];
			wikiText.push( this.title );

			for( var param in this.params ) {
				
				var keyValuePair = param + "=";
				
				//TODO: handle nested "bs.wikiText.Template" objects
				keyValuePair += this.params[param];
			}
			return '{{' + wikiText.join('|') + '}}';
		}
		//jQuery-like setter
		this.set = function( keyOrCfg, value ) {
			//TODO: implement
		}
	}
	
}( mediaWiki, blueSpice, jQuery ) );