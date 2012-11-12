<?php
//
//  class-ai1ec-themes-list-table.php
//  all-in-one-event-calendar
//
//  Created by The Seed Studio on 2012-04-05.
//

class Ai1ec_Themes_List_Table extends WP_List_Table {

	/**
	 * wp_theme_directories class variable
	 *
	 * @var string
	 **/
	var $wp_theme_directories;

	/**
	 * _wp_theme_directories class variable
	 *
	 * @var string
	 **/
	var $_wp_theme_directories;

	/**
	 * wp_broken_themes class variable
	 *
	 * @var string
	 **/
	var $wp_broken_themes;

	/**
	 * _wp_broken_themes class variable
	 *
	 * @var string
	 **/
	var $_wp_broken_themes;

	/**
	 * search class variable
	 *
	 * Holds search terms
	 *
	 * @var array
	 **/
	var $search = array();

	/**
	 * features class variable
	 *
	 * @var array
	 **/
	var $features = array();

	/**
	 * prepare_items function
	 *
	 * Prepares themes for display, applies search filters if available
	 *
	 * @return void
	 **/
	public function prepare_items() {
		global $ct, $wp_theme_directories, $wp_broken_themes;

		// setting wp_themes to null in case
		// other plugins have changed its value
		unset( $GLOBALS["wp_themes"] );

		// preserve global values
		$this->_wp_theme_directories = $wp_theme_directories;
		$this->_wp_broken_themes     = $wp_broken_themes;

		// assign new values to the global vars
		$this->wp_theme_directories = $wp_theme_directories = array( AI1EC_THEMES_ROOT );
		$this->wp_broken_themes     = $wp_broken_themes     = array();

		// get available themes
		$ct     = $this->current_theme_info();

		// get allowed themes (checks to see if a themes has all necessary files - style.css and index.php)
		$themes = get_themes();

		// handles theme searching by keyword
		if ( ! empty( $_REQUEST['s'] ) ) {
			$search = strtolower( stripslashes( $_REQUEST['s'] ) );
			$this->search = array_merge( $this->search, array_filter( array_map( 'trim', explode( ',', $search ) ) ) );
			$this->search = array_unique( $this->search );
		}

		// handles theme search by features (tags, one column, widget etc)
		if ( !empty( $_REQUEST['features'] ) ) {
			$this->features = $_REQUEST['features'];
			$this->features = array_map( 'trim', $this->features );
			$this->features = array_map( 'sanitize_title_with_dashes', $this->features );
			$this->features = array_unique( $this->features );
		}

		// applies search and features terms from above to available themes
		// and remove themes that do not match the applied filters/keywords
		if ( $this->search || $this->features ) {
			foreach ( $themes as $key => $theme ) {
				if ( !$this->search_theme( $theme ) )
					unset( $themes[ $key ] );
			}
		}

		if( isset( $ct->name ) && isset( $themes[$ct->name] ) ) {
			unset( $themes[$ct->name] );
		}

		// sort themes using strnatcasecmp function
		uksort( $themes, "strnatcasecmp" );

		// themes per page
		$per_page = 24;

		// get current page
		$page = $this->get_pagenum();
		$start = ( $page - 1 ) * $per_page;

		$this->items = array_slice( $themes, $start, $per_page );

		// set total themes and themes per page
		$this->set_pagination_args( array(
			'total_items' => count( $themes ),
			'per_page'    => $per_page,
		) );

		// assign back original values to the global vars
		$wp_theme_directories = $this->_wp_theme_directories;
		$wp_broken_themes     = $this->_wp_broken_themes;
	}

	/**
	 * display function
	 *
	 * Returns html display of themes table
	 *
	 * @return string
	 **/
	public function display() {
		$this->switch_theme_folders_start();
		$this->tablenav( 'top' );

		echo '<div id="availablethemes">' . $this->display_rows_or_placeholder() . '</div>';

		$this->tablenav( 'bottom' );
		$this->switch_theme_folders_end();
	}

