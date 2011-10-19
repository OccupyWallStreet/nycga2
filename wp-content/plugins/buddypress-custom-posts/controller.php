<?php
/**
 * controller.php
 *
 * The main controller for BuddyPress Custom posts. A new instance
 * of the controller is instantiated for every custom post type
 * registered as a component. That's why the strict avoidance of
 * globals.
 * 
 * @author Kunal Bhalla
 */

/**
 * The controller class for custom posts.
 *
 * All filters, actions, etc. are instantiated here. There is various
 * hook/filter order craziness spread throughout this class, but hopefully
 * you won't have to worry too much about it.
 *
 * @since 0.1
 */
class bpcp {

	/**
	 * The arguments, as well as slugs for this post.
	 *
	 * @var Mixed Array $settings
	 *
	 * @since 0.1
	 */
	var $settings;

	/**
	 * The post type identifier. This 'id' will be converted to
	 * 'ids' for the component. (Movie->Movies, etc.)
	 *
	 * @var String $id
	 *
	 * @since 0.1
	 */
	var $id;

	/**
	 * The views class instance for this object.
	 *
	 * @var bpcp_Views $bpcp_v
	 *
	 * @since 0.1
	 */
	var $bpcp_v;

	/**
	 * The models class instance for this object.
	 *
	 * @var bpcp_Models $bpcp_m
	 *
	 * @since 0.1
	 */
	var $bpcp_m;

	/**
	 * Constructor.
	 *
	 * Registers the post type as a custom component; initiates hooking
	 * and sets functions to be called at the correct points in WordPress.
	 *
	 * Arguments accepted:
	 *	id => The post type id
	 *	labels => Default to 'Example' based labels, should be overwritten
	 *		name => Examples
	 *		my_posts => My Examples
	 *		posts_directory => Examples Directory
	 *		home => Home
	 *		edit => Edit
	 *		activity => Activity
	 *	theme_nav => whether to display navigation in the theme
	 *	nav => whether to add to the buddybar
	 *	theme_dir => will first check this directory for files while loading
	 *	             any template
	 *	activity => an array with a list of post events for which you want activity
	 *                  posts. Currently offers 'create' and 'edit'.
	 *	forum => allow posts to have their own forums? (boolean)
	 *
	 *
	 * @param Mixed Array $args The settings.
	 *
	 * @since 0.1
	 */
	function bpcp( $args = Array() ) {

		$defaults = Array(
			'id'	=> 'example',
			'labels' => Array(
				'name' => 'Examples',
				'my_posts' => 'My Examples',
				'posts_directory' => 'Examples Directory',
				'all_posts' => 'All Examples',
				'home' => __( 'Home', 'bpcp' ),
				'edit' => __( 'Edit', 'bpcp' ),
				'activity' => __( 'Activity', 'bpcp' )
			),
			'theme_nav' => false,
			'nav' => false,
			'theme_dir' => dirname( __FILE__ ) . '/themes',
			'activity' => Array(),
			'forum' => false
		);

		//Store all arguments provided as settings.
		$this->settings = wp_parse_args( $args, $defaults );
		$this->settings['labels'] = wp_parse_args( $args['labels'], $defaults['labels'] );
		$this->settings['activity'] = wp_parse_args( $args['activity'], $defaults['activity'] );

		//Reduce typing
		$this->id = $this->settings['id'];

		//Create all the slugs that will be required.
		$this->settings['slug'] = strtolower( preg_replace( '/ /', '-', $this->settings['labels']['name'] ) );
		$this->settings['slugs'] = (object) Array(
			'my_posts' => _x( sanitize_title_with_dashes( 
				preg_replace( '/\(\%.+?\)/', '', $this->settings['labels']['my_posts'] ) 
			), 'slug for own posts', 'bpcp' ),
			'single_home' => _x( 'home', 'slug for home page', 'bpcp' ),
			'single_edit' => _x( 'edit', 'slug for the edit page', 'bpcp' ),
			'single_activity' => _x( 'activity', 'slug for activity page', 'bpcp' )
		);

		$this->settings = apply_filters( 'bpcp_settings', $this->settings, $this->id ); 
		$this->settings = apply_filters( 'bpcp_' . $this->id . '_settings', $this->settings ); 

		//Initialize the instances of views and models for this particular component.
		$this->bpcp_v = new bpcp_V( $this->settings );
		$this->bpcp_m = new bpcp_M( $this->settings );

		//Setup globals
		add_action( 'bp_setup_globals', Array( &$this, 'setup_globals' ) );

		//The main controller that shows views/controls models based on the action provided.
		add_action( 'wp', Array( &$this, 'screen_controller' ), 1 );

		//Add to the buddybar: after init so that the post_type has been set up, before 10 to act as a controller too.
		add_action( 'wp', Array( &$this, 'setup_navbar' ), 2 );

		//Add to the navigation on the front page.
		add_action( 'bp_nav_items', Array( &$this->bpcp_v, 'setup_nav' ) );

		//Load custom template for this.
		add_filter( 'bp_located_template', Array( &$this, 'load_template' ), 10, 2);

		//Enable activity notification for post creation, editing
		if ( isset( $this->settings['activity']['create_posts'] ) && function_exists( 'bp_activity_add' ) )
			add_action( 'transition_post_status', Array( &$this, 'create_post_activity' ), 10, 3 );

		if ( isset( $this->settings['activity']['edit_posts'] ) && function_exists( 'bp_activity_add' ) )
			add_action( 'edit_post', Array( &$this, 'edit_post_activity' ), 10, 2 );

		//register as a root component
		add_action( 'bp_setup_root_components', Array( &$this, 'setup_root_component' ) );

		//get the template tags for themes that might use them
		$this->load_template_tags();

		//modify the custom post type
		add_action( 'init', Array( &$this, 'modify_custom_type' ) );

		//add translation support
		add_action( 'init', Array( &$this, 'load_textdomain' ) );

		//hook into ajax handling
		add_action( 'wp_ajax_' . $this->settings['slug'] . '_filter', Array( &$this, 'ajax_directory' ) );
		add_action( 'wp_ajax_' . $this->id . '_filter', Array( &$this, 'ajax_directory' ) );

		//add thumbnail support. Can this go into a better place?
		add_theme_support( 'post-thumbnails' );

		//add action to update last activity time
		add_action( 'bp_activity_add', Array( &$this, 'update_last_activity' ) );

		//Always get the new permalinks. Also, consider a redirect for all blog pages?
		add_action( 'post_type_link', Array( &$this, 'special_permalinks' ), 10, 4 );

		//Add my-posts to activity tab
		add_action( 'bp_before_activity_type_tab_mentions', Array( &$this->bpcp_v, 'activity_tab' ) );

		//Add the filter for custom post type ajax requests
		add_filter( 'bp_dtheme_activity_feed_url', Array( &$this, 'feed_url' ) );

		//Add options to the activity filter
		add_action( 'bp_member_activity_filter_options', Array( &$this, 'activity_options' ) );
		add_action( 'bp_activity_filter_options', Array( &$this, 'activity_options' ) );

		//Filter out type based activity
		add_action( 'bp_ajax_querystring', Array( &$this, 'type_activity' ), 11, 2 ); 
		add_action( 'bp_ajax_querystring', Array( &$this, 'type_forums' ), 11, 2 ); 
		//add_action( 'bp_ajax_querystring', Array( &$this, 'activity_debug' ), 20, 2 ); 

		//Add a forum based on settings. todo make this on a per item basis.
		if ( $this->settings['forum'] && function_exists( 'bp_forums_setup' ) ) 
			add_action( 'edit_post', Array( &$this, 'create_forum' ), 10, 2 );
	}		

