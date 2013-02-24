<?php
//
// CheezCap - Cheezburger Custom Administration Panel
// (c) 2008 - 2010 Cheezburger Network (Pet Holdings, Inc.)
// LOL: http://cheezburger.com
// Source: http://code.google.com/p/cheezcap/
// Authors: Kyall Barrows, Toby McKes, Stefan Rusek, Scott Porad
// License: GNU General Public License, version 2 (GPL), http://www.gnu.org/licenses/gpl-2.0.html
//

class Group {
	var $name;
	var $id;
	var $options;
	
	function Group( $_name, $_id, $_options ) {
		$this->name = $_name;
		$this->id = "cap_$_id";
		$this->options = apply_filters('cc_cap_get_options', $_options, $_id);
	}
	
    function WriteHtml() {

					 
			echo '<div class="accordion">';
			for ( $i=0; $i < count( $this->options ); $i++ ) {				
				$this->options[$i]->WriteHtml();
			}
			echo '</div>';
	}
}

class Option {
	var $name;
	var $desc;
	var $id;
	var $_key;
	var $std;
	var $accordion;
	var $accordion_name;
	
	function Option( $_name, $_desc, $_id, $_std  ) {
		$this->name = $_name;
		$this->desc = $_desc;
		$this->id = "cap_$_id";
		$this->_key = $_id;
		$this->std = $_std;
	}
	
	function WriteHtml() {
		echo '';
	}
	
	function Reset( $ignored ) {
		update_option( $this->id, $this->std );
	}
	
	function Import( $data ) {
		if ( array_key_exists( $this->id, $data->dict ) )
				$cap = get_option('custom_community_theme_options');
				$cap[$this->id] = isset($data->dict[$this->id]) ? $data->dict[$this->id] : '';
				update_option( 'custom_community_theme_options', $cap );
	}
	
	function Export( $data ) {
		$cap = get_option('custom_community_theme_options');
		$data->dict[$this->id] = $cap[$this->id];	
	}

	function get() {
		$value = get_option('custom_community_theme_options');
		return isset($value[$this->id]) ? $value[$this->id] : '';
	}
}

class TextOption extends Option {
	var $useTextArea;

	function TextOption( $_name, $_desc, $_id, $_std = '', $_useTextArea = false, $_accordion = 'on', $_accordion_name = "off"  ) {
		$this->Option( $_name, $_desc, $_id, $_std );
		$this->useTextArea = $_useTextArea;
		$this->accordion = $_accordion;
		$this->accordion_name = $_accordion_name;
	}
	
	function WriteHtml() {

		$stdText = $this->std;
		$value = get_option('custom_community_theme_options');
    	if ( isset($value[$this->id]) && $value[$this->id] != "" )
            $stdText =  $value[$this->id];
	
			if($this->accordion == 'on' || $this->accordion == 'start'){ ?>	
				<?php if($this->accordion_name != 'off' && $this->accordion_name != __('off','cc') ) { ?>
					<h3 class="option-title"><a href="#"><?php echo $this->accordion_name; ?></a></h3>
					<div>
					<p class="option-title"><b><?php echo $this->name; ?></b></p>
				<?php } else {?>
					<h3 class="option-title"><a href="#"><?php echo $this->name; ?></a></h3>
					<div>
				<?php }?>
			<?php } else { ?>
				<p class="option-title"><b><?php echo $this->name; ?></b></p>
			<?php } ?>
			<p class="desc"><?php echo $this->desc; ?></p> 
			<?php $commentWidth = 2;
			if ( $this->useTextArea ) :
				$commentWidth = 1;
				?>
                <textarea class="text_option_teaxarea" name="custom_community_theme_options[<?php echo $this->id; ?>]" id="<?php echo $this->id; ?>"><?php echo esc_attr( stripcslashes($stdText) ); ?></textarea>
				<?php
			else :
				?>
				<input name="custom_community_theme_options[<?php echo $this->id; ?>]" id="<?php echo $this->id; ?>" type="text" value="<?php echo esc_attr( stripcslashes($stdText) ); ?>" size="40" />
				<?php
			endif; 
			
			if($this->accordion == 'on' || $this->accordion == 'end'){ ?>
				</div>
			<?php } ?>
	<?php 
	}

