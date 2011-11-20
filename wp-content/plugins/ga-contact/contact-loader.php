<?php
/*ini_set('display_errors',1); 
error_reporting(E_ALL);*/
class CONTACT extends BP_Group_Extension {
	var $contact = false;
	
	var $slug = 'contact';
	var $name = false;
	var $nav_item_name = false;

	/* By default - Is it visible to non-members of a group? Options: public/private */
	var $visibility = true;

	var $create_step_position = 6;
	var $nav_item_position = 15;

	var $enable_create_step = true; // will set to true in future version
	var $enable_nav_item = false;
	var $enable_edit_item = true;

	var $display_hook = 'groups_contacttab_group_boxes';
	var $template_file = 'groups/single/plugins';
	
	function CONTACT(){
		global $bp;
		
		// populate extra extras data in global var
		//$bp->groups->current_group->contacttab = groups_get_groupmeta($bp->groups->current_group->id, 'contact');
		
		// Display or Hide top menu from group non-members
		$this->visibility = /*$bp->groups->current_group->contacttab['display_page'] ? $bp->groups->current_group->contacttab['display_page'] :*/ 'public';
		$this->enable_nav_item = /*$bp->groups->current_group->contacttab['display_page'] == 'public' ?*/ true /*: false*/;
                $this->enable_edit_item = current_user_can('manage_options');
                
		// In Admin
		$this->name = contact_names('nav');
		// Public page
		$this->nav_item_name = contact_names('name');
		
		add_action('groups_custom_group_fields_editable', array($this, 'edit_group_fields'));
		add_action('groups_group_details_edited', array($this, 'edit_group_fields_save'));                      
	}
	
	// Public page with already saved content
	function display() {
		global $bp;
                //assume the tab is empty until proven otherwise
                $emptytab = true;
		$fields = $this->get_all_fields($bp->groups->current_group->id);
		if (empty($fields))
			$this->add_default_fields();
		//Temp fix for bug
		$listed = array();			
		echo '<div class="extra-data">';
			foreach($fields as $field){
				$data = groups_get_groupmeta($bp->groups->current_group->id, $field->slug);	
				if ( is_array($data))
					$data = implode(', ', $data);
				$data = stripslashes($data);
				if ( $field->display != 1 || empty($data) || in_array($field->slug, $listed) ){
					continue;
				}
                                elseif( $emptytab == true ){
					$emptytab=false;
                                }
				echo '<h4 title="' . ( ! empty($field->desc)  ? esc_attr($field->desc) : '')  .'">' . $field->title .'</h4>';
				$data = $this->auto_link($data);
				echo '<p>' . apply_filters('groups_ga_custom_tab',$data) . '</p>'; 
				array_push($listed, $field->slug);                    
			}
                        if($emptytab==true){
                            echo '<p>This group has not added any contact information yet. </p>';
                        }
		echo '</div>';
	}

	// Display exra fields on edit group details page
	function edit_group_fields(){
		global $bp;
		$fields = $this->get_all_fields($bp->groups->current_group->id);
		if (empty($fields))
			return false;
		
		foreach( $fields as $field ){
			$field->value = groups_get_groupmeta($bp->groups->current_group->id, $field->slug);
			$field->value = stripslashes($field->value);
			$req = false;
			if ( $field->required == 1 ) $req = '* ';
			echo '<label for="' . $field->slug . '">' . $req . $field->title . '</label>';
			switch($field->type){
				case 'text':
					echo '<input id="' . $field->slug . '" name="contact-' . $field->slug . '" type="text" value="' . $field->value . '" />';
					break;
				case 'textarea':
					echo '<textarea id="' . $field->slug . '" name="contact-' . $field->slug . '">' . $field->value . '</textarea>';
					break;
				case 'select':
					echo '<select id="' . $field->slug . '" name="contact-' . $field->slug . '">';
						echo '<option ' . ($field->value == $option ? 'selected="selected"' : '') .' value="">-------</option>';
						foreach($field->options as $option){
							echo '<option ' . ($field->value == $option ? 'selected="selected"' : '') .' value="' . $option . '">' . $option . '</option>';
						}
					echo '</select>';
					break;
				case 'checkbox':
					
					foreach($field->options as $option){
						echo '<input ' . ( in_array($option, $field->value) ? 'checked="checked"' : '') .' type="' . $field->type . '" name="contact-' . $field->slug . '[]" value="' . $option . '"> ' . $option . '<br />';
					}
					break;
				case 'radio':
					echo '<span id="contact-' . $field->slug . '">';
						foreach($field->options as $option){
							echo '<input ' . ($field->value == $option ? 'checked="checked"' : '') .' type="' . $field->type . '" name="contact-' . $field->slug . '" value="' . $option . '"> ' . $option . '<br />';
						}
					echo '</span>';
					if ($req) 
						echo '<a class="clear-value" href="javascript:clear( \'contact-' . $field->slug . '\' );">'. __( 'Clear', 'contact' ) .'</a>';
					break;
				case 'datebox':
					echo '<input id="' . $field->slug . '" class="datebox" name="contact-' . $field->slug . '" type="text" value="' . $field->value . '" />';
					break;					
			}
			if ( ! empty($field->desc) ) echo '<p class="description">' . $field->desc . '</p>';
			$req = false;
		}
		
	}
	
