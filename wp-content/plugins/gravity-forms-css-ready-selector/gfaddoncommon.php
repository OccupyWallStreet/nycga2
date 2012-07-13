<?php

if (!class_exists('GFAddonCommon')) {

  class GFAddonCommon {

    //display admin warnings if GF is not the correct version or GF is not installed
    public static function admin_warnings($plugin, $textdomain, $min_gf_version) {
      if ( !self::is_gravityforms_installed() ) {
        $message = __('requires Gravity Forms to be installed.', $textdomain);
      } else if ( !self::is_gravityforms_supported($min_gf_version) ) {
        $message = __('requires a minimum Gravity Forms version of ', $textdomain) . $min_gf_version;
      }

      if (empty($message)) {
        return;
      }
      ?>
      <div class="error">
        <p>
          <?php _e('The plugin ', $textdomain); ?><strong><?php echo $plugin; ?></strong> <?php echo $message; ?><br />
          <?php _e('Please ', $textdomain); ?><a href="http://bit.ly/getgravityforms"><?php _e(' download the latest version ',$textdomain); ?></a><?php _e(' of Gravity Forms and try again.',$textdomain) ?>
        </p>
      </div>
      <?php
    }

    /*
     * Check if GF is installed
     */
    public static function is_gravityforms_installed(){
      return class_exists( 'RGForms' );
    }

    /*
     * Check if the installed version of GF is supported
     */
    public static function is_gravityforms_supported($min_gf_version = '1.5'){
      return self::check_gravityforms_version( $min_gf_version,'>=' );
    }

    /*
     * Do a GF version compare
     */
    public static function check_gravityforms_version($version, $operator){
      if(class_exists('GFCommon')){
        return version_compare( GFCommon::$version, $version, $operator );
      }
      return false;
    }

    /*
     * Returns the url of the plugin's root folder
     */
    public function get_base_url(){
      return plugins_url(null, __FILE__);
    }

    /*
     * Returns the physical path of the plugin's root folder
     */
    public function get_base_path(){
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
  
}
?>