jQuery(document).ready(function(){

		jQuery('#rightnav a').live('click', function(event){
   			jQuery('#mobileNav').toggleClass('show');
   			jQuery('#loginNav').removeClass('show');
   			jQuery('#notifications-header').removeClass('show');		
	});   
	
		jQuery('#mobileNav ul li a').live('click', function(event){
   			jQuery(this).addClass('navLoad');
   					
	});

		jQuery('#leftnav-login a').live('click', function(event){
   			jQuery('#loginNav').toggleClass('show');	
   			jQuery('#mobileNav').removeClass('show');	
   			jQuery('#notifications-header').removeClass('show');
	}); 
	
		jQuery('#content').live('click', function(event){
   			jQuery('#mobileNav').removeClass('show');
   			jQuery('#loginNav').removeClass('show');
   			jQuery('#notificationsheader').removeClass('show');		
	});   
	
	jQuery('#notifications-badge').live('touchstart', function(event){
   			jQuery('#notifications-header').toggleClass('show');
   			jQuery('#loginNav').removeClass('show');
   			jQuery('#mobileNav').removeClass('show');	
   				
	}); 
	


		jQuery('#theme-switch').live('click', function(event){
			jQuery.cookie( 'bpthemeswitch', 'normal', {path: '/'} );			
	}); 		
	
		jQuery('#theme-switch-site').live('click', function(event){
			jQuery.cookie( 'bpthemeswitch', 'mobile', {path: '/'} );			
	});   
		
});
