<?php 
/**
 * FOOTER
 *
 * This file controls the ending HTML </body></html> and common graphical
 * elements in your site footer. You can control what shows up where using
 * WordPress and PageLines PHP conditionals
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
 * @internal    last revised February November 21, 2011
 * @version     ...
 *
 * @todo Define version
 */

if(!has_action('override_pagelines_body_output')): 
		pagelines_register_hook('pagelines_start_footer'); // Hook ?>
				</div>
				<?php pagelines_register_hook('pagelines_after_main'); // Hook ?>
				<div id="morefoot_area" class="container-group">
					<?php pagelines_template_area('pagelines_morefoot', 'morefoot'); // Hook ?>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>

<?php pagelines_register_hook('pagelines_before_footer'); // Hook ?>
	<footer id="footer" class="container-group">
		<div class="outline fix">
		<?php 
			pagelines_template_area('pagelines_footer', 'footer'); // Hook 
			pagelines_register_hook('pagelines_after_footer'); // Hook
			pagelines_cred(); 
		?>
		</div>
	</footer>
</div>
<?php 

endif;

	print_pagelines_option('footerscripts'); // Load footer scripts option 	
	wp_footer(); // Hook (WordPress) 
?>
</body>
</html>