	/**
	 * tablenav function
	 *
	 * @return void
	 **/
	public function tablenav( $which = 'top' ) {
		$this->switch_theme_folders_start();
		if ( $this->get_pagination_arg( 'total_pages' ) <= 1 )
			return;
		?>
		<div class="tablenav themes <?php echo $which; ?>">
			<?php $this->pagination( $which ); ?>
		   <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-loading list-ajax-loading" alt="" />
		  <br class="clear" />
		</div>
		<?php
		$this->switch_theme_folders_end();
	}

	/**
	 * ajax_user_can function
	 *
	 * @return bool
	 **/
	public function ajax_user_can() {
		// Do not check edit_theme_options here. AJAX calls for available themes require switch_themes.
		return current_user_can('switch_themes');
	}

	/**
	 * no_items function
	 *
	 * @return void
	 **/
	public function no_items() {
		$this->switch_theme_folders_start();
		if ( $this->search || $this->features ) {
			_e( 'No themes found.', AI1EC_PLUGIN_NAME );
			return;
		}

		if ( is_multisite() ) {
			if ( current_user_can( 'install_themes' ) && current_user_can( 'manage_network_themes' ) ) {
				printf( __( 'You only have one theme enabled for this site right now. Visit the Network Admin to <a href="%1$s">enable</a> or <a href="%2$s">install</a> more themes.', AI1EC_PLUGIN_NAME ),
                network_admin_url( 'site-themes.php?id=' . $GLOBALS['blog_id'] ),
                network_admin_url( 'theme-install.php' ) );

				return;
			} elseif ( current_user_can( 'manage_network_themes' ) ) {
				printf( __( 'You only have one theme enabled for this site right now. Visit the Network Admin to <a href="%1$s">enable</a> more themes.', AI1EC_PLUGIN_NAME ),
                network_admin_url( 'site-themes.php?id=' . $GLOBALS['blog_id'] ) );

				return;
			}
			// else, fallthrough. install_themes doesn't help if you can't enable it.
		} else {
			if ( current_user_can( 'install_themes' ) ) {
				printf( __( 'You only have one theme installed right now. You can choose from many free themes in the Timely Theme Directory at any time: ' .
                    'just click on the <a href="%s">Install Themes</a> tab above.', AI1EC_PLUGIN_NAME ),
                admin_url( AI1EC_THEME_SELECTION_BASE_URL ) );

				return;
			}
		}
		// Fallthrough.
		printf( __( 'Only the active theme is available to you. Contact the <em>%s</em> administrator to add more themes.', AI1EC_PLUGIN_NAME ),
            get_site_option( 'site_name' ) );
		$this->switch_theme_folders_end();
	}

	/**
	 * get_columns function
	 *
	 * @return array
	 **/
	public function get_columns() {
		return array();
	}

