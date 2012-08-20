	var $jquery = jQuery.noConflict(); 
	$jquery(document).ready(function() 
	{ 

	if($jquery.cookie('demo_cookie') == null) {
		$jquery('#wp-social-toolbar').fadeIn(2000);
	}
	else
	{
        $jquery('#wp-social-toolbar').show();
		$jquery('#wps-toolbar-content').hide();
		$jquery('#wp-social-toolbar-show-box').fadeIn(2000);
	}

	$jquery('.wpsc_close_button').click(function(){
		$jquery('#wps-toolbar-content').hide().delay(5000).animate('hide',7000,'swing');
		$jquery('#wp-social-toolbar-show-box').show();
		$jquery.cookie('demo_cookie', 'Demo Cookie');
        
	});

	$jquery('.wpsc_show_button').click(function(){
		$jquery('#wp-social-toolbar-show-box').hide();
		$jquery.cookie('demo_cookie', null);
		$jquery('#wps-toolbar-content').show().delay(5000).animate('show',7000,'swing');
		
	});
	if($jquery('#twitter_update_list li').length>0)
	{
		
	}
	else
	{
		$jquery('#twitter_update_list').html('<li>ERROR READING TWITTER FEED</li>');
	}


});