	function get() {
		$value = get_option('custom_community_theme_options');
		$value = isset($value[$this->id]) ? $value[$this->id] : '';

		if ( empty( $value ) )
			return $this->std;
		return $value;
	}
}

class CheckboxGroupOptions extends Option{
    public $options;
    
    function CheckboxGroupOptions($_name, $_desc, $_id, $_options, $_stdIndex = 0, $_accordion = 'on', $_accordion_name = "off"){
        $this->Option( $_name, $_desc, $_id, $_stdIndex );
		$this->options = $_options;
		$this->accordion = $_accordion;
		$this->accordion_name = $_accordion_name;
    }
    function WriteHtml() {
        if($this->accordion == 'on' || $this->accordion == 'start'){ ?>	
				<?php if($this->accordion_name != 'off') { ?>
					<h3 class="option-title"><a href="#"><?php echo $this->accordion_name; ?></a></h3>
					<div>
					<p class="option-title"><b><?php echo $this->name; ?></b></p>
				<?php } else {?>
					<h3 class="option-title"><a href="#"><?php echo $this->name; ?></a></h3>
					<div>
				<?php }?>
			<?php } else { ?>
				<p class="option-title"><b><?php echo $this->name; ?></b></p>
			<?php } ?>
				<p class="desc"><?php echo $this->desc; ?></p>
				<?php
				
                $value = get_option('custom_community_theme_options');
                $value = (isset($value[$this->id]) && is_serialized($value[$this->id])) ? unserialize($value[$this->id]) : array($value[$this->id]);
				foreach( $this->options as $option ) :
                    // If standard value is given?>
                    <label>
                        <input type="checkbox" class="checkbox-group" name="custom_community_theme_options[<?php echo $this->id; ?>][]" <?php echo in_array($option['id'], $value) ? 'checked="checked"' : '';?> value="<?php echo $option['id'];?>"/><?php echo $option['name'];?><br />
                    </label>
                <?php
				endforeach;
				?>
			<?php if( $this->accordion == 'on' || $this->accordion == 'end'){ ?>
				</div>
			<?php } 
    }
    
    function get(){
        $value = get_option('custom_community_theme_options');
        if(isset($value[$this->id]) && is_serialized($value[$this->id])){
            return unserialize($value[$this->id]);
        } else if(isset($value[$this->id]) && !is_serialized($value[$this->id])){
            return array($value[$this->id]);
        } else {
            return  array();
        }
    }
}

class DropdownOption extends Option {
	var $options;

	function DropdownOption( $_name, $_desc, $_id, $_options, $_stdIndex = 0, $_accordion = 'on', $_accordion_name = "off" ) {
		$this->Option( $_name, $_desc, $_id, $_stdIndex );
		$this->options = $_options;
		$this->accordion = $_accordion;
		$this->accordion_name = $_accordion_name;
	}
	
	function WriteHtml() {

			if($this->accordion == 'on' || $this->accordion == 'start'){ ?>	
				<?php if($this->accordion_name != 'off') { ?>
					<h3 class="option-title"><a href="#"><?php echo $this->accordion_name; ?></a></h3>
					<div>
					<p class="option-title"><b><?php echo $this->name; ?></b></p>
				<?php } else {?>
					<h3 class="option-title"><a href="#"><?php echo $this->name; ?></a></h3>
					<div>
				<?php }?>
			<?php } else { ?>
				<p class="option-title"><b><?php echo $this->name; ?></b></p>
			<?php } ?>
				<p class="desc"><?php echo $this->desc; ?></p>
				<select name="custom_community_theme_options[<?php echo $this->id; ?>]" id="<?php echo $this->id; ?>">
				<?php
				
				foreach( $this->options as $key=>$option ) :
					// If standard value is given
			
					$value = get_option('custom_community_theme_options');
					$value = (isset($value[$this->id]))? $value[$this->id] : '';
					if( $this->std != "" ){
						?>
                        <option <?php if(!is_numeric($key)) echo 'value="'.$key.'"'?> <?php if ( $value == $key || $value == $option || ( ! $value && (isset($this->options[ $this->std ]))? $this->options[ $this->std ] : '' == $option )) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
						<?php
					}else{ 
						?>
						<option <?php if(!is_numeric($key)) echo 'value="'.$key.'"'?> <?php if ($value == $key || $value == $option ) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
					<?php }
				endforeach;
				?>
				</select>
			<?php if( $this->accordion == 'on' || $this->accordion == 'end'){ ?>
				</div>
			<?php } ?>
			<?php
	}

