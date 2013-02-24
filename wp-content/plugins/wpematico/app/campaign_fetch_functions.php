<?php
// don't load directly
if ( !defined('ABSPATH') )
	die('-1');


if ( class_exists( 'wpematico_campaign_fetch_functions' ) ) return;

class wpematico_campaign_fetch_functions {

	function isDuplicate(&$campaign, &$feed, &$item) {
		// Agregar variables para chequear duplicados solo de esta campaña o de cada feed ( grabados en post_meta) o por titulo y permalink
		global $wpdb, $wp_locale, $current_blog;
		$table_name = $wpdb->prefix . "posts";
		$blog_id 	= @$current_blog->blog_id;
		
		$title = $wpdb->escape($item->get_title()); // $item->get_permalink();
		$query="SELECT post_title,id FROM $table_name
					WHERE post_title = '".$title."'
					AND ((`post_status` = 'published') OR (`post_status` = 'publish' ) OR (`post_status` = 'draft' ) OR (`post_status` = 'private' ))";
					//GROUP BY post_title having count(*) > 1" ;
		$row = $wpdb->get_row($query);
		
		trigger_error(sprintf(__('Checking duplicated title \'%1s\'', WPeMatico :: TEXTDOMAIN ),$title).': '.((!! $row) ? __('Yes') : __('No')) ,E_USER_NOTICE);
		return !! $row;
	}

		
	/**
   * Filters for skip item or not
   * @param   $current_item   array    Current post data to be saved
   * @param   $campaign       array    Current campaign data
   * @param   $feed           object    Feed database object
   * @param   $item           object    SimplePie_Item object
   *
   * Return TRUE if skip the item 
   */
   
	function exclude_filters(&$current_item, &$campaign, &$feed, &$item) {  
		$categories = @$current_item['categories'];
		$post_id = $this->campaign_id;
		$skip = false;

		if( $this->cfg['nonstatic'] ) { $skip = NoNStatic :: exclfilters($current_item,$campaign,$item ); }else $skip = false;
		
		return $skip;
	} // End exclude filters

  /**
   * Parses an item content
   *
   * @param   $current_item   array    Current post data to be saved
   * @param   $campaign       array    Current campaign data
   * @param   $feed           object    Feed database object
   * @param   $item           object    SimplePie_Item object
   */
	function Item_parsers(&$current_item, &$campaign, &$feed, &$item, &$count, &$feedurl ) {

		$post_id = $this->campaign_id;
		// Item title
		if( $this->cfg['nonstatic'] ) { $current_item = NoNStatic :: title($current_item,$campaign,$item,$count ); }else $current_item['title'] = esc_attr($item->get_title());
 		// Item author
		if( $this->cfg['nonstatic'] ) { $current_item = NoNStatic :: author($current_item,$campaign, $feedurl ); }else $current_item['author'] = $campaign['campaign_author'];			
		// Item content
		if( $this->cfg['nonstatic'] ) { $current_item = NoNStatic :: content($current_item,$campaign,$item); }else $current_item['content'] = $item->get_content();
		if($this->current_item == -1 ) return -1;
		if( $this->cfg['nonstatic'] ) { $current_item = NoNStatic :: content2($current_item,$campaign,$item); }else $current_item['content'] = $item->get_content();
		
		 // take out links before apply template
		if ($campaign['campaign_strip_links']){
			trigger_error(__('Cleaning Links from content.', WPeMatico :: TEXTDOMAIN ),E_USER_NOTICE);
			$current_item['content'] = $this->strip_links((string)$current_item['content']);
		}
		
		// Template parse           
		if ($campaign['campaign_enable_template']){
			trigger_error(__('Parsing Post template.', WPeMatico :: TEXTDOMAIN ),E_USER_NOTICE);
			$vars = array(
				'{content}',
				'{title}',
				'{image}',
				'{author}',
				'{authorlink}',
				'{permalink}',
				'{feedurl}',
				'{feedtitle}',
				'{feeddescription}',
				'{feedlogo}',
				'{campaigntitle}',
				'{campaignid}'
			);

			$autor="";
			if ($author = $item->get_author())	{
				$autor = $author->get_name();
				$autorlink = $author->get_link();
			}		

			$replace = array(
				$current_item['content'],
				$current_item['title'],
				"[[[wpe1stimg]]]",
				$autor,
				$autorlink,
				$item->get_link(),
				$feed->feed_url,
				$feed->get_title(),
				$feed->get_description(),
				$feed->get_image_url(),
				get_the_title($post_id),
				$post_id
			);

			$current_item['content'] = str_ireplace($vars, $replace, ( $campaign['campaign_template'] ) ? stripslashes( $campaign['campaign_template'] ) : '{content}');
		}
	
	 // Rewrite
		//$rewrites = $campaign['campaign_rewrites'];
		if (isset($campaign['campaign_rewrites']['origin']))
			for ($i = 0; $i < count($campaign['campaign_rewrites']['origin']); $i++) {
				$origin = stripslashes($campaign['campaign_rewrites']['origin'][$i]);
				if(isset($campaign['campaign_rewrites']['rewrite'][$i])) {
					$reword = !empty($campaign['campaign_rewrites']['relink'][$i]) 
								  ? '<a href="'. stripslashes($campaign['campaign_rewrites']['relink'][$i]) .'">' . stripslashes($campaign['campaign_rewrites']['rewrite'][$i]) . '</a>' 
								  : stripslashes($campaign['campaign_rewrites']['rewrite'][$i]);
				  
					if($campaign['campaign_rewrites']['regex'][$i]) {
						$current_item['content'] = preg_replace($origin, $reword, $current_item['content']);
					}else
						$current_item['content'] = str_ireplace($origin, $reword, $current_item['content']);
				}else if(!empty($campaign['campaign_rewrites']['relink'][$i]))
					$current_item['content'] = str_ireplace($origin, '<a href="'. stripslashes($campaign['campaign_rewrites']['relink'][$i]) .'">' . $origin . '</a>', $current_item['content']);
			}
		// End rewrite

		if ( !$this->cfg['disable_credits']) {$current_item['content'] .= '<p class="wpematico_credit"><small>Powered by <a href="http://www.wpematico.com" target="_blank">WPeMatico</a></small></p>'; }
		
		return $current_item;
	} // End ParseItemContent
	
