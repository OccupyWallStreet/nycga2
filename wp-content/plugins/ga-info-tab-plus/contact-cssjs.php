<?php
add_action('wp_print_scripts', 'info_tab_plus_js_all');
function info_tab_plus_js_all() {
	global $bp;
	/*
	$bp->action_variable[0] = extras
	$bp->action_variable[1] = fields | fields-manage
	*/
	if (is_admin())
		wp_enqueue_script('info_tab_plus_ADMIN_JS', WP_PLUGIN_URL.'/ga-contact/_inc/admin-scripts.js', array('jquery') );
		
	if ( $bp->current_component == $bp->groups->slug && $bp->is_single_item && 'admin' == $bp->current_action && $bp->action_variables[0] == 'extras' ){
		wp_enqueue_script('info_tab_plus_EXTRA_JS', WP_PLUGIN_URL.'/ga-contact/_inc/extra-scripts.js', array('jquery') );
		// localize js string
		add_action('wp_head', 'info_tab_plus_js_localize', 5);
		wp_enqueue_script('jquery-ui-sortable');
	}
}

function info_tab_plus_js_localize(){ ?>
	<script type="text/javascript">/* <![CDATA[ */
	var contact = {
		enter_options: '<?php _e('Please enter options for this Field','info') ?>',
		option_text: '<?php _e('Option','info') ?>',
		remove_it: '<?php _e('Remove It','info') ?>'
	};
	/* ]]> */
	</script>
<?php 
}

add_action('wp_print_styles', 'info_tab_plus_css_all');
function info_tab_plus_css_all() {
	global $bp;

	if (is_admin())
		wp_enqueue_style('info_tab_plus_ADMIN_CSS', WP_PLUGIN_URL.'/ga-contact/_inc/admin-styles.css');
		
	if ( $bp->current_component == $bp->groups->slug && $bp->is_single_item && 'admin' == $bp->current_action && $bp->action_variables[0] == 'extras' ) 
		wp_enqueue_style('info_tab_plus_EXTRA_CSS', WP_PLUGIN_URL.'/ga-contact/_inc/extra-styles.css');
		
}