	function get() {
		$value = get_option('custom_community_theme_options');
        $value = isset($value[$this->id]) ? $value[$this->id] : '';
        
        if ( strtolower( $value ) == 'disabled' ){
            return false;
        }
		return $value;
	}
}

class DropdownCatOption extends Option {
	var $options;

	function DropdownCatOption( $_name, $_desc, $_id, $_options, $_stdIndex = 0, $_accordion = 'on', $_accordion_name = "off" ) {
		$this->Option( $_name, $_desc, $_id, $_stdIndex );
		$this->options = $_options;
		$this->accordion = $_accordion;
		$this->accordion_name = $_accordion_name;
	}
	
	function WriteHtml() {

			if($this->accordion == 'on' || $this->accordion == 'start'){ ?>	
				<?php if($this->accordion_name != 'off') { ?>
					<h3 class="option-title"><a href="#"><?php echo $this->accordion_name; ?></a></h3>
					<div>
					<p class="option-title"><b><?php echo $this->name; ?></b></p>
				<?php } else {?>
					<h3 class="option-title"><a href="#"><?php echo $this->name; ?></a></h3>
					<div>
				<?php }?>
			<?php } else { ?>
                <div class="option-item <?php echo $this->id; ?>">
                    <p class="option-title"><b><?php echo $this->name; ?></b></p>
                <?php } ?>
                    <p class="desc"><?php echo $this->desc; ?></p>
                    <select name="custom_community_theme_options[<?php echo $this->id; ?>]" id="<?php echo $this->id; ?>">
                    <?php

                    foreach( $this->options as $option ) :
                        // If standard value is given

                        $value = get_option('custom_community_theme_options');
                        $value = (isset($value[$this->id]))? $value[$this->id] : '';
                        if( $this->std != "" ){
                            ?>
                            <option<?php if ( $value == $option['slug'] || ( ! $value && $this->options[ $this->std ] == $option['slug'] )) { echo ' selected="selected"'; } ?> value="<?php echo $option['slug'] ?>"><?php echo $option['name']; ?></option>
                            <?php
                        }else{ 
                            ?>
                            <option<?php if ( $value == $option['slug'] ) { echo ' selected="selected"'; } ?> value="<?php echo $option['slug'] ?>"><?php echo $option['name']; ?></option>
                        <?php }
                    endforeach;
                    ?>
                    </select>
			<?php if( $this->accordion == 'on' || $this->accordion == 'end'){ ?>
				</div>
			<?php } else {?>
                </div>
            <?php } ?>
			<?php
	}

	function get() {
		$value = get_option('custom_community_theme_options');
		$value = isset($value[$this->id]) ? $value[$this->id] : '';
		//echo $value;
	     	if ( strtolower( $value ) == 'disabled' )
			return false;
		return $value;
	}
}


class BooleanOption extends DropdownOption {
	var $default;

	function BooleanOption( $_name, $_desc, $_id, $_default = false, $_accordion = 'on', $_accordion_name = "off"   ) {
		$this->default = $_default;
		$this->DropdownOption( $_name, $_desc, $_id, array( __('Disabled','cc'), __('Enabled','cc') ), $_default ? 1 : 0 );
		$this->accordion = $_accordion;
		$this->accordion_name = $_accordion_name;
	}

	function get() {
		$value = get_option('custom_community_theme_options');
		$value = isset($value[$this->id]) ? $value[$this->id] : '';
		if ( is_bool( $value ) )
			return $value;
		switch ( strtolower( $value ) ) {
			case 'true':
			case 'enable':
			case 'enabled':
			case strtolower(__('Enabled','cc') ):
				return true;
			default:
				return false;
		}
	}
}