	// Save extra fields in groupmeta
	function edit_group_fields_save($group_id){
            global $bp;
            if ( $bp->current_component == $bp->groups->slug && 'edit-details' == $bp->action_variables[0] ) {
                if ( $bp->is_item_admin || $bp->is_item_mod  ) {
                    // If the edit form has been submitted, save the edited details
                    if ( isset( $_POST['save'] ) ) {
                        /* Check the nonce first. */
                        if ( !check_admin_referer( 'groups_edit_group_details' ) )
                            return false;
                        $to_save = array();                        
                        foreach($_POST as $data => $value){
                            if ( substr($data, 0, 8) === 'contact-' ){
                                $oldvalue = groups_get_groupmeta($bp->groups->current_group->id, substr($data,8));
                                if($oldvalue != $value){
                                    $to_save[$data] =  $value;
				    groups_delete_groupmeta($bp->groups->current_group->id, substr($data,8));
                                }
                            }
                        }
                        
                        if(count($to_save) > 0){
                            foreach($to_save as $key => $value){
                                $key = substr($key, 8);
                                if ( ! is_array($value) ) {
                                    $value = wp_kses_data($value);
                                    $value = force_balance_tags($value);
                                }
                                groups_update_groupmeta($group_id, $key, $value);
                            }
                            $this->save_activity();
			}                            
                    }
                }
            }
	}
        
        function save_activity(){
            global $bp;
            //Throttle duplicate activity items when fields are edited multiple times in a short timespan
            $duplicate_args = array(
            'max' => 1,
            'sort' => 'DESC',
            'show_hidden' => 1, // We need to compare against all activity
            'filter' => array(
            'user_id' => $bp->loggedin_user->id,
            'action' => 'contact_update', // BP bug. 'action' is type
            'item_id' => $bp->groups->current_group->id // We don't really care about the item_id for these purposes (it could have been changed)
                ),
            );

            $duplicate_activity = bp_activity_get( $duplicate_args );
                   
            // If any activity items are found, compare its date_recorded with time() to
            // see if it's within the allotted throttle time. If so, don't record the
            // activity item
            if ( !empty( $duplicate_activity['activities'] ) ) {
                $date_recorded 	= $duplicate_activity['activities'][0]->date_recorded;
                $drunix 	= strtotime( $date_recorded );
                if ( time() - $drunix <= apply_filters( 'bp_contact_edit_activity_throttle_time', 60*45 ) )
                    return;
            }
                                            
            // Record this in activity streams
            if ( function_exists( 'bp_is_current_component' ) ) {
                foreach ( $bp->active_components as $comp => $value ) {
                        if ( bp_is_current_component( $comp ) ) {
                            $component = $comp;
                            break;
                        }
                }
            } else {
                $component = bp_current_component();
            }
    
            $primary_link = bp_get_group_permalink( $bp->groups->current_group ) . 'contact/';
            $activity_action = sprintf( __( '%1$s updated the contact information for the %2$s group', 'buddypress'), bp_core_get_userlink( $bp->loggedin_user->id ), '<a href="' . $primary_link . '">' . $bp->groups->current_group->name . '</a>');
    
            $args = array(
                'user_id'               => $bp->loggedin_user->id,
                'action'                => apply_filters( 'groups_activity_contact_update_action',       $activity_action ),
                'primary_link'          => apply_filters( 'groups_activity_contact_update_primary_link', $primary_link ),
                'component'		=> $component,
                'type'			=> 'contact_update',
                'item_id'		=> $bp->groups->current_group->id, // Set to the group/user/etc id, for better consistency with other BP components
                'secondary_item_id'	=> false, 
                'recorded_time'		=> bp_core_current_time(),
                'hide_sitewide'		=> apply_filters( 'bp_ga_contact_hide_sitewide', false, $bp->groups->current_group->id, $component ) // Filtered to allow plugins and integration pieces to dictate
            );
            bp_activity_add( apply_filters( 'bp_contact_activity_args', $args ) );
        }        
	