	/**
   * Filters an item content
   * @param   $current_item   array    Current post data to be saved
   * @param   $campaign       array    Current campaign data
   * @param   $feed           object    Feed database object
   * @param   $item           object    SimplePie_Item object
   */
   
	function Item_filters(&$current_item, &$campaign, &$feed, &$item) {  
		$categories = $current_item['categories'];
		$post_id = $this->campaign_id;
		
		//Proceso Words to Category y si hay las agrego al array
		if ( $this->cfg['enableword2cats']) {
			trigger_error(sprintf(__('Processing Words to Category %1s', WPeMatico :: TEXTDOMAIN ), $current_item['title'] ),E_USER_NOTICE);
			//$wrd2cats = $campaign['campaign_wrd2cat'];
			if (isset($campaign['campaign_wrd2cat']['word']))
				for ($i = 0; $i < count($campaign['campaign_wrd2cat']['word']); $i++) {
					$foundit = false;
					$word = stripslashes(@$campaign['campaign_wrd2cat']['word'][$i]);
					if(isset($campaign['campaign_wrd2cat']['w2ccateg'][$i])) {
						$tocat = $campaign['campaign_wrd2cat']['w2ccateg'][$i];
						if($campaign['campaign_wrd2cat']['regex'][$i]) {
							$foundit = (preg_match($word, $current_item['content'])) ? true : false; 
						}else{
							if($campaign['campaign_wrd2cat']['cases'][$i]) 
								$foundit = strpos($current_item['content'], $word);
							else $foundit = stripos($current_item['content'], $word); //insensible a May/min
						}
						if ($foundit !== false ) {
							trigger_error(sprintf(__('Found!: word %1s to Cat_id %2s', WPeMatico :: TEXTDOMAIN ),$word,$tocat),E_USER_NOTICE);
							$current_item['categories'][] = $tocat;
						}else{
							trigger_error(sprintf(__('Not found word %1s', WPeMatico :: TEXTDOMAIN ),$word),E_USER_NOTICE);
						}
					}
				}
		}	// End Words to Category
		//Tags
		if( $this->cfg['nonstatic'] ) { $current_item = NoNStatic :: postags($current_item,$campaign, $item ); }else $current_item['tags'] = explode(',', $campaign['tags']);

		return $current_item;
	} // End item filters
    
