(function($) {

	$(document).ready(function() {
		$('#clickthru_url_search').on('psu_item_selected', function(e, data) {
			$('#clickthru_url').val(data.target.data('permalink'));
			e.preventDefault();
		});
	});

})(jQuery);