	function widget_display() {
		echo '';
		//echo 'BP_Group_Extension::widget_display()';
	}

	// Admin area
	function edit_screen() {
		global $bp;
		//print_var($bp->groups->current_group);

		if ( 'admin' == $bp->current_action && $bp->action_variables[1] == 'fields' ) {
			$this->edit_screen_fields($bp);
		}elseif ( 'admin' == $bp->current_action && $bp->action_variables[1] == 'fields-manage' ) {
			$this->edit_screen_fields_manage($bp);
		}elseif ( 'admin' == $bp->current_action && $bp->action_variables[1] == 'fields-reset' ) {
			$this->edit_screen_fields_reset($bp);			
		}else{
			$this->edit_screen_general($bp);
		}

	}
	
	function edit_screen_general($bp){
		$fields = $this->get_all_fields($bp->groups->current_group->id);
		if (empty($fields))
			$this->add_default_fields();		
		$public_checked = $bp->groups->current_group->contacttab['display_page'] == 'public' ? 'checked="checked"' : '';
		$private_checked = $bp->groups->current_group->contacttab['display_page'] == 'private' ? 'checked="checked"' : '';

		$this->edit_screen_head('general');
		
		echo '<p>';
			echo '<label for="group_contacttab_display">'.sprintf(__('Do you want to make <strong>"%s"</strong> page public (extra group information will be displayed on this page)?','contact'), $this->nav_item_name).'</label>';
			echo '<input type="radio" value="public" '.$public_checked.' name="group-contacttab-display"> '.__('Show it', 'contact').'<br />';
			echo '<input type="radio" value="private" '.$private_checked.' name="group-contacttab-display"> '. __('Hide it', 'contact');
		echo '</p>';
		
		echo '<hr />';
		
		/*
		echo '<p>';
			$fields = $this->get_all_fields($bp->groups->current_group->id);
			if (!empty($fields)){
				echo '<label for="group_extras_display">'.sprintf(__('Please choose below the fields you want to show on <strong>"%s"</strong> page:','contact'), $this->nav_item_name).'</label>';
				foreach ((array)$fields as $field){
					echo '<input type="checkbox" value="1" ' . (($field->display == 1) ? 'checked="chekced"' : '' ) . ' name="extra-field-display['.$field->slug.']"> ' . $field->title . '<br />';
				}
			}
		echo '</p>';
                */
		
		echo '<p><input type="submit" name="save_general" id="save" value="'.__('Save Changes &rarr;','contact').'"></p>';
		wp_nonce_field('groups_edit_group_contacttab');
	}
	
