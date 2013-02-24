<?php function get_pro(){ 

	if( defined('is_pro') ): 	
		return; 

	else: ?>
		 <div id="cap_getpro">
			<div class="getpro_content">
			    <a href="http://themekraft.com/shop/custom-community-pro/" title="Custom Community Pro" target="_blank">
			    <img src="<?php echo get_template_directory_uri(); ?>/_inc/images/get-pro.jpg" width="861" height="600" style="margin:10px 0;" />
			    </a> 
			</div>
		</div>
	    <div class="spacer"></div><?php 
	endif; 
} ?>