<?php
add_action('wp_print_scripts', 'status_js_all');
function status_js_all() {
	global $bp;
	/*
	$bp->action_variable[0] = extras
	$bp->action_variable[1] = fields | fields-manage
	*/
	if (is_admin())
		wp_enqueue_script('STATUS_ADMIN_JS', WP_PLUGIN_URL.'/GA-Status/_inc/admin-scripts.js', array('jquery') );
		
	if ( $bp->current_component == $bp->groups->slug && $bp->is_single_item && 'admin' == $bp->current_action && $bp->action_variables[0] == 'extras' ){
		wp_enqueue_script('STATUS_EXTRA_JS', WP_PLUGIN_URL.'/GA-Status/_inc/extra-scripts.js', array('jquery') );
		// localize js string
		add_action('wp_head', 'status_js_localize', 5);
		wp_enqueue_script('jquery-ui-sortable');
	}
}

function status_js_localize(){ ?>
	<script type="text/javascript">/* <![CDATA[ */
	var status = {
		enter_options: '<?php _e('Please enter options for this Field','status') ?>',
		option_text: '<?php _e('Option','status') ?>',
		remove_it: '<?php _e('Remove It','status') ?>'
	};
	/* ]]> */
	</script>
<?php 
}

add_action('wp_print_styles', 'status_css_all');
function status_css_all() {
	global $bp;

	if (is_admin())
		wp_enqueue_style('STATUS_ADMIN_CSS', WP_PLUGIN_URL.'/GA-Status/_inc/admin-styles.css');
		
	if ( $bp->current_component == $bp->groups->slug && $bp->is_single_item && 'admin' == $bp->current_action && $bp->action_variables[0] == 'extras' ) 
		wp_enqueue_style('STATUS_EXTRA_CSS', WP_PLUGIN_URL.'/GA-Status/_inc/extra-styles.css');
		
}