    /**
     * Get relative path
     * @param $baseUrl base url
     * @param $relative relative url
     * @return absolute url version of relative url
     */
    function getRelativeUrl($baseUrl, $relative){
        $schemes = array('http', 'https', 'ftp');
        foreach($schemes as $scheme){
            if(strpos($relative, "{$scheme}://") === 0) //if not relative
                return $relative;
        }
        
        $urlInfo = parse_url($baseUrl);
        
        $basepath = $urlInfo['path'];
        $basepathComponent = explode('/', $basepath);
        $resultPath = $basepathComponent;
        $relativeComponent = explode('/', $relative);
        $last = array_pop($relativeComponent);
        foreach($relativeComponent as $com){
            if($com === ''){
                $resultPath = array('');
            } else if ($com == '.'){
                $cur = array_pop($resultPath);
                if($cur === ''){
                    array_push($resultPath, $cur);
                } else {
                    array_push($resultPath, '');
                }
            } else if ($com == '..'){
                if(count($resultPath) > 1)
                    array_pop($resultPath);
                array_pop($resultPath);
                array_push($resultPath, '');
            } else {
                if(count($resultPath) > 1)
                    array_pop($resultPath);
                array_push($resultPath, $com);
                array_push($resultPath, '');
            }
        }
        array_pop($resultPath);
        array_push($resultPath, $last);
        $resultPathReal = implode('/', $resultPath);
        return $urlInfo['scheme'] . '://' . $urlInfo['host'] . $resultPathReal;
    }
    
    function getReadUrl($url){
        $headers = get_headers($url);
        foreach($headers as $header){
            $parts = explode(':', $header, 2);
            if(strtolower($parts[0]) == 'location')
                return trim($parts[1]);
        }
        return $url;
    }
	
 	/**
   * Filters images, upload and replace on text item content
   * @param   $current_item   array    Current post data to be saved
   * @param   $campaign       array    Current campaign data
   * @param   $feed           object    Feed database object
   * @param   $item           object    SimplePie_Item object
   */
	function Item_images(&$current_item, &$campaign, &$feed, &$item) { 
        
		if($this->cfg['imgcache'] || $campaign['campaign_imgcache']) {
			$images = $this->parseImages($current_item['content']);
			$current_item['images'] = $images[2];  //lista de url de imagenes
            $itemUrl = $this->getReadUrl($item->get_permalink());
			
			if( $this->cfg['nonstatic'] ) { $current_item['images'] = NoNStatic :: imgfind($current_item,$campaign,$item ); }
			
			if( sizeof($current_item['images']) ) { // Si hay alguna imagen en el contenido
				trigger_error(__('Uploading images.', WPeMatico :: TEXTDOMAIN ),E_USER_NOTICE);
				//trigger_error(print_r($current_item['images'],true),E_USER_NOTICE);
				$img_new_url = array();
				foreach($current_item['images'] as $imagen_src) {
				    trigger_error(__('Uploading image...', WPeMatico :: TEXTDOMAIN ).$imagen_src,E_USER_NOTICE);
					if($this->campaign['campaign_cancel_imgcache']) {
						if($this->cfg['gralnolinkimg'] || $this->campaign['campaign_nolinkimg']) 
							$current_item['content'] = str_replace($imagen_src, '', $current_item['content']);  // Si no quiere linkar las img al server borro el link de la imagen						
					}else {
						//$imagen_src = str_replace('_s.','_n.', $imagen_src);  //Trae la imagen grande en vez del thumb
                        $imagen_src_real = $this->getRelativeUrl($itemUrl, $imagen_src);
						$bits = @file_get_contents($imagen_src_real);
						$name = str_replace(array(' ','%20'),'_',substr(strrchr($imagen_src, "/"),1));
						$afile = wp_upload_bits( $name, NULL, $bits);
						if(!$afile['error']) {
							trigger_error($afile['url'],E_USER_NOTICE);
							$current_item['content'] = str_replace($imagen_src, $afile['url'], $current_item['content']);
							$img_new_url[] = $afile['url'];
						} else {  // Si no la pudo subir intento con mi funcion
							trigger_error('wp_upload_bits error:'.print_r($afile,true).', trying custom function.',E_USER_WARNING);
							$upload_dir = wp_upload_dir();
							$imagen_dst = $upload_dir['path'] . str_replace('/','',strrchr($imagen_src, '/'));
							$imagen_dst_url = $upload_dir['url']. '/' . str_replace('/','',strrchr($imagen_src, '/'));

							if(in_array(str_replace('.','',strrchr($imagen_dst, '.')),explode(',','jpg,gif,png,tif,bmp'))) {   // -------- Controlo extensiones permitidas
								trigger_error('imagen_src='.$imagen_src.' <b>to</b> imagen_dst='.$imagen_dst.'<br>',E_USER_NOTICE);
								$newfile = $this->guarda_imagen($imagen_src_real, $imagen_dst);
								if($newfile) {	
									$current_item['content'] = str_replace($imagen_src, $imagen_dst_url, $current_item['content']);
									$img_new_url[] = $imagen_dst_url;
								} else {
									if($this->cfg['gralnolinkimg'] || $this->campaign['campaign_nolinkimg']) $current_item['content'] = str_replace($imagen_src, '', $current_item['content']);  // Si no quiere linkar las img al server borro el link de la imagen
									trigger_error('Upload file failed:'.$imagen_dst,E_USER_WARNING);
								}
							}else {
								trigger_error('Extension not allowed:'.$imagen_dst,E_USER_WARNING);
							}
						}
					}
				}
				$current_item['images'] = (array)$img_new_url;
			}  // // Si hay alguna imagen en el contenido
		}		
		return $current_item;		
	}  // item images


