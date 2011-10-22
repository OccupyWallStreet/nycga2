<?php
add_action('wp_print_scripts', 'bpge_js_all');
function bpge_js_all() {
	global $bp;
	/*
	$bp->action_variable[0] = extras
	$bp->action_variable[1] = fields | fields-manage
	*/
	if (is_admin())
		wp_enqueue_script('BPGE_ADMIN_JS', WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/admin-scripts.js', array('jquery') );
		
	if ( $bp->current_component == $bp->groups->slug && $bp->is_single_item && 'admin' == $bp->current_action && $bp->action_variables[0] == 'extras' ){
		wp_enqueue_script('BPGE_EXTRA_JS', WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/extra-scripts.js', array('jquery') );
		// localize js string
		add_action('wp_head', 'bpge_js_localize', 5);
		wp_enqueue_script('jquery-ui-sortable');
	}
}

function bpge_js_localize(){ ?>
	<script type="text/javascript">/* <![CDATA[ */
	var bpge = {
		enter_options: '<?php _e('Please enter options for this Field','bpge') ?>',
		option_text: '<?php _e('Option','bpge') ?>',
		remove_it: '<?php _e('Remove It','bpge') ?>'
	};
	/* ]]> */
	</script>
<?php 
}

add_action('wp_print_styles', 'bpge_css_all');
function bpge_css_all() {
	global $bp;

	if (is_admin())
		wp_enqueue_style('BPGE_ADMIN_CSS', WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/admin-styles.css');
		
	if ( $bp->current_component == $bp->groups->slug && $bp->is_single_item && 'admin' == $bp->current_action && $bp->action_variables[0] == 'extras' ) 
		wp_enqueue_style('BPGE_EXTRA_CSS', WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/extra-styles.css');
		
}