	/**
	 * display_rows function
	 *
	 * @return void
	 **/
	function display_rows() {
		$this->switch_theme_folders_start();
		$themes = $this->items;
		$theme_names = array_keys( $themes );
		natcasesort( $theme_names );

		foreach ( $theme_names as $theme_name ) {
			$class = array( 'available-theme' );
			?>
			<div class="<?php echo join( ' ', $class ); ?>">
			<?php
			if ( !empty( $theme_name ) ) :
				$template       = $themes[$theme_name]['Template'];
				$stylesheet     = $themes[$theme_name]['Stylesheet'];
				$title          = $themes[$theme_name]['Title'];
				$version        = $themes[$theme_name]['Version'];
				$description    = $themes[$theme_name]['Description'];
				$author         = $themes[$theme_name]['Author'];
				$screenshot     = $themes[$theme_name]['Screenshot'];
				$stylesheet_dir = $themes[$theme_name]['Stylesheet Dir'];
				$template_dir   = $themes[$theme_name]['Template Dir'];
				$parent_theme   = $themes[$theme_name]['Parent Theme'];
				$theme_root     = $themes[$theme_name]['Theme Root'];
				$theme_root_uri = $themes[$theme_name]['Theme Root URI'];
				$preview_link   = esc_url( get_option( 'home' ) . '/' );

				if ( is_ssl() )
					$preview_link = str_replace( 'http://', 'https://', $preview_link );

				$preview_link = htmlspecialchars(
					add_query_arg(
						array(
							'preview'          => 1,
							'ai1ec_template'   => $template,
							'ai1ec_stylesheet' => $stylesheet,
							'preview_iframe'   => true,
							'TB_iframe'        => 'true'
						),
						$preview_link
					)
				);

				$preview_text   = esc_attr( sprintf( __( 'Preview of &#8220;%s&#8221;', AI1EC_PLUGIN_NAME ), $title ) );
				$tags           = $themes[$theme_name]['Tags'];
				$thickbox_class = 'thickbox thickbox-preview';
				$activate_link  = wp_nonce_url(
					admin_url( AI1EC_THEME_SELECTION_BASE_URL ) .
					"&amp;action=activate&amp;ai1ec_template=" .
					urlencode( $template ) .
					"&amp;ai1ec_stylesheet=" .
					urlencode( $stylesheet ),
					'switch-ai1ec_theme_' . $template
				);
				$activate_text  = esc_attr( sprintf( __( 'Activate &#8220;%s&#8221;', AI1EC_PLUGIN_NAME ), $title ) );
				$actions        = array();
				$actions[]      = '<a href="' . $activate_link .  '" class="activatelink" title="' . $activate_text . '">' .
				                  __( 'Activate', AI1EC_PLUGIN_NAME ) . '</a>';
				$actions[]      = '<a href="' . $preview_link . '" class="thickbox thickbox-preview" title="' .
				                  esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', AI1EC_PLUGIN_NAME ), $theme_name ) ) . '">' .
				                  __( 'Preview', AI1EC_PLUGIN_NAME ) . '</a>';

				if( ! is_multisite() && current_user_can( 'delete_themes' ) ) {
					$delete_link = wp_nonce_url(
						admin_url( AI1EC_THEME_SELECTION_BASE_URL ) .
						"&amp;action=delete&amp;ai1ec_template=$stylesheet", 'delete-ai1ec_theme_' . $stylesheet
					);
					$actions[] = '<a class="submitdelete deletion" href="' .
					             $delete_link .
					             '" onclick="' . "return confirm( '" .
					             esc_js( sprintf(
						             __( "You are about to delete this theme '%s'\n  'Cancel' to stop, 'OK' to delete.", AI1EC_PLUGIN_NAME ),
						             $theme_name
					             ) ) .
					             "' );" . '">' . __( 'Delete', AI1EC_PLUGIN_NAME ) . '</a>';
				}

				$actions = apply_filters( 'theme_action_links', $actions, $themes[$theme_name] );

				$actions = implode ( ' | ', $actions );
			?>
				<a href="<?php echo $preview_link; ?>" class="<?php echo $thickbox_class; ?> screenshot">
				<?php if ( $screenshot ) : ?>
					<img src="<?php echo $theme_root_uri . '/' . $stylesheet . '/' . $screenshot; ?>" alt="" />
				<?php endif; ?>
				</a>
				<h3>
			<?php
				/* translators: 1: theme title, 2: theme version, 3: theme author */
				printf( __( '%1$s %2$s by %3$s', AI1EC_PLUGIN_NAME ), $title, $version, $author ) ; ?></h3>
				<p class="description"><?php echo $description; ?></p>
				<span class='action-links'><?php echo $actions ?></span>
				<?php if ( current_user_can( 'edit_themes' ) && $parent_theme ) {
					/* translators: 1: theme title, 2:  template dir, 3: stylesheet_dir, 4: theme title, 5: parent_theme */ ?>
					<p>
						<?php
						printf(
							__( 'The template files are located in <code>%2$s</code>. The stylesheet files are located in <code>%3$s</code>. ' .
							    '<strong>%4$s</strong> uses templates from <strong>%5$s</strong>. Changes made to the templates will affect ' .
							    'both themes.', AI1EC_PLUGIN_NAME
							),
							$title,
							str_replace( WP_CONTENT_DIR, '', $template_dir ),
							str_replace( WP_CONTENT_DIR, '', $stylesheet_dir ),
							$title,
							$parent_theme );
						?>
					</p>
			<?php } else { ?>
				<p>
					<?php
					printf(
						__( 'All of this theme&#8217;s files are located in <code>%2$s</code>.', AI1EC_PLUGIN_NAME ),
						$title,
						str_replace( WP_CONTENT_DIR, '', $template_dir ),
						str_replace( WP_CONTENT_DIR, '', $stylesheet_dir )
					);
					?>
				</p>
			<?php } ?>
			<?php if ( $tags ) : ?>
				<p>
					<?php _e( 'Tags:', AI1EC_PLUGIN_NAME ); ?> <?php echo join( ', ', $tags ); ?>
				</p>
			<?php endif; ?>
			<?php theme_update_available( $themes[$theme_name] ); ?>
		<?php endif; // end if not empty theme_name ?>
			</div>
		<?php
		} // end foreach $theme_names
		$this->switch_theme_folders_end();
	}