	/**
	 * Hooked into post editing: creates a forum at post creation /
	 * editing a post if one doesn't exist yet.
	 *
	 * @param int $postid The id of the post being edited
	 * @param int $post The post object
	 *
	 * @uses bpcp_M->new_forum()
	 *
	 * @since 0.1
	 */
	function create_forum($postid, $post) {
		if ( $post->post_type == $this->id ) {
			$forumid = get_post_meta( $postid, '_bpcp_forum_id', true );
			if ( '' != $forumid )
				return;

			$this->bpcp_m->new_forum( $post );

			do_action( 'bpcp_new_forum', $this->id );
			do_action( 'bpcp_' . $this->id . '_new_forum' );
		}
	}

	/**
	 * Load the textdomain for this plugin translation.
	 *
	 * @since 0.1.2.2
	 */
	function load_textdomain() {
		load_plugin_textdomain( 'bpcp', false, basename( BPCP_DIR ) . '/lang' );
	}

	/**
	 * Modifies the feed URL in the activity page for this post type.
	 *
	 * @param string $url The current URL
	 *
	 * @since 0.1
	 */
	function feed_url( $url ) {
		global $bp;

		if ( $_POST['scope'] == $bp->{$this->id}->slug )
			return $bp->loggedin_user->domain . BP_ACTIVITY_SLUG . '/' . $bp->{$this->id}->slug . '/feed/';
		
		return apply_filters( 'bpcp_feedurl' . $this->id . '', apply_filters( 'bpcp_feedurl', $url ) );
	}

	/**
	 * Filter the activity stream for the post type and current post.
	 *
	 * Hooked into the loop for activities.
	 *
	 * @param string $query_string The current query string
	 * @param string $object The type of query (activity, in this case).
	 *
	 * @since 0.1
	 */
	function type_activity( $query_string, $object ) {
		global $bp, $post;

		if ( ( isset( $_COOKIE[ 'bp-activity-scope' ] ) && $_COOKIE[ 'bp-activity-scope' ] == $bp->{$this->id}->slug ) || ( 'activity' == $object && $bp->current_action == $bp->{$this->id}->slug ) ) {
			$query_string = $this->filter_activity( $query_string, $object );
		} 

		if ( $bp->current_component == $bp->{$this->id}->slug && $bp->current_action != '' && $object == 'activity' ) {
			$query_string = rtrim( ('primary_id=' . $post->ID . '&' . $query_string), '&' ) ;
		}

		return $query_string;
	}