class ColorOption extends Option
{

	function ColorOption( $_name, $_desc, $_id, $_std = "", $_accordion = 'on', $_accordion_name = "off"   )
	{
        $this->Option( $_name, $_desc, $_id, $_std );
        $this->accordion = $_accordion;
		$this->accordion_name = $_accordion_name;
	}
	
	function WriteHtml(){
	
		$stdText = $this->std;
		$value = get_option('custom_community_theme_options');
    	if ( !empty($value[$this->id]) )
            $stdText =  $value[$this->id];

			if($this->accordion == 'on' || $this->accordion == 'start'){ ?>	
				<?php if($this->accordion_name != 'off') { ?>
					<h3 class="option-title"><a href="#"><?php echo $this->accordion_name; ?></a></h3>
					<div>
					<p class="option-title"><b><?php echo $this->name; ?></b></p>
				<?php } else {?>
					<h3 class="option-title"><a href="#"><?php echo $this->name; ?></a></h3>
					<div>
				<?php }?>
			<?php } else { ?>
				<p class="option-title"><b><?php echo $this->name; ?></b></p>
			<?php } ?>
			<p class="desc"><?php echo $this->desc; ?></p>
                <input name="custom_community_theme_options[<?php echo $this->id; ?>]" id="<?php echo $this->id ?>" type="text" value="<?php echo htmlspecialchars(stripslashes($stdText)) ?>" size="40" />
			<?php 
        	if($this->accordion == 'on' || $this->accordion == 'end'){ ?>
				</div>
			<?php } ?>

			<script type="text/javascript">
				jQuery('#<?php echo $this->id ?>').ColorPicker({
					onSubmit: function(hsb, hex, rgb, el) {
					jQuery(el).val(hex);
						jQuery(el).ColorPickerHide();
					},
					onBeforeShow: function () {
						jQuery(this).ColorPickerSetColor(this.value);
					}
				})
				.bind('keyup', function(){
					jQuery(this).ColorPickerSetColor(this.value);
				});
		
		</script>
	<?php 
	}

    function get() {
		$value = get_option('custom_community_theme_options');
    	$value = isset($value[$this->id]) ? $value[$this->id] : '';
        if (!$value)
            return $this->std;
        return $value;
    }
}


class FileOption extends Option
{

	function FileOption( $_name, $_desc, $_id, $_std = "", $_accordion = 'on', $_accordion_name = "off"  )
	{
        $this->Option( $_name, $_desc, $_id, $_std);
        $this->accordion = $_accordion;
		$this->accordion_name = $_accordion_name;
	}
	
	function WriteHtml()
	{

		$stdText = $this->std;
		$value = get_option('custom_community_theme_options');
    	if ( isset($value[$this->id]) && $value[$this->id] != "" )
            $stdText =  $value[$this->id];
		   
			if($this->accordion == 'on' || $this->accordion == 'start'){ ?>	
				<?php if($this->accordion_name != 'off') { ?>
					<h3 class="option-title"><a href="#"><?php echo $this->accordion_name; ?></a></h3>
					<div>
					<p class="option-title"><b><?php echo $this->name; ?></b></p>
				<?php } else {?>
					<h3 class="option-title"><a href="#"><?php echo $this->name; ?></a></h3>
					<div>
				<?php }?>
			<?php } else { ?>
				<p class="option-title"><b><?php echo $this->name; ?></b></p>
			<?php } ?>
			<p class="desc"><?php echo $this->desc; ?></p>
			
			<div class="option-inputs">

				<label for="image1">
				<input id="#upload_image<?php echo $this->id ?>" type="text" size="36" name="custom_community_theme_options[<?php echo $this->id; ?>]" value="<?php echo htmlspecialchars(stripslashes($stdText)) ?>" />
				<input class="upload_image_button" type="button" value="<?php _e('Browse..','cc')?>" />
				<img class="cc_image_preview" id="image_<?php echo $this->id ?>" src="<?php echo htmlspecialchars($stdText);  ?>" />
				
				</label>

			</div> 
		<?php 	if($this->accordion == 'on' || $this->accordion == 'end'){ ?>
				</div>
			<?php } ?>
		<?php 
	}