	function edit_screen_fields($bp){
	
		$this->edit_screen_head('fields');

		$fields = $this->get_all_fields($bp->groups->current_group->id);

		if(empty($fields)){
			$this->notices('no-fields');
			return false;
		}

		echo '<ul id="fields-sortable">';
			foreach($fields as $field){
				echo '<li id="position_'.str_replace('_', '', $field->slug).'" class="default">
								<strong title="' . $field->desc . '">' . $field->title .'</strong> &rarr; ' . $field->type . '
								<span class="field-link">
									<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug . '/fields-manage/?edit=' . $field->slug . '" class="button" title="'.__('Change its title, description etc','contact').'">'.__('Edit field', 'contact').'</a>&nbsp;
									<a href="#" class="button delete_field" title="'.__('Delete this field and all its content', 'contact').'">'.__('Delete', 'contact').'</a>
								</span>
							</li>';
			}
		echo '</ul>';
	}
	
	function edit_screen_fields_reset($bp){
	
		$this->edit_screen_head('fields');

		echo '<p>Warning: This will remove all custom fields.</p>';
                echo '<p><input type="submit" name="save_restore" id="save" value="Restore"></p>';
		wp_nonce_field('groups_edit_group_contacttab');
	}      	
	
	function edit_screen_fields_manage($bp){

		if (isset($_GET['edit']) && !empty($_GET['edit'])){
			$field = $this->get_field_by_slug($_GET['edit']);
		}
		
		$this->edit_screen_head('fields-manage');
		
		echo '<p>';
			echo '<label>' . __('Field Title', 'contact') . '</label>';
			echo '<input type="text" value="'.$field->title.'" name="extra-field-title">';
			
			if (empty($field)){
				echo '<label>' . __('Field Type', 'contact') . '</label>';
				echo '<select name="extra-field-type" id="extra-field-type">';
					echo '<option value="text">' . __('Text Box', 'contact') . '</option>';
					echo '<option value="textarea">' . __('Multi-line Text Box', 'contact') . '</option>';
					echo '<option value="checkbox">' . __('Checkboxes', 'contact') . '</option>';
					echo '<option value="radio">' . __('Radio Buttons', 'contact') . '</option>';
					//echo '<option value="datebox">' . __('Date Selector', 'contact') . '</option>';
					echo '<option value="select">' . __('Drop Down Select Box', 'contact') . '</option>';
				echo '</select>';
				
				echo '<div id="extra-field-vars">';
					echo '<div class="content"></div>';
					echo '<div class="links">
									<a class="button" href="#" id="add_new">' . __('Add New', 'contact') . '</a>
							</div>';
				echo '</div>';
			}
			echo '<label>' . __('Field Description', 'contact') . '</label>';
				echo '<textarea name="extra-field-desc">'.$field->title.'</textarea>';
			
			echo '<label for="extra-field-required">' . __('Is this field required (will be displayed on appropriate group creation step)?','contact') . '</label>';
				$req = '';
				$not_req = 'checked="checked"';
				if ( $field->required == 1 ) {
					$req = 'checked="checked"';
					$not_req = '';
				}
				echo '<input type="radio" value="1" '.$req.' name="extra-field-required"> '.__('Required', 'contact').'<br />';
				echo '<input type="radio" value="0" '.$not_req.' name="extra-field-required"> '. __('Not Required', 'contact');
				
			echo '<label for="extra-field-display">' . sprintf(__('Should this field be displayed for public on "<u>%s</u>" page?','contact'), $this->nav_item_name) . '</label>';
				$disp = 'checked="checked"';
				$not_disp = '';
				if ( $field->display != 1 ) {
					$not_disp = 'checked="checked"';
					$disp = '';
				}
				echo '<input type="radio" value="1" '.$disp.' name="extra-field-display"> '.__('Display it', 'contact').'<br />';
				echo '<input type="radio" value="0" '.$not_disp.' name="extra-field-display"> '. __('Do NOT display it', 'contact');
		echo '</p>';
		
		if (empty($field)){
			echo '<p><input type="submit" name="save_fields_add" id="save" value="'.__('Create New &rarr;','contact').'"></p>';
		}else{
			echo '<input type="hidden" name="extra-field-slug" value="' . $field->slug . '">';
			echo '<p><input type="submit" name="save_fields_edit" id="save" value="'.__('Save Changes &rarr;','contact').'"></p>';
		}
		wp_nonce_field('groups_edit_group_contacttab');
	}
	