	/**
	 * Load the forum for the given post type.
	 *
	 * Queries the post meta for forum id.
	 *
	 * @param string $query_string The current query string
	 * @param string $object The type of query (forums, in this case).
	 *
	 * @since 0.1
	 */
	function type_forums( $query_string, $object ) {
		global $bp, $post;
		if ( $bp->current_component == $bp->{$this->id}->slug && $bp->current_action != '' && $object == 'forums' ) {
			if ( '' != ( $forum_id = get_post_meta( $post->ID, '_bpcp_forum_id', true ) ) )
				$query_string = rtrim( ('forum_id=' . $forum_id . '&' . $query_string), '&' ) ;
		}
		return $query_string;
	}

	/**
	 * Debugging function: dumps the query string and object.
	 *
	 * @since 0.1
	 */
	function activity_debug( $query_string, $object ) {
		var_dump( $query_string, $object );

		return $query_string;
	}

	/**
	 * Add another option to sort by on the activity page.
	 *
	 * @since 0.1
	 */
	function activity_options() {
		$type = get_post_type_object( $this->id );

		if ( isset( $this->settings['activity']['create_posts'] ) ) {
			?><option value="<?php echo "new_{$this->id}"; ?>"><?php echo $type->labels->show_created; ?></option><?php
			do_action( 'bpcp_activity_options', $this->id );
			do_action( 'bpcp_' . $this->id . '_activity_options' );
		}
	}

	/**
	 * Filter activity for the current user's posts only.
	 *
	 * @param string $query_string The current query string
	 * @param string $object The type of query (forums, in this case).
	 *
	 * @since 0.1
	 *
	 * @uses $bp
	 */
	function filter_activity( $query_string, $object ) {
		global $bp;

		if ( bp_is_my_profile() ) $id = bp_loggedin_user_id();
		else $id = $bp->loggedin_user->id;

		$user_posts = bpcp_get_user_post_ids( Array( 'userid' => $id, 'type' => $this->id ) );

		$primary_id = implode( ",", $user_posts );

		//Prevents all activity attributed to user from showing.
		if ( empty($primary_id) )
			$primary_id = -1;

		$query_string =  'primary_id=' . $primary_id . "&$query_string";
		return $query_string;
	}

	/**
	 * Used to generate the Query to be used for posts
	 *
	 * @todo Integrate with the main controller, if at all possible.
	 * Also used in the ajax calls.
	 *
	 * @param $defaults The default values that can be used to add to this query.
	 *                  same arguments as the_query expects.
	 */
	function cookie_query( $defaults = Array() ) {
		global $bp;

		$query = Array(
			'post_type' => $this->id,
			'nopaging' => true
		);
		$query = array_merge( $query, $defaults );

		if ( isset( $_POST[ 'filter' ] ) ) {
			switch( $_POST[ 'filter' ] ) {
				case 'active':
					$query['orderby'] = '_bpcp_last_activity';
					break;
				case 'newest':
					$query['orderby'] = 'date';
					break;
				case 'alphabetical':
					$query['orderby'] = 'title';
					$query['order'] = 'ASC';
					break;
			}
		}

		if ( isset( $_POST['scope'] ) ) {
			switch ( $_POST['scope'] ) {
				case 'all': break;
				case 'personal': 
					$query['author'] = bp_loggedin_user_id();
					break;
			}
		}
	
		
		$query = apply_filters( 'bpcp_' . $this->id . '_cookie_query', apply_filters( 'bpcp_cookie_query', $query ) );
		query_posts( $query );
	}

	/**
	 * Load the this type's loop in the ajax query.
	 *
	 * First looks for event/event-loop (or whatever your type is),
	 * followed by the generic type/type-loop.
	 *
	 * @since 0.1
	 */
	function _ajax_directory_work() {
		$this->cookie_query();
		bpcp_locate_template( Array( "$this->id/$this->id-loop.php", 'type/type-loop.php' ), true );
	}

	/**
	 * Enqueues the template to be loaded in an ajax request.
	 *
	 * The actual work is done after init so that the custom post type can
	 * be properly initiated.
	 *
	 * @uses _ajax_directory_work()
	 *
	 * @since 0.1
	 */
	function ajax_directory() {
		do_action( 'bpcp_ajax_directory', $this->id );
		do_action( 'bpcp_' . $this->id . '_ajax_directory' );
		add_action( 'init', Array( &$this, '_ajax_directory_work' ), 100 );
	}

	/**
	 * Set up the globals to be used for this component.
	 *
	 * Stores the object at $bp->[name], also adds settings to this
	 * global.
	 *
	 * @uses $bp
	 *
	 * @since 0.1
	 */
	function setup_globals() {
		global $bp;
		$bp->{$this->id}->id = $this->settings['id'];
		$bp->{$this->id}->slug = $this->settings['slug'];

		$bp->active_components[$bp->{$this->id}->slug] = $this->id;
	}

