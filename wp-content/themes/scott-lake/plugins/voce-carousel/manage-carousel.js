/* global wpAjax */
(function($) {
	$('#add_to_carousel').on('click', '#push-to-carousel', function(e){
		e.preventDefault();
		var $link = $(this),
		$metabox = $link.parent('div');

		$metabox.html('');
		$.getJSON($link.attr('href'), function(data){
				if(data.success) {
					$metabox.html(data.html);
				} else {
					$metabox.html('<span class="error">An error occurred.  Please try again.</span>');
				}
		});

	});

	$('#quick_create_carousel_item').on('click', function(e){
		e.preventDefault();
		$.post(ajaxurl, {
			action : 'quick_create_carousel_item',
			post_id : $('#post_ID').val(),
			_ajax_nonce : $(this).data('nonce')
		}, function(r){
			var res = wpAjax.parseAjaxResponse(r, 'ajax-response');
			if( !res || res.errors ){
				return;
			}

			if(res.responses[0].data){
				if(window.confirm('You will now be redirected to the new carousel item post. Any unsaved data on this post will be lost. Proceed?')){
					window.location = res.responses[0].data;
				}
			}
		});
		return false;
	});

})(jQuery);

