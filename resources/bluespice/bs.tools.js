BsTools = {
	tooltipInit: function(){
		var target = $(this).next().find('#' + $(this).attr('id') + '-target');
		$(this).next().offset({ left: $(this).offset().left });

		$(this).hover( function() {
			$(this).next().stop( true, true ).slideDown();
		}, function() {
			$(this).next().stop( true, true ).delay(1000).slideUp();
		});
		$(this).next().hover( function() {
			$(this).stop( true, true ).slideDown();
		}, function() {
			$(this).stop( true, true ).delay(1000).slideUp();
		});

		if( typeof target.attr('data-maxheight') === 'undefined' ) return;
		//items with display none have no height :(
		//if( target.height() < target.attr('data-maxheight') ) return;

		target.slimScroll({
			height: target.attr('data-maxheight') + 'px',
			color: '#3F527F',
			alwaysVisible: true,
			railVisible: true
		});
	}
}

//Tooltips
$.fn.bstooltip = function(){
	$(this).each( BsTools.tooltipInit );
};