	/**
	 * The main controller based on URLs
	 *
	 * Generates the right query to be used for the given URL, and call 
	 * the right templates if the menu isn't already doing that.
	 *
	 * @uses $bp
	 * @uses $is_member_page
	 *
	 * @since 0.1
	 */
	function screen_controller() {
		global $bp, $is_member_page;

		//Are we in the current type
		if ( $bp->current_component == $bp->{$this->id}->slug ) {

			do_action( 'bpcp_controller' );
			do_action( 'bpcp_' . $this->id . '_controller' );

			// The directory. 
			if ( empty( $bp->current_action ) && empty( $bp->action_variables ) && !$is_member_page ) {

				do_action( 'bpcp_controller_directory', $this->id );
				do_action( 'bpcp_' . $this->id . '_controller_directory' );

				$bp->is_directory = true;
				//Create a query based on REQUEST parameters
				$this->cookie_query();

				//Load the directory template
				$this->bpcp_v->directory();
			
			//Member page
			} else if ( $is_member_page ) {

				//Add the template to the members page
				//@todo This can break if a front page is defined in the theme. Needs a better hook?
				add_action( 'bp_after_member_body', Array( &$this->bpcp_v, 'my_posts_inner' ) );

				//Query for the current user's posts.
				if ( empty( $bp->current_action ) || $bp->current_action == $this->settings['slugs']->my_posts ) {
					if ( bp_is_my_profile() )
						$this->cookie_query( Array(
							'author' => bp_loggedin_user_id()
						) ); 
					else
						$this->cookie_query( Array(
							'author' => $bp->displayed_user->id
						) );
				}

				do_action( 'bpcp_member_page', $this->id );
				do_action( 'bpcp_' . $this->id . '_member_page' );

			//Post creation page
			} else if ( $bp->current_action == 'create' ) {
				$type = get_post_type_object( $this->id );
				if ( current_user_can( $type->cap->publish_posts ) ) {
					//Save the post
					if ( isset( $bp->action_variables[0] ) && $bp->action_variables[0] == 'save' ) {
						$this->bpcp_m->save_post_data();
						bp_core_redirect(  get_bloginfo('url') . "/" . $bp->{$this->id}->slug . "/" );

						do_action( 'bpcp_save_post', $this->id );
						do_action( 'bpcp_' . $this->id . '_save_post' );
					//Display the front-end editor
					} else {
						$this->bpcp_v->create();
						do_action( 'bpcp_create_post', $this->id );
						do_action( 'bpcp_' . $this->id . '_create_post' );
					}
				} else {
					bp_core_add_message( __( 'You do not have enough permissions to create an event.', 'bpcp' ) );
					bp_core_redirect( get_bloginfo('url') . "/" . $bp->{$this->id}->slug . "/" );
				}

			//Home/Forum page for single post
			} else {
				$bp->is_single_item = true;
				
				//Query based on slug
				query_posts( Array(
					'post_type' => $this->id,
					'name' => $bp->current_action
				) );


				//First run of the loop -- will be reset so that the theme can call it again.
				if ( have_posts() ) {
					the_post(); 

					//Shift everything up a step
					$bp->current_item = $bp->current_action;
					$bp->current_action = isset( $bp->action_variables[0] ) ? $bp->action_variables[0] : $this->settings['slugs']->single_home;
					array_shift($bp->action_variables);
			
					//Some random stuff that groups did making forums group-exclusive
					if ( $bp->current_action == 'forum' ) {
						remove_filter( 'bbpress_init', 'groups_add_forum_privacy_sql' );

					//Editing a post
					} else if ( 'edit' == $bp->current_action ) {
						if( isset( $bp->action_variables[0]) && 'save' == $bp->action_variables[0] )  {
							$this->bpcp_m->save_post_data();
							bp_core_redirect( get_permalink() . 'edit/' );
						}
					}

					do_action( 'bpcp_controller_single_' . $bp->current_action, $this->id );
					do_action( 'bpcp_' . $this->id . '_controller_single_' . $bp->current_action );

					//Set the page title to have the post title
					$bp->bp_options_title = get_the_title();
				} else {
					//@ToDo Redirect to 404
				}
			}

		//Not my component, but the bit in activity
		} else if ( $bp->current_component == 'activity' && $bp->current_action == $bp->{$this->id}->slug && $is_member_page ) {
			//For activity feeds
			//@ToDo while this _is_ the standard implementation, why can't the feed be loaded as a template?
			if ( isset( $bp->action_variables[0] ) && $bp->action_variables[0] == 'feed' ) {
				$wp_query->is_404 = false;
				status_header( 200 );

				require( 'feeds/bp-activity-myposts-feed.php' );
				exit();
			}
			
		}

	}

