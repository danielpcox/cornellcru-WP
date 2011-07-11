var blog_url;
jQuery(document).ready(function() {
		blog_url = jQuery("#la-networkpub_plugin_url").val();
		jQuery(".lanetworkpubre").live("click", function(e) {
				jQuery("#la-networkpub_msg").css('display', 'block');
				jQuery("#la-networkpub_msg").html('Removing...');	
				var key = jQuery(this).attr("id");
				jQuery(this).parent().parent().css('opacity','.30');
				if(key) {
						jQuery.post(blog_url+"la-click-and-share-networkpub_ajax.php", {lacandsnw_networkpub_key:key, type:'remove'}, function(data) {
								if(data == '500') {
										jQuery("#la-networkpub_msg").html('<div class="msg_error">Error occured while removing the Network. As a workaround, you can remove this publishing at the following link: <a href="http://www.linksalpha.com/user/publish">LinksAlpha Publisher</a> </div>');
								} else {
										jQuery("#r_"+key).remove();
										jQuery("#la-networkpub_msg").css('display', 'block');
										jQuery("#la-networkpub_msg").html('Network has been removed successfully');	
								}
						});
				} 
				return false;
		});	
	
	jQuery.receiveMessage(
		function(e){
			jQuery("#networkpub_postbox").height(e.data.split("=")[1]+'px');
		},
		'http://www.linksalpha.com'
	);
		
});