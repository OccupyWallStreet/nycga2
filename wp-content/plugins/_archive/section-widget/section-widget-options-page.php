<?php

add_action('admin_menu', 'tabbed_section_widget_add_option_page');
/**
 * tabbed_section_widget_add_option_page function.
 * 
 * @access public
 * @return void
 */
function tabbed_section_widget_add_option_page() {
    add_theme_page(__('Section Widget Settings','section-widget'), __('Section Widget','section-widget'), 10, 'section-widget', 'tabbed_section_widget_option_page');
}
/**
 * tabbed_section_widget_option_page function.
 * 
 * @access public
 * @return void
 */
function tabbed_section_widget_option_page() {
    $updated = false;
    $options = wp_parse_args((array) get_option('section-widget-settings'), array(
        'theme' => 'redmond',
        'scope' => '.swt-outter',
        'heightfix' => false
    ));
    
    if(isset($_POST['Submit'])) {
        $options['theme'] = $_POST['swt_theme'];
        $options['scope'] = $_POST['swt_scope'];
        $options['heightfix'] = ($_POST['swt_heightfix'] == 'true')? true : false; 
        update_option('section-widget-settings',$options);
        $updated = true;
    }
    
    $theme = $options['theme'];
    $scope = $options['scope'];
    $heightFix = ($options['heightfix'])? ' checked="checked"' : '';
    
    // Find all themes
    $themes = array();
    
    $folder = dirname(__FILE__).'/themes';
    
    if ( $dir = @opendir( $folder ) ) {
        while (($theme_folder = readdir( $dir ) ) !== false ) {
            if ( is_file($folder.'/'.$theme_folder.'/sw-theme.css') )
                $themes[] = $theme_folder;
        }
    }
    @closedir( $dir );
    
    sort($themes);
    
    // For scope testing
    $links = array();
    
    // The home page
    $links[] = get_bloginfo('wpurl');
    
    // Grab a random page
    $page = get_pages('number=1');
        
    if(count($page) > 0)
        $links[] = get_permalink($page[0]->ID);
    
    // Grab a random post
    $post = get_posts('numberposts=1&orderby=rand'); // See how inconsistent WP is...
    
    if(count($post) > 0)
        $links[] = get_permalink($post[0]->ID);
    
    // Tags archive
    $tag = get_tags('number=1');
        
    if(count($tag) > 0)
        $links[] = get_tag_link($tag[0]->term_ID);
    
    // Cats archive
    $cat = get_categories('number=1');
    
    if(count($cat) > 0)
        $links[] = get_category_link($cat[0]->cat_ID);
    
    // And the 404
    $links[] = get_bloginfo('wpurl') . '/' . md5('random');
    
    $clean_links = array();
    
    foreach($links as $link)
        if(is_string($link) && substr($link, '0', '4') == 'http')
            $clean_links[] = "'" . add_query_arg('swt-scope-test', '', $link) . "'";
    
    $links_text = implode(',', $clean_links);
?>
<script type="text/javascript">
    var stylesheet_url = '<?php echo plugins_url("section-widget/themes/theme-loader.php"); ?>';
    var links = [<?php echo $links_text; ?>];
</script>

<div class="wrap">
    <h2><?php _e('Section Widget Settings','section-widget'); ?></h2>
    <?php if($updated): ?>
    <div class="updated"><p><strong><?php _e('Settings updated.','section-widget'); ?></strong></p></div>
    <?php endif; ?>
    <form method="post">
        <h3><?php _e('Tabbed Section Widget','section-widget'); ?></h3>
        <p>
            <?php _e('Section Widget ships with 25 default <a href="http://jqueryui.com/" target="_blank">jQuery UI</a> themes. You can specify a CSS selector which will be prepended to all CSS rules provided by the theme. Click on the help link below if you are not sure what to do.','section-widget'); ?>
        </p>
        <p>
            <?php _e('You may also roll your own theme by selecteing <strong>Bring My Own Stylesheet</strong> in theme menu. If you choose to do this, no extra stylesheet will be loaded and the CSS Scope option will be <strong>ignored</strong>. In this case the <strong>Base</strong> theme will be used in the widget designer. Refer to the <a href="http://jqueryui.com/docs/Theming" target="_blank">theming guide</a> for more information.','section-widget'); ?>
        </p>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label for="swt-theme"><?php _e('Theme','section-widget'); ?></label>
                </th>
                <td>
                    <select name="swt_theme" id="swt-theme">
                        <option value="none" <?php if($theme == 'none') echo 'selected="selected"'; ?>>
                            <?php _e('Bring My Own Stylesheet','section-widget'); ?>
                        </option>
                        <?php foreach($themes as $t): ?>
                        <option value="<?php echo $t; ?>" <?php if($theme == $t) echo 'selected="selected"'; ?>>
                            <?php echo theme_display_name($t); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="description">(<a id="swt-theme-preview-link" href="#"><?php _e('Preview','section-widget'); ?></a>)</span>
                    <div id="swt-theme-preview" style="display:none">
                        <div id="swt-theme-preview-wrapper">
                            <ul>
                                <li><a href="#swt-theme-preview-tab1">Tab 1</a></li>
                                <li><a href="#swt-theme-preview-tab2">Tab 2</a></li>
                                <li><a href="#swt-theme-preview-tab3">Tab 3</a></li>
                            </ul>
                            <div id="swt-theme-preview-tab1">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                            </div>
                            <div id="swt-theme-preview-tab2">
                                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                            </div>
                            <div id="swt-theme-preview-tab3">
                                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </div>
                        </div>
                        <p>
                            <a id="swt-theme-preview-hide-link" href="#"><?php _e('Hide preview','section-widget'); ?></a>
                        </p>
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label for="swt-scope"><?php _e('CSS Scope','section-widget'); ?></label>
                </th>
                <td>
                    <input name="swt_scope" type="text" id="swt-scope" value="<?php echo $scope ?>" class="regular-text code" />
                    <span class="description"><?php _e('Enter a CSS selector to limit the theme\'s scope (<a id="swt-scope-help-link" href="#">Help is here</a>)','section-widget'); ?></span>
                    <div id="swt-scope-help" style="display:none">
                        <p>
                            <?php _e('This is to ensure the theme you have chosen here will not affect other areas of your site. Generally speaking, you would want to narrow down the scope as much as possible. This would help to override rules defined by your WordPress theme too.','section-widget'); ?>
                        </p>
                        <p>
                            <?php _e('If you have no idea what this is all about, we can detect the optimal settings for you. To do this, you would need to have <strong>at least two Tabbed Section Widget</strong> added to your sidebar. You can do that in the','section-widget') ?> <a href="<?php echo admin_url('widgets.php'); ?>"><?php _e('Widgets','section-widget'); ?></a> <?php _e('control panel. You may choose to leave out the content fields so that they would be invisible to your visitors.','section-widget'); ?>
                        </p>
                        <p>
                            <?php _e('Once you\'ve done that, come back to this page and click the button below.','section-widget'); ?>
                        </p>
                        <div>
                            <div id="swt-scope-detect-message" style="display:none"></div>
                            <input type="button" class="button button-highlighted" value="Detect Scope" id="swt-scope-detect" />
                        </div>
                        <p>
                            <a id="swt-scope-help-hide-link" href="#"><?php _e('Hide this help message','section-widget'); ?></a>
                        </p>
                    </div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php _e('Height Fix','section-widget'); ?>
                </th>
                <td>
                    <label for="swt_heightfix">
                        <input name="swt_heightfix" type="checkbox" id="swt_heightfix" value="true" class="regular-text code"<?php echo $heightFix ?> />
                        <?php _e('Turn this on if the widget looks stretched out on your theme','section-widget'); ?>
                    </label>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes','section-widget'); ?>" />
        </p>
    </form>
</div>
<?php
}
/**
 * theme_display_name function.
 * 
 * @access public
 * @param mixed $theme_folder
 * @return void
 */
function theme_display_name($theme_folder) {
    $parts = explode('-',$theme_folder);
        
    foreach($parts as $i => $p) {
        if($p == 'ui')
            $parts[$i] = 'UI';
        else
            $parts[$i] = ucfirst($p);
    }
    
    return implode(' ', $parts);
}

?>