	/**
	 * Add the type to the buddybar.
	 *
	 * @uses $bp
	 * @uses $post
	 * @uses $is_member_page
	 *
	 * @since 0.1
	 */
	function setup_navbar() {
		global $bp, $post, $is_member_page;

		$type = get_post_type_object( $this->id );
		$parent_url = $bp->loggedin_user->domain . $bp->{$this->id}->slug . '/';

		//The main navigation item
		bp_core_new_nav_item( Array( 
			'name' => $this->settings['labels']['name'],
			'slug' => $bp->{$this->id}->slug, 
			'position' => 100, 
			'item_css_id' => $bp->{$this->id}->id,
			'default_subnav_slug' => $this->settings['slugs']->my_posts,
			'screen_function' => Array( $this->bpcp_v, 'my_posts' )
		) );

		//My posts of this type
		bp_core_new_subnav_item( Array(
			'name' => preg_replace( '/\(%.*?\)/', '', $this->settings['labels']['my_posts'] ),
			'slug' => $this->settings['slugs']->my_posts,
			'parent_slug' => $bp->{$this->id}->slug,
			'parent_url' =>	$parent_url,
			'position' => 10,
			'user_has_access' => current_user_can( $type->cap->publish_posts ),
			'screen_function' => Array( $this->bpcp_v, 'my_posts' )
		) );

		//Add subnavs using these actions
		do_action( 'bpcp_general_bnav', $this->id );
		do_action( 'bpcp_' . $this->id . '_general_nav' );

		//A post page -- not a general page
		if ( $bp->{$this->id}->slug == $bp->current_component && isset( $post ) ) {

				//Clean up subnav
				bp_core_reset_subnav_items( $this->settings['slug'] );

				//Default to home page of the current post -- displays contents of post
				bp_core_new_nav_default( Array(
					'parent_slug' => $this->settings['slug'],
					'screen_function' => Array( &$this->bpcp_v, 'single_home' ),
					'subnav_slug' => $this->settings['slugs']->single_home
				) );

				//Add the home page subnav item
				bp_core_new_subnav_item( Array(
					'name' => $this->settings['labels']['home'],
					'slug' => $this->settings['slugs']->single_home,
					'parent_slug' => $bp->{$this->id}->slug,
					'position' => 10,
					'screen_function' => Array( &$this->bpcp_v, 'single_home' ),
					'parent_url' => get_permalink()
				) );

				//Activity for this event
				bp_core_new_subnav_item( Array(
					'name' => $this->settings['labels']['activity'],
					'slug' => $this->settings['slugs']->single_activity,
					'parent_slug' => $bp->{$this->id}->slug,
					'position' => 10,
					'screen_function' => Array( &$this->bpcp_v, 'single_activity' ),
					'parent_url' => get_permalink()
				) );

				//Edit this post
				bp_core_new_subnav_item( Array(
					'name' => $this->settings['labels']['edit'],
					'slug' => $this->settings['slugs']->single_edit,
					'parent_slug' => $bp->{$this->id}->slug,
					'position' => 20,
					'screen_function' => Array( &$this->bpcp_v, 'single_edit' ),
					'parent_url' => get_permalink(),
					'user_has_access' => current_user_can( $type->cap->edit_post, $post->ID )
				) );

				//If forums exist, and are enabled
				if ( $this->settings['forum'] && function_exists( 'bp_forums_is_installed_correctly' ) && bp_forums_is_installed_correctly() && get_post_meta( $post->ID, '_bpcp_forum_id', true ) )
					bp_core_new_subnav_item( Array( 
						'name' => __( 'Forum', 'bpcp' ), 
						'slug' => 'forum', 
						'parent_url' => get_permalink(),
						'parent_slug' => $bp->{$this->id}->slug, 
						'screen_function' => Array( &$this, 'forum_controller' ),
						'position' => 30, 
						'item_css_id' => 'forums' 
					) );

				//Use these actions to add to the single sub nav menu
				do_action( 'bpcp_single_subnav', $this->id );
				do_action( 'bpcp_' . $this->id . '_single_subnav' );
		}

		//Add this type to the activity page
		$user_domain = ( !empty( $bp->displayed_user->domain ) ) ? $bp->displayed_user->domain : $bp->loggedin_user->domain;
		bp_core_new_subnav_item( Array(
				'name' => $this->settings['labels']['name'],
				'slug' => $this->settings['slug'],
				'parent_slug' => BP_ACTIVITY_SLUG,
				'position' => 30,
				'screen_function' => Array( &$this->bpcp_v, 'mytype_activity_page' ),
				'parent_url' => $user_domain . $bp->activity->slug . '/'
		) );
	}

	/**
	 * Adds this post type as a root component.
	 *
	 * @since 0.1
	 */
	function setup_root_component() {
		bp_core_add_root_component( $this->settings['slug'] );
	}

	/**
	 * Hook into BuddyPress's load template.
	 *
	 * Adds the custom theme directories in -- the one you
	 * provide, followed by the all-purpose defaults provided
	 * with the plugin.
	 *
	 * @param $found_template The current template
	 * @param $templates Searching for this.
	 *
	 * @uses $bp
	 *
	 * @since 0.1
	 */
	function load_template( $found_template, $templates ) {
		global $bp, $bp_path;

		if ( $bp->current_component != $bp->{$this->id}->slug ) 
			return $found_template;

		foreach ( (array) $templates as $template ) {
			if ( file_exists( STYLESHEETPATH . '/' . $template ) )
				$filtered_templates[] = STYLESHEETPATH . '/' . $template;
			else if ( file_exists( $this->settings['theme_dir'] . '/' . $template ) )
				$filtered_templates[] = $this->settings['theme_dir'] . '/' . $template;
			else if ( file_exists( BPCP_THEMES_DIR . '/' . $template ) )
				$filtered_templates[] = BPCP_THEMES_DIR . '/' . $template;
		}

		$found_template = $filtered_templates[0];

		return $found_template;
	}

