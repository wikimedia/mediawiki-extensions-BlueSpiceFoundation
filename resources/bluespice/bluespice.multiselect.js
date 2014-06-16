$('form').each(function() {
	$(this).submit(function() {
		$(this).find('.multiselectplusadd').each(function(index, item) {
			var i;
			for (i = item.length - 1; i>=0; i--) {
				item.options[i].selected = true;
			}
		}) ;
		return true;
	});
});
$('form').each(function() {
	$(this).find('.multiselectplusadd').each(function(index, item) {
		var i;
		for (i = item.length - 1; i>=0; i--) {
			item.options[i].selected = false;
		}
	});
});