	function Item_parseimg(&$current_item, &$campaign, &$feed, &$item) {
		if ( preg_match("[[[wpe1stimg]]]", $current_item['content']) ) {  // en el content
			$imgenc = $current_item['images'][0];
			$imgstr = "<img class=\"wpe_imgrss\" src=\"" . $imgenc . "\">";  //Solo la imagen

			$current_item['content'] = str_ireplace("[[[wpe1stimg]]]",$imgstr, $current_item['content']);
		}
		return $current_item;
	}



	/*** Devuelve todas las imagenes del contenido	*/
	function parseImages($text){    
		preg_match_all('/<img(.+?)src=\"(.+?)\"(.*?)>/', $text, $out);  //for tag img
		preg_match_all('/<link rel=\"(.+?)\" type=\"image\/jpg\" href=\"(.+?)\"(.+?)\/>/', $text, $out2); // for rel=enclosure
		array_push($out,$out2);  // sum all items to array 
		return $out;
	}
 
	
	/*** Adjunta un archivo ya subido al postid dado  */
 	function insertfileasattach($filename,$postid) {
  		$wp_filetype = wp_check_filetype(basename($filename), null );
		$wp_upload_dir = wp_upload_dir();
		$attachment = array(
		  'guid' => $filename, 
		  'post_mime_type' => $wp_filetype['type'],
		  'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
		  'post_content' => '',
		  'post_status' => 'inherit'
		);
		trigger_error(__('Attaching file:').$filename,E_USER_NOTICE);
		$relfilename=str_replace($wp_upload_dir['baseurl'], '', $filename);
//		trigger_error(__('Attaching file relfilename:').$relfilename,E_USER_NOTICE);
		$attach_id = wp_insert_attachment( $attachment,  $relfilename, $postid );
		if (!$attach_id)
			trigger_error(__('Sorry, your attach could not be inserted. Something wrong happened.').print_r($filename,true),E_USER_WARNING);
		// must include the image.php file for the function wp_generate_attachment_metadata() to work
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id,  $attach_data );
		
		return $attach_id;
	}

	
 
 	/* Guardo imagen en mi servidor
	EJEMPLO 
	guarda_imagen("http://ablecd.wz.cz/vendeta/fuhrer/hitler-pretorians.jpg","/usr/home/miweb.com/web/iimagen.jpg");
	Si el archivo destino ya existe guarda una copia de la forma "filename[n].ext"
	***************************************************************************************/
	function guarda_imagen ($url_origen,$archivo_destino){ 
		$mi_curl = curl_init ($url_origen); 
		if(!$mi_curl) {
			return false;
		}else{
			$i = 1;
			while (file_exists( $archivo_destino )) {
				$file_extension  = strrchr($archivo_destino, '.');    //Will return .JPEG         substr($url_origen, strlen($url_origen)-4, strlen($url_origen));
				$file_name = substr($archivo_destino, 0, strlen($archivo_destino)-strlen($file_extension));
				$archivo_destino = $file_name."[$i]".$file_extension;
				$i++;
			}
			$fs_archivo = fopen ($archivo_destino, "w"); 
//			if(is_writable($fs_archivo)) {
				curl_setopt ($mi_curl, CURLOPT_FILE, $fs_archivo); 
				curl_setopt ($mi_curl, CURLOPT_HEADER, 0); 
				curl_exec ($mi_curl); 
				curl_close ($mi_curl); 
				fclose ($fs_archivo); 
				return $archivo_destino;
//			}
		}
	} 

	function strip_links($text) {
	    $tags = array('a','iframe','script');
	    foreach ($tags as $tag){
	        while(preg_match('/<'.$tag.'(|\W[^>]*)>(.*)<\/'. $tag .'>/iusU', $text, $found)){
	            $text = str_replace($found[0],$found[2],$text);
	        }
	    }
	    return preg_replace('/(<('.join('|',$tags).')(|\W.*)\/>)/iusU', '', $text);
	}

} // class