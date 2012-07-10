<?php
/**
 * SIDEBAR (SIDEBAR WRAP)
 *
 * This file controls the sidebar wrap template; which depending on the mode
 * container one or both sidebars from layout.
 *
 * @package     PageLines Framework
 * @since       1.0
 *
 * @link        http://www.pagelines.com/
 * @link        http://www.pagelines.com/tour
 *
 * @author      PageLines   http://www.pagelines.com/
 * @copyright   Copyright (c) 2008-2012, PageLines  hello@pagelines.com
 *
 * @internal    This is used by BuddyPress as well and should play nice with child themes.
 *
 * @internal    last revised February 2, 2012
 * @version     ...
 *
 * @todo Define version
 */

if(has_action('override_pagelines_sidebar_wrap')):
	do_action('override_pagelines_sidebar_wrap');

else:

	pagelines_register_hook('pagelines_before_sidebar_wrap'); // Hook

	global $pagelines_layout;
	
	$GLOBALS['sidebar_was_run'] = true;
	
	if($pagelines_layout->layout_mode != 'fullwidth'):?>

		<div id="sidebar-wrap" class="">
	<?php 
				if(ploption('sidebar_wrap_widgets') == 'top' || !ploption('sidebar_wrap_widgets')){
					pagelines_template_area('pagelines_sidebar_wrap', 'sidebar_wrap'); // Hook
				}
		
			if($pagelines_layout->layout_mode != 'two-sidebar-center'):?>
				<div id="sidebar1" class="scolumn" >
					<div class="scolumn-pad">
						<?php pagelines_template_area('pagelines_sidebar1', 'sidebar1'); // Hook ?>	
					</div>
				</div>
			<?php endif;
		
			if($pagelines_layout->num_columns == 3): ?>
				<div id="sidebar2" class="scolumn">
					<div class="scolumn-pad">
						<?php pagelines_template_area('pagelines_sidebar2', 'sidebar2'); // Hook ?>
					</div>
				</div>
	<?php 	endif;

				if(ploption('sidebar_wrap_widgets') == 'bottom'){
					pagelines_template_area('pagelines_sidebar_wrap', 'sidebar_wrap'); // Hook
				}
			
			?>
		</div>		
	<?php 
	endif;

	pagelines_register_hook('pagelines_after_sidebar_wrap'); // Hook

endif;
