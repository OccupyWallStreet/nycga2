<?php

function mrt_opt_mng_pg() {
	mrt_wpss_menu_head('WP-Security Admin tools by WebsiteDefender');

			add_meta_box("wpss_mrt_1", 'Initial Scan', "wpss_mrt_meta_box", "wpss");
			add_meta_box("wpss_mrt_2", 'System Information Scan', "wpss_mrt_meta_box2", "wpss2");
			add_meta_box("wpss_mrt_3", 'About Website Defender', "wsd_render_main", "wpss_wsd");

echo '	
			<div class="metabox-holder">
				<div style="float:left; width:48%;" class="inner-sidebar1">';
		 
					do_meta_boxes('wpss','advanced',''); 	
					do_meta_boxes('wpss2','advanced',''); 	

echo '		
				</div>
				<div style="float:right;width:48%;" class="inner-sidebar1">';
					do_meta_boxes('wpss_wsd','advanced','');  
echo '	
				</div>
						
				<div style="clear:both"></div>
			</div>';

	mrt_wpss_menu_footer();

	}
?>