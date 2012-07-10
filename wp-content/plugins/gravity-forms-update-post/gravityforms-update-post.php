<?php
/*
Plugin Name: Gravity Forms - Update Post
Plugin URI: http://p51labs.com
Description: Allow Gravity Forms to Update Post Content and the data associated with it.
Version: 0.5.3
Author: Kevin Miller
Author URI: http://p51labs.com
Contributer: Ron Sparks
Contributer URI: http://ronsparks.net

------------------------------------------------------------------------
Copyright 2012 P51 Labs

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

$gform_update_post = new GFUpdatePost();

class GFUpdatePost
{
  public $options = array(
    'request_id' => 'gform_post_id'
    ,'post_status' => 'default'
    ,'capabilities' => array(
      'update' => 'default' 
      ,'delete' => 'disable' 
    )
    ,'entries' => true
  );
  
  private $name = 'gform_update_post';
  
  private $post = array(
    'ID' => null
    ,'object' => null
    ,'type_object' => null
  );
  
  private $form = array(
    'conditional' => array()
  );
  
  public function __construct()
  {
    add_action('init', array(&$this, 'init'), 100);
  }
  
  public function init()
  {
    $this->options = apply_filters($this->name . '_options', $this->options);

    if (isset($_REQUEST[$this->options['request_id']]) && is_numeric($_REQUEST[$this->options['request_id']]))
    {
      $this->get_post_object($_REQUEST[$this->options['request_id']]);

      add_filter('gform_form_tag', array(&$this, 'gform_form_tag'), 50, 2);
      add_filter('gform_submit_button', array(&$this, 'gform_submit_button'), 50, 2);
      add_filter('gform_pre_render', array(&$this, 'gform_pre_render'));
      add_filter('gform_confirmation', array(&$this, 'gform_confirmation_delete'), 10, 4);
      add_filter('gform_validation', array(&$this, 'gform_validation'));
      
      add_action('gform_post_data', array(&$this, 'gform_post_data'), 10, 2);
      add_action('gform_post_submission', array(&$this, 'gform_post_submission'), 10, 2);
    }

    add_filter('gform_tooltips', array(&$this, 'gform_tooltips'));
    
    add_action('gform_field_standard_settings', array(&$this, 'gform_field_standard_settings'), 10, 2);
    add_action('gform_editor_js', array(&$this, 'gform_editor_js'));
  }
  
  public function gform_field_standard_settings($position, $form_id)
  {
    if ($position == 700): 
?>
    
    <li class="post_custom_field_unique field_setting">

      <input type="checkbox" id="field_unique_custom_meta_value" onclick="SetFieldProperty('postCustomFieldUnique', this.checked);" />
      
      <label for="field_unique_custom_meta_value" class="inline">
        <?php _e('Unique Custom Field?'); ?>
        <?php gform_tooltip('form_field_unique_custom_meta_value') ?>
      </label>
    
    </li>
    
<?php
    endif;
  }
  
  public function gform_editor_js()
  {
?>
    <script type="text/javascript">
  
      var fieldTypes = [
        'post_custom_field'
      ];
    
      for (var i = 0; i < fieldTypes.length; i++)
      {
        fieldSettings[fieldTypes[i]] += ', .post_custom_field_unique';
      }

      jQuery(document).bind('gform_load_field_settings', function(event, field, form)
      {
        jQuery('#field_unique_custom_meta_value').attr('checked', field['postCustomFieldUnique'] == true);
      });
  
    </script>
<?php
  }

  public function gform_tooltips($tooltips)
  {
     $tooltips['form_field_unique_custom_meta_value'] = "<h6>Unique Meta Field</h6>Check this box to ensure this meta field is saved as unique.";
     
     return $tooltips;
  }
  
  public function get_post_object($id)
  {
    global $wp_post_types, $wp_taxonomies;
    
    $this->post = array(
      'ID' => $id
      ,'object' => get_post($id, ARRAY_A)
      ,'taxonomies' => array()
    );

    foreach ($wp_taxonomies as $taxonomy => $properties)
    {
      if (in_array($this->post['object']['post_type'], $properties->object_type))
      {
        $this->post['taxonomies'][$taxonomy == 'post_tag' ? 'post_tags' : $taxonomy] = wp_get_object_terms($this->post['ID'], $taxonomy);
      }
    }
  }
  
  public function gform_pre_render($form)
  {
    $this->options['request_id'] = apply_filters($this->name . '_id', $this->options['request_id']);
    
    if ($this->is_allowed())
    {
      $meta = get_post_custom($this->post['ID']);
      
      foreach ($form['fields'] as &$field)
      {
        $field_type = RGFormsModel::get_input_type($field);

        if (isset($this->post['object'][$field['type']]))
        {
          $field = $this->gform_populate_element($field, $field_type, $this->post['object'][$field['type']]);
        }
        elseif ($field['type'] == 'post_custom_field' && isset($meta[$field['postCustomFieldName']]))
        {
          $field = $this->gform_populate_element($field, $field_type, end($meta[$field['postCustomFieldName']]));
        }
        elseif (isset($this->post['taxonomies'][$field_type]) || (!empty($field['populateTaxonomy']) && $this->post['taxonomies'][$field['populateTaxonomy']]))
        {
          $field = $this->gform_populate_element($field, $field_type, isset($this->post['taxonomies'][$field_type]) ? $this->post['taxonomies'][$field_type] : $this->post['taxonomies'][$field['populateTaxonomy']], 'name');
        }
        
        if (!empty($field['defaultValue']) && !empty($field['conditionalLogic']) && is_array($field['conditionalLogic']))
        {
          foreach ($field['conditionalLogic']['rules'] as $rule)
          {
            if (!in_array($rule['fieldId'], $this->form['conditional']))
            {
              array_push($this->form['conditional'], $rule['fieldId']);
            }
          }
        }
        
        $field = apply_filters($this->name . '_field_default_value', $field);
      }
    }

    return $form;
  }
  
  public function gform_populate_element($field, $field_type, $value, $value_index = false)
  {
    if ($value_index)
    {
      $new_value = array();
      foreach ($value as $object)
      {
        array_push($new_value, is_object($object) ? $object->$value_index : $object[$value_index]);
      }
      $value = $new_value;
    }
      
    $value = maybe_unserialize($value);
    
    switch ($field_type)
    {
      case 'select': 
      case 'multiselect': 
      case 'checkbox':
      case 'radio':      
      case 'list':      
        
        $value = !is_array($value) ? array($value) : $value;
        
        if (isset($field['choices']))
        {
          foreach ($field['choices'] as &$choice)
          {
            foreach ($value as $term)
            {
              if (($value_index && isset($term[$value_index]) && $term[$value_index] == $choice['text']) || $choice['text'] == $term || $term == $choice['value'])
              {
                $choice['isSelected'] = true;
              }
            }
          }
        }
        
      break;
    
      default: 
        
        if (is_array($value))
        {
          $value = implode(', ', $value);
        }
        
        $field['defaultValue'] = $value;
        
      break;
    }

    return $field;
  }
  
  public function gform_post_data($post_data, $form)
  {
    if ($this->is_allowed() && !$this->is_delete())
    {
      // If a custom field is unique, delete the old value before we proceed
      foreach ($form['fields'] as $field)
      {
        if ($field['type'] == 'post_custom_field' && isset($field['postCustomFieldUnique']))
        {
          delete_post_meta($this->post['ID'], $field['postCustomFieldName']);
        }
      }
      
      $post_data['ID'] = $this->post['ID'];
      $post_data['post_type'] = $this->post['object']['post_type'];
      
      $this->options['post_status'] = apply_filters($this->name . '_status', $this->options['post_status'], $form);
      
      if (in_array($this->options['post_status'], array('draft', 'publish', 'pending', 'future', 'private', 'inherit')))
      {
        $post_data['post_status'] = $this->options['post_status'] == 'inherit' ? $this->post['object']['post_status'] : $this->options['post_status'];
      }
    }

    return $post_data;
  }  
  
  public function gform_form_tag($form_tag, $form)
  {
    if ($this->is_allowed())
    {
      $form_tag .= '<input type="hidden" name="' . $this->options['request_id'] . '" value="' . $this->post['ID'] . '" class="gform_hidden" />';
      
      if (!empty($this->form['conditional']))
      {
        $inputs = array();
        foreach ($this->form['conditional'] as $field_id)
        {
          array_push($inputs, "input[name=input_$field_id]");
        }
        
        $form_tag .= '<script type="text/javascript">jQuery(document).load(function(){ jQuery("' . implode(', ', $inputs) . '").trigger("click"); });</script>';
       
        $this->form['conditional'] = array();
      }
    }
    
    return $form_tag;
  }
  
  public function gform_submit_button($button, $form)
  {
    if ($this->options['capabilities']['delete'] != 'disable')
    {
      $button .= apply_filters($this->name . '_delete_button', '<input type="submit" id="' . $this->name . '_delete_button_' . $form["id"] . '" name="' . $this->name . '_delete_button" class="button gform_button" value="' . __('Delete') . '" />', $form);
    }
    
    return $button;
  }
  
  public function gform_validation($validation_result)
  {
    if ($this->is_delete())
    {
      $validation_result['is_valid'] = true;
      
      foreach($validation_result['form']['fields'] as &$field)
      {
        $field['failed_validation'] = false;
      }
    }
    
    return $validation_result;
  }
  
  public function gform_post_submission($entry, $form)
  {
    global $wpdb;

    $this->options['entries'] = apply_filters($this->name . '_entries', $this->options['entries'], $form);

    if (!$this->options['entries'] || $this->is_delete())
    {
      $tables = (object) array(
        'lead_table' => RGFormsModel::get_lead_table_name()
        ,'lead_notes_table' => RGFormsModel::get_lead_notes_table_name()
        ,'lead_detail_table' => RGFormsModel::get_lead_details_table_name()
        ,'lead_detail_long_table' => RGFormsModel::get_lead_details_long_table_name()
      );

      $queries = array(
        $wpdb->prepare("DELETE FROM $tables->lead_detail_long_table WHERE lead_detail_id IN (SELECT id FROM $tables->lead_detail_table WHERE lead_id = %d)", $entry['id'])
        ,$wpdb->prepare("DELETE FROM $tables->lead_detail_table WHERE lead_id = %d", $entry['id'])
        ,$wpdb->prepare("DELETE FROM $tables->lead_notes_table WHERE lead_id = %d", $entry['id'])
        ,$wpdb->prepare("DELETE FROM $tables->lead_table WHERE id = %d", $entry['id'])
      );
    
      foreach ($queries as $query)
      {
        $wpdb->query($query);
      }
    }
    
    if ($this->is_delete())
    {
      wp_delete_post($this->post['ID']);
    }
    
    // If a custom field is unique, get all the rows and combine them into one
    foreach ($form['fields'] as $field)
    {
      if ($field['type'] == 'post_custom_field' && isset($field['postCustomFieldUnique']))
      {
        $meta = get_post_meta($this->post['ID'], $field['postCustomFieldName']);
        
        delete_post_meta($this->post['ID'], $field['postCustomFieldName']);
        
        add_post_meta($this->post['ID'], $field['postCustomFieldName'], is_array($meta) && count($meta) == 1 ? $meta[0] : $meta, true);
      }
    }
  }
  
  public function gform_confirmation_delete($confirmation, $form, $lead, $ajax)
  {
    if ($this->is_delete())
    {
      $confirmation =  apply_filters($this->name . '_confirmation_delete', $confirmation);
    }
    
    return $confirmation;
  }
  
  private function is_allowed($type = 'update')
  {
    if (!is_user_logged_in() || is_null($this->post['object']) || !in_array($type, array_keys($this->options['capabilities'])))
    {
      return false;
    }
    
    $allowed = false;
      
    switch ($this->options['capabilities'][$type])
    {
      case 'author':

        $current_user = wp_get_current_user();
      
        $allowed = $current_user->ID == $this->post['object']['post_author'];
        
      break;

      case 'default':

        $allowed = current_user_can($this->get_capability($type, $this->post['object']['post_type']));
        
      break;
      
      default:
      
        $allowed = current_user_can($this->options['capabilities'][$type]);
        
      break;
    }
    
    return $allowed;
  }
  
  private function is_delete()
  {
    return isset($_REQUEST[$this->name . '_delete_button']) && $this->is_allowed('delete');
  }
  
  private function get_capability($type, $post_type)
  {
    $capability = null;
    
    if (is_null($this->post['type_object']))
    {
      $this->post['type_object'] = get_post_type_object($this->post['object']['post_type']);
    }
    
    switch ($type)
    {
      case 'update':
      
        $capability = $this->post['type_object']->cap->edit_posts;
        
      break;
      
      case 'delete':
      
        $capability = $this->post['type_object']->cap->delete_posts;
        
      break;
    }
    
    return $capability;
  }
  
  private function pre($output)
  {
    echo '<pre>';
    
    print_r($output);
    
    echo '</pre>';
  }
}

?>