	/**
	 * Override the default wordpress permalinks to instead display
	 * types/post-slug instead. The original look of a post can still
	 * be accessed using the standard URLs.
	 *
	 * @param $permalink The generated permalink
	 * @postid The current post id
	 *
	 * @since 0.1
	 */
	function special_permalinks( $permalink, $postid, $leave, $sample ) {
		$post = get_post( $postid );
		$type = $post->post_type;

		if ( $type == $this->id ) 
			$permalink = bp_get_root_domain() . '/' . $this->settings['slug'] . '/' . $post->post_name . '/';

		return apply_filters( 'bpcp_' . $this->id . '_permalink', apply_filters( 'bpcp_permalink', $permalink, $this->id ) );
	}

	/**
	 * Get template tags for bp.
	 *
	 * @since 0.1
	 */
	function load_template_tags() {
		require 'themes/tags.php';
	}

	/**
	 * Recursively merge objects.
	 *
	 * Internal function used to merge objects -- used to
	 * add the custom settings and labels to the global
	 * post type object.
	 *
	 * @since 0.1
	 */
	function _merge_objects($a, $b) {
		$a = (object) $a; $b = (object) $b;

		foreach( (array)$a as $key => $value ) {
			if ( isset( $b->{$key} ) && ( is_object( $value ) || is_array( $value ) )  ) {
				if ( is_array( $b->{$key} ) || is_object( $b->{$key} ) ) {
					$a->{$key} = $this->_merge_objects( $value, $b->{$key} );
				} else {
					$value[] = $b->{$key};
					$a->{$key} = $value;
				}
			}
		}

		return (object) array_merge( (array) $b, (array) $a );
	}

	/**
	 * As, funnily enough, the extension of the custom post type 
	 * is carried out before the actual type is registered.
	 *
	 * Merges the buddypress settings with the post type object. 
	 * In this case, the egg did come before the chicken,
	 * whatever science might say.
	 *
	 * @since 0.1
	 */
	function modify_custom_type() {
		global $wp_post_types;

		if ( array_key_exists( $this->id, $wp_post_types ) ) {
			$wp_post_types[ $this->id ] = $this->_merge_objects( $wp_post_types[ $this->id ], $this->settings );
		} else return false;
	}

	/**
	 * Called whenever a new post is created and an 
	 * activity post has to be generated.
	 *
	 * Modify the content by using the filter bpcp_create_<type>_activity
	 * which is passed the post id.
	 *
	 * @param $new_status The new status of the post
	 * @param $old_status The previous status of the post
	 * @param $postid Which post
	 *
	 * @since 0.1
	 */
	function create_post_activity( $new_status, $old_status, $postid ) {
		$post = &get_post( $postid );

		if ( $new_status != $old_status && $new_status == 'publish' && $post->post_type == $this->id  ) {

			$object = get_post_type_object( $post->post_type );

			$args = Array( 
				'action' => sprintf( __( "%s created the %s <a href = '%s'>%s</a>." ), bp_core_get_userlink( $post->post_author ), $object->labels->singular_name, get_permalink( $post->ID ), $post->post_title ),
				'content' => '',
				'type' => 'new_' . $object->id
			);

			$args = apply_filters( 'bpcp_create_' . $object->id . '_activity', apply_filters( 'bpcp_create_activity', $args, $post->ID ), $post->ID );	
			do_action( 'bpcp_create_activity', $args );

			bp_activity_add( Array(
				'action' => $args['action'],
				'component' => $this->id,
				'content' => $args['content'],
				'type' => $args['type'],
				'user_id' => $post->post_author,
				'item_id' => $post->ID
			) );
		} else return false;
	}

	/**
	 * Modifies the '_bpcp_last_activity' post meta whenever
	 * an activity is created for a given post.
	 *
	 * Hooked into bp's add_activity function, to avoid havin
	 * to write a wrapper around adding activities.
	 *
	 * @uses $bp
	 *
	 * @param $args
	 *
	 * @since 0.1
	 */
	function update_last_activity( $args ) {
		global $bp;

		if ( $this->id == $args['component'] ) {
			if ( array_key_exists( 'item_id', $args ) ) {
				update_post_meta( $args['item_id'], '_bpcp_last_activity', $args['recorded_time'] );
				do_action( 'bpcp_update_last_activity', $this->id, $args );
				do_action( 'bpcp_' . $this->id . '_update_last_activity', $args );
			}
		}
	}