	/**
	 * search_theme function
	 *
	 * @return void
	 **/
	function search_theme( $theme ) {
		$matched = 0;

		// Match all phrases
		if ( count( $this->search ) > 0 ) {
			foreach ( $this->search as $word ) {
				$matched = 0;

				// In a tag?
				if ( in_array( $word, array_map( 'sanitize_title_with_dashes', $theme['Tags'] ) ) )
					$matched = 1;

				// In one of the fields?
				foreach ( array( 'Name', 'Title', 'Description', 'Author', 'Template', 'Stylesheet' ) AS $field ) {
					if ( stripos( $theme[$field], $word ) !== false )
						$matched++;
				}

				if ( $matched == 0 )
					return false;
			}
		}

		// Now search the features
		if ( count( $this->features ) > 0 ) {
			foreach ( $this->features as $word ) {
				// In a tag?
				if ( !in_array( $word, array_map( 'sanitize_title_with_dashes', $theme['Tags'] ) ) )
					return false;
			}
		}

		// Only get here if each word exists in the tags or one of the fields
		return true;
	}

	/**
	 * {@internal Missing Short Description}}
	 *
	 * @since 2.0.0
	 *
	 * @return unknown
	 */
	function current_theme_info() {
		$this->switch_theme_folders_start();
		$themes = get_themes();
		$current_theme = self::get_current_ai1ec_theme();

		if ( ! $themes ) {
			$ct = new stdClass;
			$ct->name = $current_theme;
			return $ct;
		}

		if ( ! isset( $themes[$current_theme] ) ) {
			delete_option( 'ai1ec_current_theme' );
			$current_theme = self::get_current_ai1ec_theme();
		}

		$ct = new stdClass;
		$ct->name = $current_theme;
		$ct->title = $themes[$current_theme]['Title'];
		$ct->version = $themes[$current_theme]['Version'];
		$ct->parent_theme = $themes[$current_theme]['Parent Theme'];
		$ct->template_dir = $themes[$current_theme]['Template Dir'];
		$ct->stylesheet_dir = $themes[$current_theme]['Stylesheet Dir'];
		$ct->template = $themes[$current_theme]['Template'];
		$ct->stylesheet = $themes[$current_theme]['Stylesheet'];
		$ct->screenshot = $themes[$current_theme]['Screenshot'];
		$ct->description = $themes[$current_theme]['Description'];
		$ct->author = $themes[$current_theme]['Author'];
		$ct->tags = $themes[$current_theme]['Tags'];
		$ct->theme_root = $themes[$current_theme]['Theme Root'];
		$ct->theme_root_uri = $themes[$current_theme]['Theme Root URI'];
		$this->switch_theme_folders_end();
		return $ct;
	}
	/**
	 * Retrieve current theme display name.
	 *
	 * If the 'current_theme' option has already been set, then it will be returned
	 * instead. If it is not set, then each theme will be iterated over until both
	 * the current stylesheet and current template name.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	static function get_current_ai1ec_theme() {
		if( $theme = get_option('ai1ec_current_theme') )
			return $theme;

		$themes = get_themes();
		$current_theme = 'Vortex';

		if ( $themes ) {
			$theme_names = array_keys( $themes );
			$current_template   = get_option( 'ai1ec_template' );
			$current_stylesheet = get_option( 'ai1ec_stylesheet' );

			foreach ( (array) $theme_names as $theme_name ) {
				if ( $themes[$theme_name]['Stylesheet'] == $current_stylesheet &&
						$themes[$theme_name]['Template'] == $current_template ) {
					$current_theme = $themes[$theme_name]['Name'];
					break;
				}
			}
		}

		update_option( 'ai1ec_current_theme', $current_theme );
		return $current_theme;
	}
	/**
	 * Retrieve list of WordPress theme features (aka theme tags)
	 *
	 * @since 3.1.0
	 *
	 * @return array  Array of features keyed by category with translations keyed by slug.
	 */
	static function get_theme_feature_list() {
		// Hard-coded list is used if api not accessible.
		$features = array(
				__('Colors') => array(
					'black'   => __( 'Black' ),
					'blue'    => __( 'Blue' ),
					'brown'   => __( 'Brown' ),
					'gray'    => __( 'Gray' ),
					'green'   => __( 'Green' ),
					'orange'  => __( 'Orange' ),
					'pink'    => __( 'Pink' ),
					'purple'  => __( 'Purple' ),
					'red'     => __( 'Red' ),
					'silver'  => __( 'Silver' ),
					'tan'     => __( 'Tan' ),
					'white'   => __( 'White' ),
					'yellow'  => __( 'Yellow' ),
					'dark'    => __( 'Dark' ),
					'light'   => __( 'Light ')
				),

			__('Width') => array(
				'fixed-width'    => __( 'Fixed Width' ),
				'flexible-width' => __( 'Flexible Width' )
			),

			__( 'Features' ) => array(
				'featured-images'       => __( 'Featured Images' ),
				'front-page-post-form'  => __( 'Front Page Posting' ),
				'full-width-template'   => __( 'Full Width Template' ),
				'rtl-language-support'  => __( 'RTL Language Support' ),
				'threaded-comments'     => __( 'Threaded Comments' ),
				'translation-ready'     => __( 'Translation Ready' )
			),

			__( 'Subject' )  => array(
				'holiday'       => __( 'Holiday' ),
				'photoblogging' => __( 'Photoblogging' ),
				'seasonal'      => __( 'Seasonal' )
			)
		);

		return $features;
	}

	/**
	 * switch_theme_folders_start function
	 *
	 * @return void
	 **/
	public function switch_theme_folders_start() {
		global $wp_theme_directories, $wp_broken_themes;

		// preserve global values
		$this->_wp_theme_directories = $wp_theme_directories;
		$this->_wp_broken_themes     = $wp_broken_themes;

		// assign new values to the global vars
		$wp_theme_directories = $this->wp_theme_directories;
		$wp_broken_themes     = $this->wp_broken_themes;
	}

	/**
	 * switch_theme_folders_end function
	 *
	 * @return void
	 **/
	public function switch_theme_folders_end() {
		global $wp_theme_directories, $wp_broken_themes;

		// assign back original values to the global vars
		$wp_theme_directories = $this->_wp_theme_directories;
		$wp_broken_themes     = $this->_wp_broken_themes;
	}
}
// END class
