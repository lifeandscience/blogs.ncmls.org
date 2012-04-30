jQuery(function(){
	var title;
	jQuery('#piclist ul.pics li').hover(function(){
		title = jQuery('.title', jQuery(this).parent().parent()).html();
		jQuery('.title', jQuery(this).parent().parent()).html(jQuery('a', this).html());
	}, function(){
		jQuery('.title', jQuery(this).parent().parent()).html(title);
	});
});