<?php if( $activated ) : ?>
	<div id="message2" class="updated">
		<p>
			<?php printf( __( 'New theme activated. <a href="%s">Visit site</a>', AI1EC_PLUGIN_NAME ), home_url( '/' ) ); ?>
		</p>
	</div>
<?php elseif( $deleted ) : ?>
	<div id="message3" class="updated">
		<p>
			<?php _e( 'Theme deleted.', AI1EC_PLUGIN_NAME ) ?>
		</p>
	</div>
<?php endif; ?>

<div class="wrap">
	<?php screen_icon() ?>
	<h2><?php echo esc_html( __( 'Manage Themes', AI1EC_PLUGIN_NAME ) ); ?></h2>
	<h3><?php _e('Current Theme'); ?></h3>
	<div id="current-theme"<?php echo ( $ct->screenshot ) ? ' class="has-screenshot"' : '' ?>>
	<?php if ( $ct->screenshot ) : ?>
	<img src="<?php echo $ct->theme_root_uri . '/' . $ct->stylesheet . '/' . $ct->screenshot; ?>" alt="<?php esc_attr_e('Current theme preview'); ?>" />
	<?php endif; ?>
	<h4><?php
		/* translators: 1: theme title, 2: theme version, 3: theme author */
		printf(__('%1$s %2$s by %3$s'), $ct->title, $ct->version, $ct->author) ; ?></h4>
	<p class="theme-description"><?php echo $ct->description; ?></p>
	<div class="theme-options">
		<?php if ( $ct->tags ) : ?>
		<p><?php _e('Tags:'); ?> <?php echo join(', ', $ct->tags); ?></p>
		<?php endif; ?>
	</div>
	<?php theme_update_available($ct); ?>

	</div>

	<br class="clear" />
	<?php
	if ( ! current_user_can( 'switch_themes' ) ) {
		echo '</div>';
		require( './admin-footer.php' );
		exit;
	}
	?>

	<h3><?php _e('Available Themes'); ?></h3>

	<?php if ( !empty( $_REQUEST['s'] ) || !empty( $_REQUEST['filter'] ) || $wp_list_table->has_items() ) : ?>

	<form class="search-form filter-form" action="<?php echo admin_url( AI1EC_THEME_SELECTION_BASE_URL ) ?>" method="get">
		<input type="hidden" name="page" value="<?php echo AI1EC_PLUGIN_NAME ?>-themes" />
	<p class="search-box">
		<label class="screen-reader-text" for="theme-search-input"><?php _e('Search Installed Themes'); ?>:</label>
		<input type="text" id="theme-search-input" name="s" value="<?php _admin_search_query(); ?>" />
		<?php submit_button( __( 'Search Installed Themes' ), 'button', false, false, array( 'id' => 'search-submit' ) ); ?>
		<a id="filter-click" href="<?php echo admin_url( AI1EC_THEME_SELECTION_BASE_URL ) ?>&amp;filter=1"><?php _e( 'Feature Filter' ); ?></a>
	</p>

	<br class="clear"/>

	<div id="filter-box" style="<?php if ( empty($_REQUEST['filter']) ) echo 'display: none;'; ?>">
	<?php $feature_list = Ai1ec_Themes_List_Table::get_theme_feature_list(); ?>
		<div class="feature-filter">
			<p class="install-help"><?php _e('Theme filters') ?></p>
		<?php if ( !empty( $_REQUEST['filter'] ) ) : ?>
			<input type="hidden" name="filter" value="1" />
		<?php endif; ?>
		<?php foreach ( $feature_list as $feature_name => $features ) :
				$feature_name = esc_html( $feature_name ); ?>

			<div class="feature-container">
				<div class="feature-name"><?php echo $feature_name ?></div>

				<ol class="feature-group">
					<?php foreach ( $features as $key => $feature ) :
							$feature_name = $feature;
							$feature_name = esc_html( $feature_name );
							$feature = esc_attr( $feature );
							?>
					<li>
						<input type="checkbox" name="features[]" id="feature-id-<?php echo $key; ?>" value="<?php echo $key; ?>" <?php checked( in_array( $key, $wp_list_table->features ) ); ?>/>
						<label for="feature-id-<?php echo $key; ?>"><?php echo $feature_name; ?></label>
					</li>
					<?php endforeach; ?>
				</ol>
			</div>
		<?php endforeach; ?>

		<div class="feature-container">
			<?php submit_button( __( 'Apply Filters' ), 'button-secondary submitter', false, false, array( 'style' => 'margin-left: 120px', 'id' => 'filter-submit' ) ); ?>
			&nbsp;
			<small><a id="mini-filter-click" href="<?php echo esc_url( remove_query_arg( array('filter', 'features', 'submit') ) ); ?>"><?php _e( 'Close filters' )?></a></small>
		</div>
		<br/>
		</div>
		<br class="clear"/>
	</div>

	<br class="clear" />

	<?php endif; ?>

	<?php $wp_list_table->display(); ?>

	</form>
	<br class="clear" />

	<?php
	// List broken themes, if any.
	$broken_themes = get_broken_themes();
	if ( current_user_can('edit_themes') && count( $broken_themes ) ) {
	?>

	<h3><?php _e('Broken Themes'); ?></h3>
	<p><?php _e('The following themes are installed but incomplete. Themes must have a stylesheet and a template.'); ?></p>

	<table id="broken-themes">
		<tr>
			<th><?php _ex('Name', 'theme name'); ?></th>
			<th><?php _e('Description'); ?></th>
		</tr>
	<?php
		$theme = '';

		$theme_names = array_keys($broken_themes);
		natcasesort($theme_names);

		foreach ($theme_names as $theme_name) {
			$title = $broken_themes[$theme_name]['Title'];
			$description = $broken_themes[$theme_name]['Description'];

			$theme = ('class="alternate"' == $theme) ? '' : 'class="alternate"';
			echo "
			<tr $theme>
				 <td>$title</td>
				 <td>$description</td>
			</tr>";
		}
	?>
	</table>
	<?php
	}
	?>
	</div>