	/**
	 * Called whenever a new post is created and an 
	 * activity post has to be generated.
	 *
	 * Modify the content by using the filter bpcp_create_<type>_activity
	 * which is passed the post id.
	 *
	 * @param $postid Which post
	 * @param $post Post object
	 *
	 * @since 0.1
	 */
	function edit_post_activity( $postid, $post ) {
		if ( $post->post_status == 'publish' && $post->post_type == $this->id && did_action( 'bpcp_create_activity' ) <= 0 ) {
			$object = get_post_type_object( $post->post_type );

			$args = Array( 
				'action' => sprintf( __( "%s updated the %s <a href = '%s'>%s</a>." ), bp_core_get_userlink( $post->post_author ), $object->labels->singular_name, get_permalink( $post->ID ), get_the_title( $post->ID ) ),
				'content' => '',
				'type' => 'activity_update'
			);

			$args = apply_filters( 'bpcp_edit_' . $object->id . '_activity', apply_filters( 'bpcp_edit_activity', $args, $post->ID ) );

			bp_activity_add( Array(
				'action' => $args['action'],
				'component' => $this->id,
				'content' => $args['content'],
				'type' => $args['type'],
				'user_id' => $post->post_author,
				'item_id' => $post->ID
			) );
		} else return false;
	}

