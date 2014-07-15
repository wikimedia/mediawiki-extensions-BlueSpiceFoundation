$(function(){
	$('.bs-tooltip-link').each(function(){

		var conf = {
			target: this,
			title: $(this).data('bs-tt-title') || '&#160;',
			html: $(this).data('bs-tt-html') || '',
			contentEl: $(this).data('bs-tt-target') || document,
			anchor: $(this).data('bs-tt-anchor') || 'top',
			autoHide: $(this).data('bs-tt-autohide') || false,
			maxHeight: $(this).data('bs-tt-maxheight'),
			minWidth: $(this).data('bs-tt-maxheight') || 100,
			overflowX: 'auto',
			overflowY: 'auto'
		};

		Ext.create('Ext.tip.ToolTip', conf);
	});
});