    function get() {
		$value = get_option('custom_community_theme_options');
		$value = isset($value[$this->id]) ? $value[$this->id] : '';
        if (!$value)
                return $this->std;
            return $value;
        }
}

// This class is the handy short cut for accessing config options
// 
// $cap->post_ratings is the same as get_bool_option("cap_post_ratings", false)
//

class autoconfig {
	private $data = false;
	private $cache = array();

	function init() {
		if ( $this->data )
			return;

		$this->data = array();
		$options = cap_get_options();

		foreach ($options as $group) {
			foreach($group->options as $option) {
                $this->data[$option->_key] = $option;
			}
		}
	}

	public function __get( $name ) {
		$this->init();
		if ( array_key_exists( $name, $this->cache ) )
			return $this->cache[$name];

		if ( empty($this->data[$name]) )
			return ''; 
		
		$option = $this->data[$name];
		$value = $this->cache[$name] = $option->get();
		return $value;
	}
}

function cap_admin_css() {

	wp_enqueue_style('thickbox');

	wp_enqueue_style( 'colorpicker-css', get_template_directory_uri().'/admin/css/colorpicker.css', false );
	//wp_enqueue_style( 'fileuploader-css', get_template_directory_uri().'/admin/css/fileuploader.css' );
	wp_enqueue_style( 'jquery-ui-css', get_template_directory_uri().'/admin/css/jquery-ui.css' );
	wp_enqueue_style('cc_admin', get_template_directory_uri() . '/_inc/css/admin.css');
}

function cap_admin_js_libs() {
	
	wp_enqueue_script( 'jquery-ui' );	
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-widget' );
	wp_enqueue_script( 'jquery-color' );
	
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_register_script('my-upload', get_template_directory_uri() . '/admin/js/uploader.js', array('jquery','media-upload','thickbox'));
	wp_enqueue_script('my-upload');
	
	wp_register_script( 'jquery-ui-accordion', get_template_directory_uri() . '/admin/js/jquery.ui.accordion.js', array( 'jquery' ), '1.8.9', true );
	wp_enqueue_script( 'jquery-ui-accordion' );	
	
	wp_enqueue_script( 'colorpicker-js', get_template_directory_uri()."/admin/js/colorpicker.js", array(), true );
	wp_enqueue_script( 'autogrow-textarea');

}

function cap_admin_js_footer() {
?>
<script type="text/javascript">
/* <![CDATA[ */
	jQuery(document).ready(function($) {
		jQuery("#config-tabs").tabs();
		jQuery(".accordion").accordion({ header: "h3", active: false, autoHeight: false, collapsible:true });
});
/* ]]> */
</script>
<?php
}

