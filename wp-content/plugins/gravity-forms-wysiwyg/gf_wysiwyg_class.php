<?php

if (!class_exists('gf_wysiwyg_logic')) {

  /*
   * Base class for the GFCPT Addon. All common code is in here and differences per version are overrided
   */
  class gf_wysiwyg_logic {
    
    protected $_wysiwygs = array();

    /*
     * Main initilize method for wiring up all the hooks
     */
    public function init() {

      //adds the button to the std field box
      add_filter( 'gform_add_field_buttons', array(&$this, 'add_wysiwyg_button') );
      
      // Adds title to GF custom field
      add_filter( 'gform_field_type_title', array(&$this, 'wysiwyg_title') );

      add_action( 'gform_field_input', array(&$this, 'wysiwyg_field_input'), 10, 5 );
      
      add_action( 'gform_editor_js', array(&$this, 'wysiwyg_editor_js') );

      add_action( 'gform_enqueue_scripts' , array(&$this, 'wysiwyg_enqueue_scripts') , 10 , 2 );
      
      add_filter( 'gform_save_field_value' , array(&$this, 'save_wysiwyg_values'), 10, 4);
      
      //add our advanced options to the form builder
      add_action( 'gform_field_standard_settings', array(&$this, 'wysiwyg_field_settings'), 10, 2);

      //include javascript for the form builder
      add_action( 'gform_editor_js', array(&$this, 'render_editor_js'));

      // filter to add a new tooltip
      //add_filter( 'gform_tooltips', array(&$this, 'add_gf_tooltips'));
      
      add_action( 'gform_preview_footer', array(&$this, 'render_preview_footer_js') );
    }
    
    function add_wysiwyg_button( $field_groups ) {
      foreach( $field_groups as &$group ){
      
        // to add to the Standard Fields
        if( $group['name'] == 'standard_fields' ) { 
          
          $group['fields'][] = array(
            'class' => 'button',
            'value' => __('WYSIWYG', 'gravityforms'),
            'onclick' => "StartAddField('wysiwyg');"
          );
          break;
          
        }
      }
      return $field_groups;      
    }
 
    function wysiwyg_title( $type ) {
      if ( $type == 'wysiwyg' ) {
        return __( 'WYSIWYG' , 'gravityforms' );
      }
    }
    
    function wysiwyg_field_input( $input, $field, $value, $lead_id, $form_id ) {
      if ( $this->is_wysiwyg($field) ) {
        
        $input_id = 'input_' . $form_id . '_' . $field["id"];
        
        if (is_admin()) {

          $tabindex = GFCommon::get_tabindex();
          return sprintf("<div class='ginput_container'><textarea readonly name='input_%s' id='input_%s' class='textarea gform_wysiwyg' {$tabindex} rows='10' cols='50'>WYSIWYG editor</textarea></div>", $field["id"], 'wysiwyg-'.$field['id']);
          
        } else {
          
          if (array_key_exists($field["id"], $this->_wysiwygs)) {
            return $this->_wysiwygs[$field["id"]];
          }

          return "";

        }
        
      }
      
      return false;
    }
    
    function wysiwyg_editor_js () {
      ?>
      <script type='text/javascript'>
        jQuery(document).ready(function($) {

          //this determines which fields to show for the wysiwyg field
          fieldSettings["wysiwyg"] = ".label_setting, .description_setting, .rules_setting, .admin_label_setting, .default_value_textarea_setting, .error_message_setting, .css_class_setting, .visibility_setting";

        });
      </script>
      <?php
    }
    
    function wysiwyg_enqueue_scripts( $form, $ajax ) {
      if (sizeof($this->_wysiwygs)>0) { return; }
      
      foreach ( $form['fields'] as $field ) {
        if ( $this->is_wysiwyg($field) ) {
          
          //$tabindex = GFCommon::$tab_index > 0 ? GFCommon::$tab_index++ : 0;
          $input_id = 'input_' . $form["id"] . '_' . $field["id"];
          
          $args = array(
            'textarea_name' => 'input_'.$field["id"],
            'wpautop' => true,
            'media_buttons' => false,
            'editor_class' => 'frontend',
            'textarea_rows' => 5,
            'tabindex' => 0 //$tabindex
          );          
          
          ob_start();
          wp_editor('', $input_id, $args);
          $html = ob_get_contents();
          ob_end_clean();
          
          $this->_wysiwygs[$field["id"]] = "<div class='ginput_container'>" . $html . "</div>";
        }
      }
    }
    
    function is_wysiwyg($field) {
      $type = $this->get_type($field);
      
      if ( $type == 'wysiwyg' ) { return true; }
      else if ( $type = 'post_content' ) {
        if ( array_key_exists( 'enableWysiwyg', $field ) ) {
          return $field['enableWysiwyg'] == 'true';
        }
      }
              
      return false;
    }
    
    function save_wysiwyg_values($value, $lead, $field, $form) {
      
      if ( $this->is_wysiwyg($field) ) {
        $value = rgpost("input_{$field['id']}");
      }
      
      return $value;
      
    }
    
    function wysiwyg_field_settings($position, $form_id) {
      if($position == 25) { ?>
        <li class="wysiwyg_field_setting field_setting" style="display:list-item;">
            <input type="checkbox" id="field_enable_wysiwyg" />
            <label for="field_enable_wysiwyg" class="inline">
                <?php _e("Enable WYSIWYG", "gravityforms"); ?>
            </label>
            <?php gform_tooltip("form_field_enable_wysiwyg") ?><br />
        </li>
      <?php
      }
    }
    
    function render_editor_js() { ?>
      <script type='text/javascript'>

        jQuery(document).bind("gform_load_field_settings", function(event, field, form){
          var field_type = field['type'];
          if (field_type == 'post_content') {
            
            var $wysiwyg_container = jQuery(".wysiwyg_field_setting");

            $wysiwyg_container.show();

            var enableWysiwyg = (typeof field['enableWysiwyg'] != 'undefined' && field['enableWysiwyg'] != '') ? field['enableWysiwyg'] : false;

            if (enableWysiwyg != false) {
                //check the checkbox if previously checked
                $wysiwyg_container.find("input:checkbox").attr("checked", "checked");
            } else {
                $wysiwyg_container.find("input:checkbox").removeAttr("checked");
            }            
            
          }
        });

        jQuery(".wysiwyg_field_setting input").click(function() {
            if (jQuery(this).is(":checked")) {
              SetFieldProperty('enableWysiwyg', 'true');
            } else {
              SetFieldProperty('enableWysiwyg', '');
            }
        });
        
      </script>
    <?php        
    }

    function render_preview_footer_js() {
      wp_footer();
    }
    
    function get_type($field) {
      $type = '';

      if ( array_key_exists( 'type', $field ) ) {
        $type = $field['type'];

        if ($type == 'post_custom_field') {
          if ( array_key_exists( 'inputType', $field ) ) {
            $type = $field['inputType'];
            //print_r($type);
          }
        }

        return $type;
      }
    }
    
  }
  
}

?>