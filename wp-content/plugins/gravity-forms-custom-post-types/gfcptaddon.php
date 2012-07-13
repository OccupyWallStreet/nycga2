<?php
/*
Plugin Name: Gravity Forms + Custom Post Types
Plugin URI: http://themergency.com/plugins/gravity-forms-custom-post-types/
Description: Allows a simple way to map a Gravity Form post entry to a custom post type. Also include custom taxonomies.
Version: 3.0.1
Author: Brad Vincent
Author URI: http://themergency.com/
License: GPL2

------------------------------------------------------------------------
Copyright 2011 Themergency

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

// Include Gravity Forms
//if (!class_exists('RGForms'))
//    @include_once(WP_PLUGIN_DIR . '/gravityforms/gravityforms.php');
//if (!class_exists('RGFormsModel'))
//    @include_once(WP_PLUGIN_DIR . '/gravityforms/forms_model.php');
//if (!class_exists('GFCommon'))
//    @include_once(WP_PLUGIN_DIR . '/gravityforms/common.php');

add_action('init',  array('GFCPTAddon', 'init'), 20);
add_action('admin_notices', array('GFCPTAddon', 'admin_warnings'), 20);

class GFCPTAddon {

    private static $name = 'Gravity Forms + Custom Post Types';
    private static $slug = 'GFCPTAddon';
    private static $version = '2.0';
    private static $min_gravityforms_version = '1.5';

    //Plugin starting point. Will load appropriate files
    public static function init(){

        if(self::is_gravityforms_installed()){
	
            global $gf_cpt_addon;

            //include the base class
            require_once(self::get_base_path() . '/gfcptaddonbase.php');

            //only supports 1.5 and over
            require_once(self::get_base_path() . '/gfcptaddon_1-5.php');
            $gf_cpt_addon = new GFCPTAddon1_5();

            //start me up!
            $gf_cpt_addon->init(__FILE__);
        }
    }

    //display admin warnings if GF is not the correct version or GF is not installed
    public static function admin_warnings() {
        if ( !self::is_gravityforms_installed() ) {
            $message = __('requires Gravity Forms to be installed.', self::$slug);
        } else if ( !self::is_gravityforms_supported() ) {
            $message = __('requires a minimum Gravity Forms version of ', self::$slug) . self::$min_gravityforms_version;
        }

        if (empty($message)) {
            return;
        }
        ?>
        <div class="error">
            <p>
                <?php _e('The plugin ', self::$slug); ?><strong><?php echo self::$name; ?></strong> <?php echo $message; ?><br />
                <?php _e('Please ',self::$slug); ?><a href="http://bit.ly/getgravityforms"><?php _e(' download the latest version ',self::$slug); ?></a><?php _e(' of Gravity Forms and try again.',self::$slug) ?>
            </p>
        </div>
        <?php
    }

    /*
     * Check if GF is installed
     */
    private static function is_gravityforms_installed(){
        return class_exists( 'RGForms' );
    }

    /*
     * Check if the installed version of GF is supported
     */
    private static function is_gravityforms_supported(){
        return self::check_gravityforms_version( self::$min_gravityforms_version,'>=' );
    }

    /*
     * Do a GF version compare
     */
    private static function check_gravityforms_version($version, $operator){
        if(class_exists('GFCommon')){
            return version_compare( GFCommon::$version, $version, $operator );
        }
        return false;
    }

    /*
     * Returns the url of the plugin's root folder
     */
    protected function get_base_url(){
        return plugins_url(null, __FILE__);
    }

    /*
     * Returns the physical path of the plugin's root folder
     */
    protected function get_base_path(){
        $folder = basename(dirname(__FILE__));
        return WP_PLUGIN_DIR . '/' . $folder;
    }

    /**
     * starts_with
     * Tests if a text starts with an given string.
     *
     * @param     string
     * @param     string
     * @return    bool
     */
    public static function starts_with($haystack, $needle){
        return strpos($haystack, $needle) === 0;
    }

    /*
     * returns true if a needle can be found in a haystack
     */
    public static function str_contains($haystack, $needle) {
        if (empty($haystack) || empty($needle))
            return false;

        $pos = strpos(strtolower($haystack), strtolower($needle));

        if ($pos === false)
            return false;
        else
            return true;
    }
}
?>