	// save all changes into DB
	function edit_screen_save() {
		global $bp;
		if ( $bp->current_component == $bp->groups->slug && 'contact' == $bp->action_variables[0] ) {
			if ( !$bp->is_item_admin )
				return false;
			// Save general settings
			if ( isset($_POST['save_general'])){
				/* Check the nonce first. */
				if ( !check_admin_referer( 'groups_edit_group_contacttab' ) )
					return false;
				
				$meta = array();
				$meta['display_page'] = $_POST['group-contacttab-display'];
				
				// Save into groupmeta table
				groups_update_groupmeta( $bp->groups->current_group->id, 'contact', $meta );
				
				$this->notices('updated');
				
				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/' );
			}
			
			if ( isset($_POST['save_restore'])){
				/* Check the nonce first. */
				if ( !check_admin_referer( 'groups_edit_group_contacttab' ) )
					return false;
				
				$this->remove_all_fields();
				$this->add_default_fields();
				
				$this->notices('updated');
				
				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/' );
			}				
			
			// Save new field
			if ( isset($_POST['save_fields_add'])){
				/* Check the nonce first. */
				if ( !check_admin_referer( 'groups_edit_group_contacttab' ) )
					return false;

				// get current fields if any
				$fields = $this->get_all_fields($bp->groups->current_group->id);
				if (!$fields)	
					$fields = array();
				
				$new = new Stdclass;
				$new->title = htmlspecialchars(strip_tags($_POST['extra-field-title']));
				$new->slug = str_replace('-', '_', sanitize_title($new->title)); // will be used as unique identifier
				$new->desc = htmlspecialchars(strip_tags($_POST['extra-field-desc']));
				$new->type = $_POST['extra-field-type'];
				$new->required = $_POST['extra-field-required'];
				$new->display = $_POST['extra-field-display'];
				if(!empty($_POST['options'])){
					foreach($_POST['options'] as $option){
						$new->options[] = htmlspecialchars(strip_tags($option));
					}
				}
				
				// To the end of an array of current fields
				array_push($fields, $new);

				// Save into groupmeta table
				$fields = json_encode($fields);
				groups_update_groupmeta( $bp->groups->current_group->id, 'contact_fields', $fields );

				$this->notices('added');
				
				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields/' );
			}
			
			// Edit existing field
			if ( isset($_POST['save_fields_edit'])){
				/* Check the nonce first. */
				if ( !check_admin_referer( 'groups_edit_group_contacttab' ) )
					return false;
					
				// get current fields
				$fields = $this->get_all_fields($bp->groups->current_group->id);
				foreach( $fields as $field ){
					if ( $_POST['extra-field-slug'] == $field->slug ){
						$field->title = htmlspecialchars(strip_tags($_POST['extra-field-title']));
						$field->desc = htmlspecialchars(strip_tags($_POST['extra-field-desc']));
						$field->required = $_POST['extra-field-required'];
						$field->display = $_POST['extra-field-display'];
					}
					$updated[] = $field;
				}
				// Save into groupmeta table
				$updated = json_encode($updated);
				groups_update_groupmeta( $bp->groups->current_group->id, 'contact_fields', $updated );
				
				$this->notices('edited');
				
				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields/' );
				
			}
		}
	}

