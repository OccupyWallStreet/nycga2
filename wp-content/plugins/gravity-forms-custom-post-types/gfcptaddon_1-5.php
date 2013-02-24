<?php

if (!class_exists('GFCPTAddon1_5')) {

    /*
     * GFCPT Addon class targetting version 1.5 of Gravity Forms
     */
    class GFCPTAddon1_5 extends GFCPTAddonBase {

        /*
         * Override. Include a couple more hooks
         */
        public function init() {
            //hook up the defaults
            parent::init();

            //then add these for 1.5...
            //add our advanced options to the form builder
            add_action('gform_field_advanced_settings', array(&$this, 'render_field_advanced_settings'), 10, 2);

            //include javascript for the form builder
            add_action('gform_editor_js', array(&$this, 'render_editor_js'));

            // filter to add a new tooltip
            add_filter('gform_tooltips', array(&$this, 'add_gf_tooltips'));
        }

        /*
         * Override. Gets the post type from our new field value
         */
        function get_field_post_type( $field ) {
          if (array_key_exists('populatePostType', $field)) {
            return $field['populatePostType'];
          } else {
            return false;
          }
        }

        /*
         * Override. Gets the taxonomy from our new field value
         */
        function get_field_taxonomy( $field ) {
          if (array_key_exists('populateTaxonomy', $field)) {
            return $field['populateTaxonomy'];
          } else if (array_key_exists('saveToTaxonomy', $field)) {
            return $field['saveToTaxonomy'];
          } else {
            return false;
          }
        }

        /*
         * Override. Gets the custom post type from the post title field value
         */
        function get_form_post_type( $form ) {
            foreach ( $form['fields'] as $field ) {
                if ( $field['type'] == 'post_title' && $field['saveAsCPT'] )
                    return $field['saveAsCPT'];
            }
            return false;
        }

        function get_form_parent_post_id( $form ) {
            foreach ( $form['fields'] as $field ) {
                if ( $field['type'] == 'select' && $field['setParentPost'] ) {
                    $parent_id = RGForms::post('input_'.$field['id']);
                    return $parent_id;
                }
            }
            return 0;
        }

        /*
         * Add tooltips for the new field values
         */
        function add_gf_tooltips($tooltips){
           $tooltips["form_field_populate_post_type"] = "<h6>Populate with a Post Type</h6>Check this box to populate this field from a specific post type.";
           $tooltips["form_field_set_parent_post"] = "<h6>Try to set parent</h6>If this is checked, and the form creates a post type, then the parent for the newly created post type will be set from the value of this field. Please note that this only works for heirarcical post typs e.g. pages";
           $tooltips["form_field_custom_taxonomy"] = "<h6>Populate with a Taxonomy</h6>Check this box to populate this field from a custom taxonomy.";
           $tooltips["form_field_custom_post_type"] = "<h6>Save As Post Type</h6>Check this box to save this form to a specific post type.";
           $tooltips["form_field_save_to_taxonomy"] = "<h6>Save To Taxonomy</h6>Check this box to save this field to a specific custom taxonomy. Please note that the taxonomy must NOT be hierarchical.";
           $tooltips["form_field_tax_enhanced"] = "<h6>Enable Enhanced UI</h6>By selecting this option, this field will be tranformed into a 'tag input' control which makes it more user-friendly for selecting existing and capturing new taxonomies.";
           return $tooltips;
        }

        /*
         * Add some advanced settings to the fields
         */
         function render_field_advanced_settings($position, $form_id){
            if($position == 50){
                ?>
                <li class="populate_with_taxonomy_field_setting field_setting" style="display:list-item;">
                    <input type="checkbox" id="field_enable_populate_with_taxonomy" />
                    <label for="field_enable_populate_with_taxonomy" class="inline">
                        <?php _e("Populate with a Taxonomy", "gravityforms"); ?>
                    </label>
                    <?php gform_tooltip("form_field_custom_taxonomy") ?><br />
                    <select id="field_populate_taxonomy" onchange="SetFieldProperty('populateTaxonomy', jQuery(this).val());" style="margin-top:10px; display:none;">
                        <option value="" style="color:#999;">Select a Taxonomy</option>
                    <?php
                    $args=array(
                      'public'   => true,
                      '_builtin' => false
                    );
                    $taxonomies = get_taxonomies($args, 'objects');
                    foreach($taxonomies as $taxonomy): ?>
                        <option value="<?php echo $taxonomy->name; ?>"><?php echo $taxonomy->label; ?></option>
                    <?php endforeach; ?>
                    </select>
                </li>
                <li class="populate_with_post_type_field_setting field_setting" style="display:list-item;">
                    <input type="checkbox" class="toggle_setting" id="field_enable_populate_with_post_type" />
                    <label for="field_enable_populate_with_post_type" class="inline">
                        <?php _e("Populate with a Post Type", "gravityforms"); ?>
                    </label>
                    <?php gform_tooltip("form_field_populate_post_type") ?><br />
                    <div style="margin-top:10px; display:none;">
                      <select id="field_populate_post_type" onchange="SetFieldProperty('populatePostType', jQuery(this).val());">
                          <option value="" style="color:#999;">Select a Post Type</option>
                      <?php
                      $args=array(
                        'public'        => true
                      );
                      $post_types = get_post_types($args, 'objects');
                      foreach($post_types as $post_type): ?>
                          <option value="<?php echo $post_type->name; ?>"><?php echo $post_type->label; ?></option>
                      <?php endforeach; ?>
                      </select>
                      <input type="checkbox" class="check_parent" onclick="SetFieldProperty('setParentPost', this.checked);" id="field_set_parent_post" />
                      <label for="field_set_parent_post" class="inline">
                          <?php _e("Try to set parent", "gravityforms"); ?>
                      </label>
                      <?php gform_tooltip("form_field_set_parent_post") ?>
                    </div>
                </li>
                <li class="custom_post_type_field_setting field_setting" style="display:list-item;">
                    <input type="checkbox" id="field_enable_custom_post_type" />
                    <label for="field_enable_custom_post_type" class="inline">
                        <?php _e("Save As Post Type", "gravityforms"); ?>
                    </label>
                    <?php gform_tooltip("form_field_custom_post_type") ?><br />
                    <select id="field_populate_custom_post_type" onchange="SetFieldProperty('saveAsCPT', jQuery(this).val());" style="margin-top:10px; display:none;">
                        <option value="" style="color:#999;">Select a Post Type</option>
                    <?php
                    $args=array(
                      'public'   => true
                    );
                    $post_types = get_post_types($args, 'objects');
                    foreach($post_types as $post_type): ?>
                        <option value="<?php echo $post_type->name; ?>"><?php echo $post_type->label; ?></option>
                    <?php endforeach; ?>
                    </select>
                </li>
                <li class="save_to_taxonomy_field_setting field_setting" style="display:list-item;">
                    <input type="checkbox" class="toggle_setting" id="field_enable_save_to_taxonomy" />
                    <label for="field_enable_save_to_taxonomy" class="inline">
                        <?php _e("Save To Taxonomy", "gravityforms"); ?>
                    </label>
                    <?php gform_tooltip("form_field_save_to_taxonomy") ?>
                    <div style="margin-top:10px; display:none;">
                      <select id="field_save_to_taxonomy" onchange="SetFieldProperty('saveToTaxonomy', jQuery(this).val());">
                          <option value="" style="color:#999;">Select a Taxonomy</option>
                      <?php
                      $args=array(
                        'public'   => true,
                        '_builtin' => false
                      );
                      $taxonomies = get_taxonomies($args, 'objects');
                      foreach($taxonomies as $taxonomy):
                          if ($taxonomy->hierarchical === false) {?>
                          <option value="<?php echo $taxonomy->name; ?>"><?php echo $taxonomy->label; ?></option>
                          <?php } ?>
                      <?php endforeach; ?>
                      </select>
                      <input type="checkbox" class="check_tax_enhanced" onclick="SetFieldProperty('taxonomyEnhanced', this.checked);" id="field_tax_enhanced" />
                      <label for="field_tax_enhanced" class="inline">
                          <?php _e("Enable enhanced UI", "gravityforms"); ?>
                      </label>
                      <?php gform_tooltip("form_field_tax_enhanced") ?>
                    </div>
                </li>
                <?php
            }

        }

        /*
         * render some custom JS to get the settings to work
         */
        function render_editor_js(){
            ?>
            <script type='text/javascript'>

                jQuery(document).bind("gform_load_field_settings", function(event, field, form){
                    //only show taxonomy for selects and radios
                    var valid_types = new Array('select', 'radio', 'checkbox', 'multiselect');

                    //alert(field['type']);

                    if(jQuery.inArray(field['type'], valid_types) != -1) {

                        var $taxonomy_setting_container = jQuery(".populate_with_taxonomy_field_setting");
                        //show the setting container!
                        $taxonomy_setting_container.show();

                        //get the saved taxonomy
                        var populateTaxonomy = (typeof field['populateTaxonomy'] != 'undefined' && field['populateTaxonomy'] != '') ? field['populateTaxonomy'] : false;

                        if (populateTaxonomy != false) {
                            //check the checkbox if previously checked
                            $taxonomy_setting_container.find("input:checkbox").attr("checked", "checked");
                            //set the select and show
                            $taxonomy_setting_container.find("select").val(populateTaxonomy).show();
                        } else {
                            $taxonomy_setting_container.find("input:checkbox").removeAttr("checked");
                            $taxonomy_setting_container.find("select").val('').hide();
                        }

                        if (field['type'] == 'select') {
                          var $populate_post_type_container = jQuery(".populate_with_post_type_field_setting");
                          $populate_post_type_container.show();

                          //get the saved post type
                          var populatePostType = (typeof field['populatePostType'] != 'undefined' && field['populatePostType'] != '') ? field['populatePostType'] : false;

                          if (populatePostType != false) {
                              //check the checkbox if previously checked
                              $populate_post_type_container.find("input.toggle_setting").attr("checked", "checked");
                              //set the select
                              $populate_post_type_container.find("select").val(populatePostType);
                              //show the div
                              $populate_post_type_container.find("div").show();

                              //get the saved check for setting the parent post
                              var setParent = (typeof field['setParentPost'] != 'undefined' && field['setParentPost'] != '') ? field['setParentPost'] : false;
                              if (setParent != false) {
                                $populate_post_type_container.find(".check_parent").attr("checked", "checked");
                              } else {
                                $populate_post_type_container.find(".check_parent").removeAttr("checked");
                              }
                          } else {
                              $taxonomy_setting_container.find("input.toggle_setting").removeAttr("checked");
                              $taxonomy_setting_container.find("select").val('');
                          }

                        }

                    } else if (field['type'] == 'post_title') {
                        var $cpt_setting_container = jQuery(".custom_post_type_field_setting");

                        $cpt_setting_container.show();

                        var saveAsCPT = (typeof field['saveAsCPT'] != 'undefined' && field['saveAsCPT'] != '') ? field['saveAsCPT'] : false;

                        if (saveAsCPT != false) {
                            //check the checkbox if previously checked
                            $cpt_setting_container.find("input:checkbox").attr("checked", "checked");
                            //set the select and show
                            $cpt_setting_container.find("select").val(saveAsCPT).show();
                        } else {
                            $cpt_setting_container.find("input:checkbox").removeAttr("checked");
                            $cpt_setting_container.find("select").val('').hide();
                        }
                    } else if (field['type'] == 'text') {
                        var $tax_setting_container = jQuery('.save_to_taxonomy_field_setting');

                        $tax_setting_container.show();

                        var saveToTax = (typeof field['saveToTaxonomy'] != 'undefined' && field['saveToTaxonomy'] != '') ? field['saveToTaxonomy'] : false;

                        if (saveToTax != false) {
                            //check the checkbox if previously checked
                            $tax_setting_container.find("input.toggle_setting").attr("checked", "checked");
                            //set the select
                            $tax_setting_container.find("select").val(saveToTax);
                            //show the div
                            $tax_setting_container.find("div").show();

                            //get the saved check for using enhanced UI
                            var useEnhancedUI = (typeof field['taxonomyEnhanced'] != 'undefined' && field['taxonomyEnhanced'] != '') ? field['taxonomyEnhanced'] : false;
                            if (useEnhancedUI != false) {
                              $tax_setting_container.find(".check_tax_enhanced").attr("checked", "checked");
                            } else {
                              $tax_setting_container.find(".check_tax_enhanced").removeAttr("checked");
                            }

                        } else {
                            $tax_setting_container.find("input.toggle_setting").removeAttr("checked");
                            $tax_setting_container.find("div").hide();
                            $tax_setting_container.find(".check_tax_enhanced").removeAttr("checked");
                            $tax_setting_container.find("select").val('');
                        }
                    }
                });

                jQuery(".populate_with_taxonomy_field_setting input:checkbox").click(function() {
                    var checked = jQuery(this).is(":checked");
                    var $select = jQuery(this).parent(".populate_with_taxonomy_field_setting:first").find("select");
                    if(checked){
                        $select.slideDown();

                        //uncheck post type
                        var $pt_container = jQuery(this).parents("ul:first").find(".populate_with_post_type_field_setting:first");
                        var $pt_check = $pt_container.find("input.toggle_setting");
                        var $pt_div = $pt_container.find("div");
                        if ($pt_check.is(":checked")) {

                          SetFieldProperty('populatePostType','');
                          $pt_div.slideUp();
                          $pt_check.removeAttr('checked');

                        }

                    } else {
                        SetFieldProperty('populateTaxonomy','');
                        $select.slideUp();
                    }
                });

                jQuery(".custom_post_type_field_setting input:checkbox").click(function() {
                    var checked = jQuery(this).is(":checked");
                    var $select = jQuery(this).parent(".custom_post_type_field_setting:first").find("select");
                    if(checked){
                        $select.slideDown();
                    } else {
                        SetFieldProperty('saveAsCPT','');
                        $select.slideUp();
                    }
                });

                jQuery(".populate_with_post_type_field_setting .toggle_setting").click(function() {
                    var checked = jQuery(this).is(":checked");
                    var $div = jQuery(this).parent(".populate_with_post_type_field_setting:first").find("div");
                    if(checked){
                        $div.slideDown();
                        //uncheck taxonomy
                        var $tax_container = jQuery(this).parents("ul:first").find(".populate_with_taxonomy_field_setting:first");
                        var $tax_check = $tax_container.find("input:checkbox");
                        var $tax_select = $tax_container.find("select");
                        if ($tax_check.is(":checked")) {

                          SetFieldProperty('populateTaxonomy','');
                          $tax_select.slideUp();
                          $tax_check.removeAttr('checked');
                          
                        }

                    } else {
                        SetFieldProperty('populatePostType','');
                        $div.slideUp();
                    }
                });

                jQuery(".save_to_taxonomy_field_setting .toggle_setting").click(function() {
                    var checked = jQuery(this).is(":checked");
                    var $div = jQuery(this).parent(".save_to_taxonomy_field_setting:first").find("div");
                    if(checked){
                        $div.slideDown();
                    } else {
                        SetFieldProperty('saveToTaxonomy','');
                        $div.slideUp();
                    }
                });

            </script>
            <?php
        }

    }

}
?>