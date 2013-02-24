<?php
/**
 * The view controller for custom post types.
 *
 * @author Kunal Bhalla
 * 
 * @since 0.1
 */

/**
 * Any screen related logic, etc. is mashed into this class.
 *
 * @since 0.1
 */
class bpcp_V {

	/**
	 * A copy of settings stored in the main controller.
	 * Avoiding the global version to avoid 'timing' issues.
	 *
	 * @var Mixed Array $settings
	 *
	 * @since 0.1
	 */
	var $settings;

	/**
	 * Eases typing.
	 *
	 * @var string $id Post type identifier
	 *
	 * @since 0.1
	 */
	var $id;

	/**
	 * Constructor, initializes settings and id.
	 *
	 * @see bpcp::bpcp
	 *
	 * @since 0.1
	 */
	function bpcp_V( $settings ) {
		$this->settings = $settings;
		$this->id = $settings['id'];
	}

	/**
	 * Load the template for the user's posts. The actual
	 * template data is arranged for in the controller -- this
	 * loads the outer template.
	 *
	 * @since 0.1
	 */
	function my_posts() {
		bp_core_load_template( 'members/single/home' );
	}

	/**
	 * Load the template for the user's activity. The actual
	 * template data is arranged for in the controller -- this
	 * loads the outer template.
	 *
	 * @since 0.1
	 */
	function mytype_activity_page() {
		bp_core_load_template( 'members/single/home' );
	}

	/**
	 * Add the post type to the navigation menu in the theme.
	 *
	 * @uses $bp
	 *
	 * @since 0.1
	 */
	function setup_nav() {
		global $bp;
	
		$class = ( bp_is_page( $bp->{$this->id}->slug ) )? "class = 'selected'" : "";

		echo '<li '. $class .' ><a href = "' . get_option('siteurl') . '/' . $bp->{$this->id}->slug . '/">' . $this->settings['labels']['name'] . '</a></li>';
	}
	
	/**
	 * Load the directory template.
	 *
	 * First looks for <type name>/index, followed by the generic type/index.
	 *
	 * @since 0.1
	 */
	function directory() {
		bp_core_load_template( Array( "{$this->id}/index", 'type/index' ) );
	}

	/**
	 * Modify the title tag for the create page.
	 *
	 * @uses $bp
	 *
	 * @since 0.1
	 */
	function set_create_title( $title ) {
		global $bp;
		$type = get_post_type_object( $this->id );
		return $title . $type->labels->add_new_item;
	}

	/** 
	 * Load the template for a single page.
	 *
	 * Loads in the order <type name>/single/home, type/single/home, single (WP default).
	 *
	 * @uses $bp
	 *
	 * @since 0.1
	 */
	function single_home() {
		global $bp;

		$bp->bp_options_title = get_the_title();

		//Rewind the loop so that the theme can use it.
		rewind_posts();

		bp_core_load_template( Array( "$this->id/single/home", 'type/single/home', 'single' ) );
	}

	/** 
	 * Load the template for a single page.
	 *
	 * Loads in the order <type name>/single/home, type/single/home, single (WP default).
	 *
	 * @uses $bp
	 *
	 * @since 0.1
	 */
	function single_activity() {
		global $bp;

		$bp->bp_options_title = get_the_title();

		rewind_posts();
		bp_core_load_template( Array( "$this->id/single/home", 'type/single/home', 'single' ) );
	}

	/**
	 * Adds the my-posts tab to the activity page.
	 *
	 * @uses $current_user
	 * @users $bp
	 *
	 * @since 0.1
	 */
	function activity_tab() {
		global $current_user, $bp;
		wp_get_current_user();

		$type = get_post_type_object( $this->id );
		$count = bpcp_get_user_count( $current_user->ID, $this->id );

		if ( $count->publish ) { ?>
			<li id="activity-<?php echo $bp->{$this->id}->slug; ?>"><a href="<?php echo bp_loggedin_user_domain() . BP_ACTIVITY_SLUG . '/' . $bp->{$this->id}->slug . '/' ?>"><?php printf( $type->labels->my_posts, $count->publish ) ?></a></li>
		<?php }
	}

	/**
	 * Loading the forum template -- again, loads home page.
	 * The magic is done using an action inside it.
	 *
	 * @since 0.1
	 */
	function forum() {
		global $bp;

		$bp->bp_options_title = get_the_title();
		rewind_posts();
		bp_core_load_template( Array( "$this->id/single/home", 'type/single/home', 'single' ) );
	}

	/**
	 * Load the posts by a user. Searches in the order:
	 * members/single/<name>.php, members/single/type.php
	 *
	 * @since 0.1
	 */
	function my_posts_inner() {
		bpcp_locate_template( Array( "members/single/{$this->id}.php", 'members/single/type.php' ), true );
	}

	/**
	 * Modify the upload URL for creating/editing.
	 *
	 * @param string $source The URL
	 *
	 * @since 0.1
	 */
	function redirect_upload_iframe( $source ) {
		return get_bloginfo( 'url' ) . '/wp-admin/' . $source;
	}

