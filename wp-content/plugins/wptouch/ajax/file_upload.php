<?php
	$max_size = 128*4096; // 512k	
	$directory_list = array();
	
	if ( current_user_can( 'upload_files' ) ) {
		check_ajax_referer( 'wptouch-upload' );
		$upload_dir = compat_get_upload_dir() . "/wptouch/custom-icons" . ltrim( $dir[1], '/' );
		$dir_paths = explode( '/', $upload_dir );
		$dir = '';
		foreach ( $dir_paths as $path ) {
			$dir = $dir . "/" . $path;
			if ( !file_exists( $dir ) ) {
				@mkdir( $dir, 0755 ); 
			}			
		}
		
		if ( isset( $_FILES['submitted_file'] ) ) {
			$f = $_FILES['submitted_file'];
			if ( $f['size'] <= $max_size) {
				if ( $f['type'] == 'image/png' || $f['type'] == 'image/jpeg' || $f['type'] == 'image/gif' || $f['type'] == 'image/x-png' || $f['type'] == 'image/pjpeg' ) {	
					@move_uploaded_file( $f['tmp_name'], $upload_dir . "/" . $f['name'] );
					
					if ( !file_exists( $upload_dir . "/" . $f['name'] ) ) {
						echo __('<p style="color:red; padding-top:10px">There seems to have been an error.<p>Please try your upload again.</p>', 'wptouch' );
					} else {
						echo  __( '<p style="color:green; padding-top:10px">File has been saved and added to the pool.</p>', 'wptouch' );							
					}					
				} else {
					echo __( '<p style="color:orange; padding-top:10px">Sorry, only PNG, GIF and JPG images are supported.</p>', 'wptouch' );
				}
			} else echo __( '<p style="color:orange; padding-top:10px">Image too large. try something like 59x60.</p>', 'wptouch' );
		}
	} else echo __( '<p style="color:orange; padding-top:10px">Insufficient privileges.</p><p>You need to either be an admin or have more control over your server.</p>', 'wptouch' );