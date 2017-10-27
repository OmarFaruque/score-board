jQuery(document).ready(function($){
	$('.details a.details').click(function(){
		$(this).closest('div.details').find('.details_Content').slideToggle();
	});
});