	// Display Header and Extra-Nav
	function edit_screen_head($cur = 'general'){
		global $bp;
		if ($cur == 'general'){
			echo '<span class="extra-title">'.contact_names('title_general').'</span>';
			echo '<span class="extra-subnav">
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/'.'" class="button active">'. __('General', 'contact') .'</a>
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields/'.'" class="button">'. __('All Fields', 'contact') .'</a>
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields-manage/'.'" class="button">'. __('Add Fields', 'contact') .'</a>
                                                <a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields-reset/'.'" class="button">'. __('Restore Default Fields', 'contact') .'</a>						
					</span>';
		}elseif ($cur == 'fields'){
			echo '<span class="extra-title">'.contact_names('title_fields').'</span>';
			echo '<span class="extra-subnav">
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/'.'" class="button">'. __('General', 'contact') .'</a>
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields/'.'" class="button active">'. __('All Fields', 'contact') .'</a>
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields-manage/'.'" class="button">'. __('Add Fields', 'contact') .'</a>
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields-reset/'.'" class="button">'. __('Restore Default Fields', 'contact') .'</a>	
					</span>';
		}elseif ($cur == 'fields-reset'){
                        echo '<span class="extra-title">'.contact_names('title_fields_reset').'</span>';
			echo '<span class="extra-subnav">
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/'.'" class="button">'. __('General', 'contact') .'</a>
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields/'.'" class="button active">'. __('All Fields', 'contact') .'</a>
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields-manage/'.'" class="button">'. __('Add Fields', 'contact') .'</a>
                                                <a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields-reset/'.'" class="button">'. __('Restore Default Fields', 'contact') .'</a>
					</span>';					
		}elseif ($cur == 'fields-manage'){
			if ( isset($_GET['edit']) && !empty($_GET['edit']) ){
				echo '<span class="extra-title">'.contact_names('title_fields_edit').'</span>';
				$active = '';
			}else{
				echo '<span class="extra-title">'.contact_names('title_fields_add').'</span>';
				$active = 'active';
			}
			echo '<span class="extra-subnav">
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug . '/" class="button">'. __('General', 'contact') .'</a>
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug . '/fields/" class="button">'. __('All Fields', 'contact') .'</a>
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug . '/fields-manage/" class="button ' . $active . '">'. __('Add Fields', 'contact') .'</a>
						<a href="'.bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields-reset/'.'" class="button">'. __('Restore Default Fields', 'contact') .'</a>						
					</span>';
		}
	}
	
	// Getting all extra fields for define group
	function get_all_fields($id){
		// get all fields
		$fields = array();
		$fields = groups_get_groupmeta($id, 'contact_fields');

		if (empty($fields)) {
			$fields = false;
		}else{
			$fields = json_decode($fields);
		}
		return $fields;
	}

	function remove_all_fields(){
		global $bp;		
		groups_delete_groupmeta($bp->groups->current_group->id, 'contact_fields');
	}
	
	function add_default_fields($group_id=NULL){
		global $bp;
		if($group_id==NULL){
                    $group_id=$bp->groups->current_group->id;
                }
		$group = new BP_Groups_Group( $group_id );
		$defaultfields = array();
		
		/* EDIT DEFAULT FIELDS HERE */		
		array_push($defaultfields, array('title'=>'Phone', 'desc'=>'','type'=>'text','required'=>0,'display'=>1, 'slug'=>'phone'));
		array_push($defaultfields, array('title'=>'E-mail','desc'=>'','type'=>'text','required'=>0,'display'=>1, 'slug'=>'e_mail'));
		array_push($defaultfields, array('title'=>'Twitter','desc'=>'','type'=>'text','required'=>0,'display'=>1, 'slug'=>'twitter'));
		array_push($defaultfields, array('title'=>'Mailing List','desc'=>'','type'=>'text','required'=>0,'display'=>1, 'slug'=>'mailing_list'));
		
		$fields = array();
		foreach($defaultfields as $field){
			$new = new Stdclass;
			$new->title = htmlspecialchars(strip_tags($field["title"]));
	                $new->slug = $field["slug"]; // will be used as unique identifier 
	                $new->desc = htmlspecialchars(strip_tags($field["desc"]));
			$new->type = $field["type"];
			$new->required = $field["required"];
			$new->display = $field["display"];
			
			// To the end of an array of current fields
			array_push($fields, $new);
		}
		$fields = json_encode($fields);
		groups_update_groupmeta( $group_id, 'contact_fields', $fields );		
	}	
	
	// Get field by slug - reusable
	function get_field_by_slug($slug){
		global $bp;
		// just in case...
		if (!is_string($slug))
			return false;
			
		$fields = $this->get_all_fields($bp->groups->current_group->id);
		foreach( $fields as $field ){
			if ( $slug == $field->slug )
				$searched = $field;
		}
		return $searched;
	}
	