	/**
	 * To do everything that has to be done before the actual rendering of the edit page
	 *
	 * @uses $current_screen
	 * @uses $typenow
	 * @uses $post
	 * @uses $current_user
	 * @uses $post_ID
	 * @uses $post_id
	 *
	 * @since 0.1
	 */
	function edit() {
		global $current_screen, $typenow, $post, $current_user, $post_ID, $post_id;

		$admin_path  = ABSPATH . '/wp-admin';
		$include_url = get_bloginfo( 'wpurl' ) . '/wp-includes';
		$admin_url = get_bloginfo( 'wpurl' ) . '/wp-admin';

		//The admin template API
		require_once( $admin_path . '/includes/template.php' );
		//The post administration API.
		require_once( $admin_path . '/includes/post.php' );
		//Admin, pre-defined metaboxes.
		require_once( $admin_path . '/includes/meta-boxes.php' );
		//Admin, media
		require_once( $admin_path . '/includes/media.php' );
		//Comments API
		require_once( $admin_path . '/includes/comment.php' );

		//For the uploader
		add_filter( 'image_upload_iframe_src', Array( &$this, 'redirect_upload_iframe' ) );
		add_filter( 'video_upload_iframe_src', Array( &$this, 'redirect_upload_iframe' ) );
		add_filter( 'audio_upload_iframe_src', Array( &$this, 'redirect_upload_iframe' ) );
		add_filter( 'media_upload_iframe_src', Array( &$this, 'redirect_upload_iframe' ) );

		//Create vs Edit
		if ( $post == '' )
			$post = get_default_post_to_edit( $this->id, true );
		$post_ID = $post->ID;
		$post_id = $post_ID;

		//Just a check, if required at any point
		if ( !defined( 'BPCP_ADMIN' ) )
			define( 'BPCP_ADMIN', true );

		nocache_headers();

		$typenow = $this->id;
		$type = $this->id;
		set_current_screen( 'post' );

		wp_enqueue_style( 'bpcp_edit', BPCP_THEMES_ASSETS . 'css/edit' . kb_ext() . '.css');

		if ( post_type_supports($typenow, 'editor') ) {
			wp_enqueue_script( 'utils' );
		//	wp_enqueue_script( 'quicktags' );
		//	wp_enqueue_script( 'editor' );
		//	wp_enqueue_script( 'tinymce', $include_url . '/js/tinymce/tiny_mce.js' );
			wp_enqueue_script( 'bpcp_editor', BPCP_THEMES_ASSETS . 'js/editor' . kb_ext() . '.js' );
			
			wp_enqueue_script( 'word-count', $admin_url . '/js/word-count.js' );
			wp_localize_script( 'word-count', 'wordCountL10n', array(
				'count' => __('Word count: %d'),
				'l10n_print_after' => 'try{convertEntities(wordCountL10n);}catch(e){};'
			));
		
			$suffix = "";
			if ( defined( 'WP_DEBUG' ) ) $suffix = ".dev";

			wp_enqueue_script( 'media-upload', $admin_url . "/js/media-upload$suffix.js", array( 'thickbox' ), '20091023' );
		}

		add_thickbox();

		if( user_can_richedit() )
			add_action( 'bp_after_footer', 'wp_tiny_mce' );
		else
			add_action( 'bp_after_footer', 'wp_quicktags' );

		//Registering metaboxes
		//Basic publish button
		add_meta_box( 'bpcp_submit', __( 'Publish' ), Array( &$this, 'submit_metabox' ), $type, 'side' );

		//Basic avatar
		add_meta_box( 'bpcp_thumbnail', __( 'Thumbnail' ), 'post_thumbnail_meta_box', $type, 'side' );

		//Use to add your own metaboxes.
		do_action( 'bpcp_edit_add_metaboxes' );

		do_action( 'bpcp_edit_page', $this->id );
		do_action( 'bpcp_' . $this->id . '_edit_page' );
	}

	/**
	 * Wrapper for creating a new post.
	 *
	 * @since 0.1
	 */
	function create() {
		$this->edit();

		add_filter( 'bp_page_title', Array( &$this, 'set_create_title' ) );
		bp_core_load_template( 'type/create', true );
	}

	/**
	 * Wrapper for editing a post, loading template after that.
	 *
	 * @since 0.1
	 */
	function single_edit() {
		$this->edit();
		$this->single_home();
	}

	/**
	 * The metabox for submitting a post.
	 *
	 * An embarassingly simplified version of the actual publish metabox.
	 *
	 * @uses $post
	 *
	 * @since 0.1
	 */
	function submit_metabox() {
		global $post;
		//Adds a submit button early on for pressing enter. Could tabindex = 1 have done the same?
	?>
		<div style="display:none;">
			<input type="submit" name="save" value="<?php esc_attr_e('Save'); ?>" />
		</div>

		<div id="save">
	<?php
		
		if ( 'publish' == $post->post_status )  {
	?>
			<input name="save" type="submit" class="button-primary" id="publish" tabindex="5" accesskey="p" value="<?php esc_attr_e('Update') ?>" />
	<?php
		} else {
	?>
			<input name="save" type="submit" class="button-primary" id="publish" tabindex="5" accesskey="p" value="<?php esc_attr_e('Publish') ?>" />
	<?php
		}
	}

}
