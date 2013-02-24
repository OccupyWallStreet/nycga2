<?php

if (!class_exists('GFCPTAddonBase')) {

    /*
     * Base class for the GFCPT Addon. All common code is in here and differences per version are overrided
     */
    class GFCPTAddonBase {

      protected $_has_tag_inputs = false;
      protected $_included_js;
      protected $_tag_inputs = array();
      protected $_tag_map = array();
      protected $_tag_terms = array();
      

        /*
         * Main initilize method for wiring up all the hooks
         */
        public function init() {
            //alter the way forms are rendered by inserting taxomony dropdowns,radios and checkboxes
            add_filter('gform_pre_render' , array(&$this, 'setup_form') );
            
            //alter the way forms are rendered by the admin too!
            add_filter('gform_admin_pre_render' , array(&$this, 'setup_form') );

            //alter the form for submission - this is mainly for checkboxes
            add_filter('gform_pre_submission_filter', array(&$this, 'setup_form') );

            //set the post type when saving a post
            add_filter("gform_post_data", array(&$this, 'set_post_values'), 10, 2);

            //intercept the form save and save any taxonomy links if needed
            add_action('gform_post_submission', array(&$this, 'save_taxonomies'), 10, 2);

            //enqueue scripts to the page
            add_action('gform_enqueue_scripts', array(&$this, 'enqueue_custom_scripts'), 10, 2);

            add_action('wp_print_scripts', array(&$this, 'enqueue_scripts'), 10, 2);

            add_filter("gform_preview_styles", array(&$this, 'preview_print_styles'), 10, 2);
        }

        /*
         * Setup the form with any taxonomies etc
         */
        function setup_form( $form ) {
        
          //loop thru all fields
          foreach($form['fields'] as &$field) {

            //see if the field is using a taxonomy
            $taxonomy = $this->get_field_taxonomy( $field );

            if($taxonomy) {
              $this->setup_taxonomy_field( $field, $taxonomy );
              continue;
            }

            //if its a select then check if we have set a post type
            if ($field['type'] == 'select') {

              $post_type = $this->get_field_post_type( $field );

              if ($post_type) {
                $this->setup_post_type_field( $field, $post_type );
                continue;
              }

            }
          }

          return $form;
        }

        function enqueue_scripts() {
          if ($this->_has_tag_inputs) {
            $script_block = '';
            if (sizeof($this->_tag_inputs)>0) {
              $script_block = 'var gfcpt_tag_inputs = {"tag_inputs": [';
              $input_ids = array();
              foreach($this->_tag_inputs as $input_id => $taxonomy) {
                $input_ids[] = '{input: "'.$input_id.'", taxonomy: "'.$taxonomy.'"}';
              }
              $script_block .= implode(', ', $input_ids);
              $script_block .= ']};
';
            }

            if (sizeof($this->_tag_terms)>0) {
              $script_block .= 'var gfcpt_tag_taxonomies = [];
';
              foreach($this->_tag_terms as $taxonomy => $terms) {
                $script_block .= 'gfcpt_tag_taxonomies["'.$taxonomy.'"] = ["'.implode('", "', $terms).'"];
';
              }
            }
            if (strlen($script_block) > 0) {
            ?>
  <script type='text/javascript'>
    <?php
    echo $script_block;
    ?>
  </script>
  <?php
            }
          }
        }

        function preview_print_styles($styles, $form){
            return array('gfcpt_jquery_ui_theme', 'gfcpt_tagit_css');
        }

        function enqueue_custom_scripts($form, $is_ajax) {
          //loop thru all fields
          foreach($form['fields'] as &$field) {
            //if its a text field, see if we have set it to save to a taxonomy
            if ($field['type'] == 'text' && array_key_exists('saveToTaxonomy', $field)) {
              $saveToTaxonomy = $field['saveToTaxonomy'];

              if (taxonomy_exists($saveToTaxonomy) && array_key_exists('taxonomyEnhanced', $field)) {
                if ($field['taxonomyEnhanced']) {

                  $this->_has_tag_inputs = true;

                  $tag_input_id = '#input_'.$form['id'].'_'.$field['id'];

                  $this->_tag_inputs[$tag_input_id] = $saveToTaxonomy;

                  if ( !array_key_exists($saveToTaxonomy, $this->_tag_terms) ) {
                    //get the existing taxonomies and add them to an array to render later
                    $terms = get_terms($saveToTaxonomy, 'orderby=name&hide_empty=0&fields=names');
                    $this->_tag_terms[$saveToTaxonomy] = $terms;
                  }

                  if (!$this->_included_js) {

                    //enqueue some scripts for the enhaced UI
                    $this->_included_js = true;

                    wp_register_style(
                            $handle = 'gfcpt_jquery_ui_theme',
                            $src = plugins_url( 'css/custom/jquery-ui-1.8.16.custom.css' , __FILE__ ) );
                    wp_enqueue_style('gfcpt_jquery_ui_theme');

                    wp_register_style(
                            $handle = 'gfcpt_tagit_css',
                            $src = plugins_url( 'css/jquery.tagit.css' , __FILE__ ) );
                    wp_enqueue_style('gfcpt_tagit_css');


                    wp_register_script(
                            $handle = 'gfcpt_jquery_ui',
                            $src = plugins_url( 'js/jquery-ui-1.8.16.custom.min.js' , __FILE__ ),
                            $deps = array('jquery') );

                    wp_enqueue_script('gfcpt_jquery_ui');

                    wp_register_script(
                            $handle = 'gfcpt_tagit_js',
                            $src = plugins_url( 'js/tag-it.js' , __FILE__ ),
                            $deps = array('gfcpt_jquery_ui') );

                    wp_enqueue_script('gfcpt_tagit_js');

                    wp_register_script(
                            $handle = 'gfcpt_tagit_init_js',
                            $src = plugins_url( 'js/tag-it.init.js' , __FILE__ ),
                            $deps = array('gfcpt_tagit_js') );

                    wp_enqueue_script('gfcpt_tagit_init_js');
                  }

                }
              }
            }              
          }
        }

        /*
         * Set the post values (if neccessary)
         */
        function set_post_values( $post_data, $form ) {

            //check if the form saves a post
            if ( $this->is_form_a_post_form($form) ) {
                $target_post_type = $this->get_form_post_type( $form );

                if ($target_post_type)
                    $post_data["post_type"] = $target_post_type;

                //then check if we have set a parent
                $parent_post_id = $this->get_form_parent_post_id( $form );

                if ($parent_post_id > 0) {
                  $post_data["post_parent"] = $parent_post_id;
                }
            }
            return $post_data;

        }

        /*
         * Checks if a form includes a 'post field'
         */
        function is_form_a_post_form( $form ) {
            foreach ($form["fields"] as $field) {
                if(in_array($field["type"],
                        array("post_category","post_title","post_content",
                            "post_excerpt","post_tags","post_custom_fields","post_image")))
                    return true;
            }
            return false;
        }

        /*
         * override this to get the post type for a form
         */
        function get_form_post_type( $form ) {
            return null;
        }

        /*
         * override this to get the taxonomy for a field
         */
        function get_field_taxonomy( $field ) {
            return null;
        }

        /*
         * override this to get the post type for a field
         */
        function get_field_post_type( $field ) {
            return null;
        }

        /*
         * override this to get the parent Id for a form
         */
        function get_form_parent_post_id( $form ) {
            return 0;
        }
        
        /*
         * setup a field if it is linked to a post type
         */
        function setup_post_type_field( &$field, $post_type ) {
            $first_choice = $field['choices'][0]['text'];
            $field['choices'] = $this->load_post_type_choices( $post_type, $first_choice );
        }

        function load_post_type_choices($post_type, $first_choice = '') {
            $posts = $this->load_posts_hierarchical( $post_type );
            if ($first_choice === '' || $first_choice === 'First Choice'){
                // if no default option is specified, dynamically create based on post type name
                $post_type_obj = get_post_type_object($post_type);
                $choices[] = array('text' => "-- select a {$post_type_obj->labels->singular_name} --", 'value' => '');
            } else {
                $choices[] = array('text' => $first_choice, 'value' => '');
            }

            foreach($posts as $post) {
                $choices[] = array('value' => $post->ID, 'text' => $post->post_title);
            }

            return $choices;
        }

        /*
         * Get a hierarchical list of posts
         */
        function load_posts_hierarchical( $post_type ) {
            $args = array(
                'post_type'     => $post_type,
                'numberposts'   => -1,
                'orderby'       => 'title',
                'post_status'   => 'publish'
            );
            $posts = get_posts( $args );
            return $this->walk_posts( $posts );
        }

        /*
         * Helper function to recursively 'walk' the posts
         */
        function walk_posts( $input_array, $parent_id=0, &$out_array=array(), $level=0 ){
            foreach ( $input_array as $item ) {
                if ( $item->post_parent == $parent_id ) {
                        $item->post_title = str_repeat('--', $level) . $item->post_title;
                        $out_array[] = $item;
                        $this->walk_posts( $input_array, $item->ID, $out_array, $level+1 );
                }
            }
            return $out_array;
        }

        /*
         * setup a field if it is linked to a taxonomy
         */
        function setup_taxonomy_field( &$field, $taxonomy ) {
            $first_choice = $field['choices'][0]['text'];
            $field['choices'] = $this->load_taxonomy_choices( $taxonomy, $field['type'], $first_choice );

            //now check if we are dealing with a checkbox list and do some extra magic
            if ( $field['type'] == 'checkbox' ) {
                //clear the inputs first
                $field['inputs'] = array();

                $counter = 0;
                //recreate the inputs so they are captured correctly on form submission
                foreach( $field['choices'] as $choice ) {
                    $counter++;
                    if ( ($counter % 10) == 0 ) $counter++; //thanks to Peter Schuster for the help on this fix
                    $id = floatval( $field['id'] . '.' . $counter );
                    $field['inputs'][] = array('label' => $choice['text'], 'id' => $id);
                }
            }
        }

        /*
         * Load any taxonomy terms
         */
        function load_taxonomy_choices($taxonomy, $type, $first_choice = '') {
            $choices = array();

            if ($type === 'select') {
                $terms = $this->load_taxonomy_hierarchical( $taxonomy );
                if ($first_choice === '' || $first_choice === 'First Choice'){
                    // if no default option is specified, dynamically create based on taxonomy name
                    $taxonomy = get_taxonomy($taxonomy);
                    $choices[] = array('text' => "-- select a {$taxonomy->labels->singular_name} --", 'value' => '');
                } else {
                    $choices[] = array('text' => $first_choice, 'value' => '');
                }
            } else {
                $terms = get_terms($taxonomy, 'orderby=name&hide_empty=0');
            }

            if ( !array_key_exists("errors",$terms) ) {
              foreach($terms as $term) {
                  $choices[] = array('value' => $term->term_id, 'text' => $term->name);
              }
            }

            return $choices;
        }

        /*
         * Get a hierarchical list of taxonomies
         */
        function load_taxonomy_hierarchical( $taxonomy ) {
            $args = array(
                'taxonomy'      => $taxonomy,
                'orderby'       => 'name',
                'hierarchical'  => 1,
                'hide_empty'    => 0
            );
            $terms = get_categories( $args );
            
            if ( array_key_exists("errors",$terms) ) {
              return $terms;
            }
            else
              return $this->walk_terms( $terms );
        }

        /*
         * Helper function to recursively 'walk' the taxonomy terms
         */
        function walk_terms( $input_array, $parent_id=0, &$out_array=array(), $level=0 ){
            foreach ( $input_array as $item ) {
                if ( $item->parent == $parent_id ) {
                        $item->name = str_repeat('--', $level) . $item->name;
                        $out_array[] = $item;
                        $this->walk_terms( $input_array, $item->term_id, $out_array, $level+1 );
                }
            }
            return $out_array;
        }

        /*
         * Loop through all fields and save any linked taxonomies
         */
        function save_taxonomies( $entry, $form ) {
            // Check if the submission contains a WordPress post
            if ( isset ( $entry['post_id'] ) ) {

                foreach( $form['fields'] as &$field ) {

                    $taxonomy = $this->get_field_taxonomy( $field );

                    if ( !$taxonomy ) continue;

                    $this->save_taxonomy_field( $field, $entry, $taxonomy );
                }
            }
        }

        /*
         * Save linked taxonomies for a sinle field
         */
        function save_taxonomy_field( &$field, $entry, $taxonomy ) {
            if ( array_key_exists( 'type', $field ) && $field['type'] == 'checkbox' ) {
                $term_ids = array();
                foreach ( $field['inputs'] as $input ) {
                    $term_id = (int) $entry[ (string) $input['id'] ];
                    if ( $term_id > 0 )
                        $term_ids[] = $term_id;
                }
                if ( !empty ( $term_ids ))
                    wp_set_object_terms( $entry['post_id'], $term_ids, $taxonomy, true );
            } else if ( array_key_exists( 'type', $field ) && $field['type'] == 'text' ) {
              $terms = $entry[$field['id']];
              if ( !empty($terms) )
                wp_set_post_terms( $entry['post_id'], $terms, $taxonomy );
            } else {
                $term_id = (int) $entry[$field['id']];
                if ( $term_id > 0 )
                    wp_set_object_terms( $entry['post_id'], $term_id, $taxonomy, true );
            }
        }
    }
}

?>