	/**
	 * Controller For forums
	 *
	 * Shamelessly copied and modified from the groups forum calls.
	 *
	 * @uses $bp
	 * @uses $post
	 *
	 * @since 0.1
	 */
	function forum_controller() {
		global $bp, $post;

		if ( $bp->current_action == 'forum' ) {

		/* Fetch the details we need */
		$topic_slug = $bp->action_variables[1];
		$topic_id = bp_forums_get_topic_id_from_slug( $topic_slug );
		$forum_id = get_post_meta( $post->ID, '_bpcp_forum_id', true );

		if ( $topic_slug && $topic_id ) {

			/* Posting a reply */
			if ( !$bp->action_variables[2] && isset( $_POST['submit_reply'] ) ) {
				/* Check the nonce */
				check_admin_referer( 'bp_forums_new_reply' );

				if ( !$post_id = $this->bpcp_m->new_forum_post( $_POST['reply_text'], $topic_id, $_GET['topic_page'] ) )
					bp_core_add_message( __( 'There was an error when replying to that topic', 'buddypress'), 'error' );
				else
					bp_core_add_message( __( 'Your reply was posted successfully', 'buddypress') );

				if ( $_SERVER['QUERY_STRING'] )
					$query_vars = '?' . $_SERVER['QUERY_STRING'];

				bp_core_redirect( get_permalink() . 'forum/topic/' . $topic_slug . '/' . $query_vars . '#post-' . $post_id );
			}

			/* Sticky a topic */
			else if ( 'stick' == $bp->action_variables[2] && ( $bp->is_item_admin || $bp->is_item_mod ) ) {
				/* Check the nonce */
				check_admin_referer( 'bp_forums_stick_topic' );

				if ( !bp_forums_sticky_topic( array( 'topic_id' => $topic_id ) ) )
					bp_core_add_message( __( 'There was an error when making that topic a sticky', 'buddypress' ), 'error' );
				else
					bp_core_add_message( __( 'The topic was made sticky successfully', 'buddypress' ) );

				bp_core_redirect( wp_get_referer() );
			}

			/* Un-Sticky a topic */
			else if ( 'unstick' == $bp->action_variables[2] && ( $bp->is_item_admin || $bp->is_item_mod ) ) {
				/* Check the nonce */
				check_admin_referer( 'bp_forums_unstick_topic' );

				if ( !bp_forums_sticky_topic( array( 'topic_id' => $topic_id, 'mode' => 'unstick' ) ) )
					bp_core_add_message( __( 'There was an error when unsticking that topic', 'buddypress'), 'error' );
				else
					bp_core_add_message( __( 'The topic was unstuck successfully', 'buddypress') );

				bp_core_redirect( wp_get_referer() );
			}

			/* Close a topic */
			else if ( 'close' == $bp->action_variables[2] && ( $bp->is_item_admin || $bp->is_item_mod ) ) {
				/* Check the nonce */
				check_admin_referer( 'bp_forums_close_topic' );

				if ( !bp_forums_openclose_topic( array( 'topic_id' => $topic_id ) ) )
					bp_core_add_message( __( 'There was an error when closing that topic', 'buddypress'), 'error' );
				else
					bp_core_add_message( __( 'The topic was closed successfully', 'buddypress') );

				bp_core_redirect( wp_get_referer() );
			}

			/* Open a topic */
			else if ( 'open' == $bp->action_variables[2] && ( $bp->is_item_admin || $bp->is_item_mod ) ) {
				/* Check the nonce */
				check_admin_referer( 'bp_forums_open_topic' );

				if ( !bp_forums_openclose_topic( array( 'topic_id' => $topic_id, 'mode' => 'open' ) ) )
					bp_core_add_message( __( 'There was an error when opening that topic', 'buddypress'), 'error' );
				else
					bp_core_add_message( __( 'The topic was opened successfully', 'buddypress') );

				bp_core_redirect( wp_get_referer() );
			}

			/* Delete a topic */
			else if ( 'delete' == $bp->action_variables[2] && empty( $bp->action_variables[3] ) ) {
				/* Fetch the topic */
				$topic = bp_forums_get_topic_details( $topic_id );

				/* Check the logged in user can delete this topic */
				if ( !current_user_can( 'edit_post' ) && (int)$bp->loggedin_user->id != (int)$topic->topic_poster )
					bp_core_redirect( wp_get_referer() );

				/* Check the nonce */
				check_admin_referer( 'bp_forums_delete_topic' );

				if ( !$this->bpcp_m->delete_forum_topic( $topic_id ) )
					bp_core_add_message( __( 'There was an error deleting the topic', 'buddypress'), 'error' );
				else
					bp_core_add_message( __( 'The topic was deleted successfully', 'buddypress') );

				bp_core_redirect( wp_get_referer() );
			}

			/* Editing a topic */
			else if ( 'edit' == $bp->action_variables[2] && empty( $bp->action_variables[3] ) ) {
				/* Fetch the topic */
				$topic = bp_forums_get_topic_details( $topic_id );

				/* Check the logged in user can edit this topic */
				if ( !$bp->is_item_admin && !$bp->is_item_mod && (int)$bp->loggedin_user->id != (int)$topic->topic_poster )
					bp_core_redirect( wp_get_referer() );

				if ( isset( $_POST['save_changes'] ) ) {
					/* Check the nonce */
					check_admin_referer( 'bp_forums_edit_topic' );

					if ( !$this->bpcp_m->update_forum_topic( $topic_id, $_POST['topic_title'], $_POST['topic_text'] ) )
						bp_core_add_message( __( 'There was an error when editing that topic', 'buddypress'), 'error' );
					else
						bp_core_add_message( __( 'The topic was edited successfully', 'buddypress') );

					bp_core_redirect( get_permalink() . 'forum/topic/' . $topic_slug . '/' );
				}

				$this->bpcp_v->forum();
			}

			/* Delete a post */
			else if ( 'delete' == $bp->action_variables[2] && $post_id = $bp->action_variables[4] ) {
				/* Fetch the post */
				$bbpost = bp_forums_get_post( $post_id );

				/* Check the logged in user can edit this topic */
				if ( !$bp->is_item_admin && !$bp->is_item_mod && (int)$bp->loggedin_user->id != (int)$bbpost->poster_id )
					bp_core_redirect( wp_get_referer() );

				/* Check the nonce */
				check_admin_referer( 'bp_forums_delete_post' );

				if ( !$this->bpcp_m->delete_forum_post( $bp->action_variables[4], $topic_id ) )
					bp_core_add_message( __( 'There was an error deleting that post', 'buddypress'), 'error' );
				else
					bp_core_add_message( __( 'The post was deleted successfully', 'buddypress') );

				bp_core_redirect( wp_get_referer() );
			}

			/* Editing a post */
			else if ( 'edit' == $bp->action_variables[2] && $post_id = $bp->action_variables[4] ) {
				/* Fetch the post */
				$bbpost = bp_forums_get_post( $bp->action_variables[4] );

				/* Check the logged in user can edit this topic */
				if ( !$bp->is_item_admin && !$bp->is_item_mod && (int)$bp->loggedin_user->id != (int)$bbpost->poster_id )
					bp_core_redirect( wp_get_referer() );

				if ( isset( $_POST['save_changes'] ) ) {
					/* Check the nonce */
					check_admin_referer( 'bp_forums_edit_post' );

					if ( !$post_id = $this->bpcp_m->update_forum_post( $post_id, $_POST['post_text'], $topic_id, $_GET['topic_page'] ) )
						bp_core_add_message( __( 'There was an error when editing that post', 'buddypress'), 'error' );
					else
						bp_core_add_message( __( 'The post was edited successfully', 'buddypress') );

					if ( $_SERVER['QUERY_STRING'] )
						$query_vars = '?' . $_SERVER['QUERY_STRING'];

					bp_core_redirect( get_permalink() . 'forum/topic/' . $topic_slug . '/' . $query_vars . '#post-' . $post_id );
				}

				$this->bpcp_v->forum();
			}

			/* Standard topic display */
			else {
				$this->bpcp_v->forum();
			}

		} else {
			/* Posting a topic */
			if ( isset( $_POST['submit_topic'] ) && function_exists( 'bp_forums_new_topic') ) {
				/* Check the nonce */
				check_admin_referer( 'bp_forums_new_topic' );

				if ( !( $topic = $this->bpcp_m->new_forum_topic( $_POST['topic_title'], $_POST['topic_text'], $_POST['topic_tags'], $forum_id ) ) )
					bp_core_add_message( __( 'There was an error when creating the topic', 'buddypress'), 'error' );
				else
					bp_core_add_message( __( 'The topic was created successfully', 'buddypress') );

				bp_core_redirect( get_permalink() . 'forum/topic/' . $topic->topic_slug . '/' );
			}

			$this->bpcp_v->forum();
		}

		}
	}

}

/** 
 * Wrapper function for post types. See the constructor for the class.
 *
 * @since 0.1
 */
function bpcp_register_post_type( $args ) {
	return new bpcp( $args );
}

//Set keywords to filter out errors for kb_debug to display
//global $kb_display_keywords;
//$kb_display_keywords[] = '/bp_custom_posts/';

/** 
 * Returns .dev when SCRIPT_DEBUG is enabled.
 */
if( !function_exists( 'kb_ext' ) ) {
	function kb_ext() {
		/* TODO Make this function behave as described once minification has been completed. */
		return ".dev";

	/*
		if( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG )
			return ".dev";

		return "";
	*/
	}
}
