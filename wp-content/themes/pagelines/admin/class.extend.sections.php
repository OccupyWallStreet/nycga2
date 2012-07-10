<?php

class ExtensionSections extends PageLinesExtensions {
	
	/**
	 * Section install tab.
	 * 
	 */
 	function extension_sections( $tab = '', $mode = 'install' ) {
 		
		if($tab == 'child' && !is_child_theme())
			return $this->ui->extension_banner( __( 'A PageLines child theme is not currently activated', 'pagelines' ) );
	
		if ( !$this->has_extend_plugin() )
			return $this->ui->get_extend_plugin( $this->has_extend_plugin('status'), $tab );

		$list = array();
		$type = 'section';
				
		if ( 'install' == $mode ) {
			$sections = $this->get_latest_cached( 'sections' );

			if ( !is_object( $sections ) ) 
				return $sections;

			$sections = pagelines_store_object_sort( $sections );

			$list = $this->get_master_list( $sections, $type, $tab );
			
		} // end install mode
		
		if ( 'installed' == $mode ) {
			
			global $load_sections;
			
			// Get sections
			
	 		$available = $load_sections->pagelines_register_sections( true, true );

	 		$disabled = get_option( 'pagelines_sections_disabled', array() );

			$upgradable = $this->get_latest_cached( 'sections' );

	 		foreach( $available as $key => $section ) {

				$available[$key] = self::sort_status( $section, $disabled, $available, $upgradable );
			}
			
			$sections = self::merge_sections( $available );			

			$this->updates_list( array( 'list' => $sections, 'type' => 'section' ) );

			$list = $this->get_master_list( $sections, $type, $tab, 'installed' );	
	
		} // end installed mode
		
		
		
		return $this->ui->extension_list( array( 'list' => $list, 'tab' => $tab, 'type' => 'sections' ) );
 	}


	/**
	*
	* @TODO document
	*
	*/
	function merge_sections( $sections ) {
		
		$out = array();
		
		foreach ( $sections as $key => $section) {
			
			$out = array_merge( $out, $sections[$key] );
		}
		
		return $out;
	}


	/**
	*
	* @TODO document
	*
	*/
	function sort_status( $section, $disabled, $available, $upgradable) {
		
		if (! is_array( $section ) )
			return;
		foreach( $section as $key => $ext) {
			$section[$key]['status'] = ( isset( $disabled[ $ext['type'] ][ $ext['class'] ] ) ) ? 'disabled' : 'enabled';
			$section[$key] = self::check_version( $section[$key], $upgradable );
			$section[$key]['class_exists'] = ( isset( $available['child'][ $ext['class'] ] ) || isset( $available['custom'][ $ext['class'] ] ) ) ? true : false;
			
			$slug = basename( $ext['base_dir'] );
			$section[$key]['subscribed'] = ( isset( $upgradable->$slug->subscribed ) ) ? $upgradable->$slug->subscribed : null;
			$section[$key]['pid'] = ( isset( $upgradable->$slug->productid ) ) ? $upgradable->$slug->productid : null;
		}

		return pagelines_array_sort( $section, 'name' ); // Sort Alphabetically
	}


	/**
	*
	* @TODO document
	*
	*/
	function check_version( $ext, $upgradable ) {
		
		if ( isset( $ext['base_dir'] ) ) {
			$upgrade = basename( $ext['base_dir'] );
			if ( isset( $upgradable->$upgrade->version ) ) {
				$ext['apiversion'] = ( isset( $upgradable->$upgrade->version ) ) ? $upgradable->$upgrade->version : '';
				$ext['slug'] = $upgradable->$upgrade->slug;
			}
		}
		return $ext;
	}
}
