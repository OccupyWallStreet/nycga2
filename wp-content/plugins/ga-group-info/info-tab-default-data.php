<?php
	function gait_field( $name, $type = 'single-line' ) {
		return array(
			'name' =>  $name,
			'value' => '',
			'active' => false,
			'type' => $type
		);
	}
	
	function gait_default_fields(){
		$data = array(
			'email' => gait_field( 'Email Address' ),
			'phone' => gait_field( 'Group Phone' ),
			'twitter' => gait_field( 'Twitter' ),
			'facebook' => gait_field( 'Facebook' ),
			'website' => gait_field( 'External Website' ),
			'listserve' => gait_field( 'List Serve' ),
			'description' => gait_field( 'Detailed Description / Charter' ),
			'contact' => gait_field( 'Contact Phone' )
		);
		$data['description']['type'] = 'wysiwyg';
		foreach ($data as &$field){
			$field['active'] = true;
		}
		return $data;
	}
