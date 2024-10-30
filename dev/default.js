var c4dInstagram = {};
(function($){
	"use strict";
	$(document).ready(function(){
		$('.c4d-instagram').each(function(){
			var user = $(this).attr('data-user'),
			uid = $(this).attr('id'),
			params = c4dInstagram[uid],
			self = this;
			// url = 'https://www.instagram.com/'+user+'/media/';
			
			$.ajax({
				type: 'GET',
				dataType: 'json',
				url: c4d_instagram.ajax_url,
				data: {
					'action': 'c4d_instagram_get_user', 
					'username': user
				}
			}).done(function(data){
				if ( typeof data.items !== 'undefined' && data.items.length > 0 ){
					var count = params.count ? params.count : 4,
					html = [];
					$.each(data.items, function(index, el){
						var img = el.images.thumbnail.url,
						lImage = el.images.standard_resolution.url;
						if (index < count) {
							html.push('<div class="item"><div class="item-inner"><a rel="image-gallery" class="image-link fancybox" href="'+lImage+'"><img src="'+img+'"></a></div></div>');	
						}
					});	
					$(self).append(html.join(''));
					$(self).find('.image-link').fancybox({
						margin: 100,
						openEffect  : 'none',
						closeEffect : 'none',
						cyclic: true
					});
	  			}
			});
		});
	});
})(jQuery);