function top_level_settings() {
	global $themename, $cap;
    $cap = new autoconfig();
    if ( ! isset( $_REQUEST['updated'] ) )
		$_REQUEST['updated'] = false;

	if(!empty($_POST) && !empty($_POST['custom_community_theme_options'])){
		$options = get_option('custom_community_theme_options');
		$options = array_merge((array)$options, $_POST['custom_community_theme_options']);
        $options['cap_slideshow_cat'] = !empty($_POST['custom_community_theme_options']['cap_slideshow_cat']) ? serialize($options['cap_slideshow_cat']) : serialize(array());
		update_option('custom_community_theme_options', $options);
		$cap = new autoconfig();
		do_action('cc_after_theme_settings_saved');
	}
    
	if ( isset( $_REQUEST['saved'] ) )
		echo "<div id='message' class='updated fade'><p><strong>$themename settings saved.</strong></p></div>";
	if ( isset( $_REQUEST['reset'] ) )
		echo "<div id='message' class='updated fade'><p><strong>$themename settings reset.</strong></p></div>";
	?>
	<div class="wrap">
	
		<h2><b><?php echo $themename; ?> <?php _e('Options','cc')?></b></h2>
        <p class="additional_info"><?php _e('Custom Community is proudly brought to you by ','cc')?><a class="themekraft-link" href="http://themekraft.com/" target="_blank">Themekraft</a>.
		<br> 
		<?php if(!defined('is_pro')){ ?>
			<?php _e('Looking for more? ','cc');?><a class="full-version-link" href="http://themekraft.com/shop/custom-community-pro/" target="_blank"><?php _e('Get the full version','cc')?></a>.
		<br>
        <?php } ?>
		<a style="margin-top:10px;" href="http://support.themekraft.com/categories/20053996-custom-community" class="button button-secondary" target="_blank"><?php _e('Documentation','cc')?></a> <a class="button button-secondary" href="http://themekraft.com/support/" style="margin-top:10px;" target="_blank"><?php _e('Support','cc')?></a>
		</p>
		
		<form method="post" action="">
		<?php settings_fields( 'custom_community_options' ); ?>
	
		<div id="config-tabs">
			<ul>
                    <?php
                    $groups = cap_get_options();
                    foreach( $groups as $group ) :
                    $role_section = substr($group->id, 4) . "_min_role";
                    if(empty($cap->$role_section) || current_user_can($cap->$role_section)){
                        ?>
                            <li><a href='#<?php echo $group->id; ?>'><?php echo $group->name; ?></a></li>
                        <?php
                    }
				endforeach;
				
				if(!defined('is_pro')){
					$cap_getpro = 'Get the Pro';
					echo " <li><a href='#cap_getpro'>$cap_getpro</a></li>";
				}

				?>
			</ul>
			<?php
			foreach( $groups as $group ) :
                 $id = $group->id;
                 $role_section = substr($id, 4) . "_min_role";
                 if(empty($cap->$role_section) || current_user_can($cap->$role_section)){
                        ?>
                            <div id="<?php echo $id;?>">
                                <?php 
                                      do_action('cc_before_settings_tab', $id);
                                      $group->WriteHtml(); 
                                ?>
                            </div>
                        <?php
                 }
			endforeach;
			get_pro();
			?>
		</div>
		
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options','cc' ); ?>" />
			</p>
			
		</form>
        
        <!--Manage options-->
        <fieldset class="import-export">
            <legend><?php _e('Managing settings data', CC_TRANSLATE_DOMAIN)?></legend>
            <form enctype="multipart/form-data" method="post">
                <p class="submit alignleft">
                    <input type="file" name="file" />
                </p>
                <p class="submit alignleft">
                    <input name="action" type="submit" value="<?php _e('Import','cc');?>" />
                </p>
                <p class="submit alignleft">
                    <input name="action" type="submit" value="<?php _e('Export','cc');?>" />
                </p>
                <p class="submit alignright">
                    <input name="action" type="submit" value="<?php _e('Reset All Settings','cc');?>" />
                </p>
		</form>
        </fieldset>
		<div class="clear"></div>
        <?php /*?>
		<h2><?php _e('Preview (updated when options are saved)','cc');?></h2>
		<iframe src="<?php echo home_url( '?preview=true' ); ?>" width="100%" height="600" ></iframe>
         * 
         */?>
	</div>
	<?php
}

class ImportData {
	var $dict = array();
}

function cap_serialize_export( $data ) {
	header( 'Content-disposition: attachment; filename=theme-export.txt' );
	echo serialize( $data );
	exit();
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function custom_community_theme_options_validate( $input ) {

$cap_options = cap_get_options();

$cap_options_types = Array();

foreach( $cap_options as $cap_option ) :
	$cap_option_arr = (Array) $cap_option;
	foreach ($cap_option_arr['options'] AS $option){
		$cap_options_types[$option->id] = get_class($option);
	}
endforeach;

foreach($input as $key => $value) :
	if(isset($cap_options_types[$key])){
        switch($cap_options_types[$key]){
            case 'BooleanOption':
                if( $input[$key] == 1 ? 1 : 0);
            break;
            default:
                if(!is_string($input[$key])){
                    $input[$key] = '';
                }
            break;
        }
    }
endforeach;

 return $input;
}