	// Notices about user actions
	function notices($type){
		switch($type){
			case 'updated';
				bp_core_add_message(__('Group Extras settings were succefully updated.','contact'));
				break;
			case 'added';
				bp_core_add_message(__('New field was successfully added.','contact'));
				break;
			case 'edted';
				bp_core_add_message(__('The field was successfully updated.','contact'));
				break;
			case 'no-fields':
				echo '<div class="" id="message"><p>' . __('Please create at least 1 extra field.', 'contact') . '</p></div>';
				break;
		}
	}
	
	// Handle all ajax requests
	function ajax(){
		global $bp;
		$method = isset($_POST['method']) ? $_POST['method'] : '';
		
		switch($method){
			case 'reorder_fields':
				parse_str($_POST['field_order'], $field_order );
				$fields = $this->get_all_fields($bp->groups->current_group->id);

				// reorder all fields accordig new positions
				foreach($field_order['position'] as $u_slug){
					foreach($fields as $field){
						if ( $u_slug == str_replace('_', '', $field->slug) ){
							$new_order[] = $field;
							//break;
						}
					}
				}

				// Save new order into groupmeta table
				$new_order = json_encode($new_order);
				groups_update_groupmeta( $bp->groups->current_group->id, 'contact_fields', $new_order );
				die('saved');
				break;
				
			case 'delete_field':
				$fields = $this->get_all_fields($bp->groups->current_group->id);
				$left = array();
				// Delete all corresponding data
				foreach( $fields as $field ) {
					if ( str_replace('_', '', $field->slug) == $_POST['field'] ){
						groups_delete_groupmeta($bp->groups->current_group->id, $field->slug);
						continue;
					}
					array_push($left, $field);
				}
				// Save fields that were left
				$left = json_encode($left);
				groups_update_groupmeta($bp->groups->current_group->id, 'contact_fields', $left);
				die('deleted');
				break;
				
			default:
				die(1);
		}	
	}
	
	// Creation step - enter the data
	function create_screen() {
        if ( !bp_is_group_creation_step( $this->slug ) )
            return false;
        ?>
 	<label for="contact-phone">Phone</label>
	<input type="text" name="contact-phone" id="contact-phone" value="" ) />
	<label for="contact-email">E-mail</label>
	<input type="text" name="contact-e_mail" id="contact-e_mail" value="" ) />
	<label for="contact-twitter">Twitter</label>
	<input type="text" name="contact-twitter" id="contact-twitter" value="" ) />
	<label for="contact-mailing_list">Mailing List</label>
	<input type="text" name="contact-mailing_list" id="contact-mailing_list" value="" ) />
                
        <?php
        wp_nonce_field( 'groups_create_save_' . $this->slug );
	}

	// Creation step - save the data
	function create_screen_save() {
	global $bp;
	$this->add_default_fields();
	foreach($_POST as $data => $value){
		if ( substr($data, 0, 8) === 'contact-' ){
			$to_save[$data] =  $value;
		}

		foreach($to_save as $key => $value){
			$key = substr($key, 8);
			if ( ! is_array($value) ) {
				$value = wp_kses_data($value);
				$value = force_balance_tags($value);
			}
		groups_update_groupmeta($bp->groups->current_group->id, $key, $value);
		}
	}
}

function auto_link($text) {
  $pattern = "/(((http[s]?:\/\/)|(www\.))(([a-z][-a-z0-9]+\.)?[a-z][-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9._\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
  $text = preg_replace($pattern, " <a href='$1'>$1</a>", $text);
  // fix URLs without protocols
  $text = preg_replace("/href='www/", "href='http://www", $text);
  $text = preg_replace('~(?<!href="mailto:|>)(?>[\w.-]+@(?:[\w-]+\.){1,3}[a-z]{2,6})(?!">|</a>)~i','<a href="mailto:$0">$0</a>', $text);
  return $text;
}
	// Load if was not already loaded
	private static $instance = false;
    static function getInstance(){
        if(!self::$instance)
            self::$instance = new CONTACT;
        
        return self::$instance;
    }
}

bp_register_group_extension('CONTACT');

add_action('wp_ajax_contact', 'contact_ajax');
function contact_ajax(){
	$load = CONTACT::getInstance();
	$load->ajax();
}


