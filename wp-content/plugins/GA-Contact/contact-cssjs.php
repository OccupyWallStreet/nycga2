<?php
add_action('wp_print_scripts', 'contact_js_all');
function contact_js_all() {
	global $bp;
	/*
	$bp->action_variable[0] = extras
	$bp->action_variable[1] = fields | fields-manage
	*/
	if (is_admin())
		wp_enqueue_script('CONTACT_ADMIN_JS', WP_PLUGIN_URL.'/ga-contact/_inc/admin-scripts.js', array('jquery') );
		
	if ( $bp->current_component == $bp->groups->slug && $bp->is_single_item && 'admin' == $bp->current_action && $bp->action_variables[0] == 'extras' ){
		wp_enqueue_script('CONTACT_EXTRA_JS', WP_PLUGIN_URL.'/ga-contact/_inc/extra-scripts.js', array('jquery') );
		// localize js string
		add_action('wp_head', 'contact_js_localize', 5);
		wp_enqueue_script('jquery-ui-sortable');
	}
}

function contact_js_localize(){ ?>
	<script type="text/javascript">/* <![CDATA[ */
	var contact = {
		enter_options: '<?php _e('Please enter options for this Field','contact') ?>',
		option_text: '<?php _e('Option','contact') ?>',
		remove_it: '<?php _e('Remove It','contact') ?>'
	};
	/* ]]> */
	</script>
<?php 
}

add_action('wp_print_styles', 'contact_css_all');
function contact_css_all() {
	global $bp;

	if (is_admin())
		wp_enqueue_style('CONTACT_ADMIN_CSS', WP_PLUGIN_URL.'/ga-contact/_inc/admin-styles.css');
		
	if ( $bp->current_component == $bp->groups->slug && $bp->is_single_item && 'admin' == $bp->current_action && $bp->action_variables[0] == 'extras' ) 
		wp_enqueue_style('CONTACT_EXTRA_CSS', WP_PLUGIN_URL.'/ga-contact/_inc/extra-styles.css');
		
}
