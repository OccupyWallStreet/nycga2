<?php

function get_upload_field($id, $std = '', $desc = '') {

  $field = '<input id="' . $id . '" type="file" name="attachment_' . $id . '" />' .
           '<span class="submit"><input name="Detox_upload" type="submit" value="Upload" class="button panel-upload-save" />
		   </span> <span class="description"> '. __($desc,'Detox') .' </span>';

  return $field;
}

load_theme_textdomain('Detox');
class Detox {
	function addOptions () {
	
		if (isset($_POST['Detox_reset'])) { Detox::initOptions(true); }
		if (isset($_POST['Detox_save'])) {

			$aOptions = Detox::initOptions(false);
		
		  $aOptions['featured11-image'] = stripslashes($_POST['featured11-image']); 
		  $aOptions['featured111-image'] = stripslashes($_POST['featured111-image']);
		  $aOptions['featured1111-image'] = stripslashes($_POST['featured1111-image']);
		  
			$aOptions['featured1-image'] = stripslashes($_POST['featured1-image']);
			$aOptions['featured1-link'] = stripslashes($_POST['featured1-link']);
			
			$aOptions['featured2-image'] = stripslashes($_POST['featured2-image']);
			$aOptions['featured2-link'] = stripslashes($_POST['featured2-link']);
			
			$aOptions['featured3-image'] = stripslashes($_POST['featured3-image']);
			$aOptions['featured3-link'] = stripslashes($_POST['featured3-link']);
			
			$aOptions['featured4-image'] = stripslashes($_POST['featured4-image']);
			$aOptions['featured4-link'] = stripslashes($_POST['featured4-link']);
			
			$aOptions['featured5-image'] = stripslashes($_POST['featured5-image']);
			$aOptions['featured5-link'] = stripslashes($_POST['featured5-link']);
			
			$aOptions['featured6-image'] = stripslashes($_POST['featured6-image']);
			$aOptions['featured6-link'] = stripslashes($_POST['featured6-link']);
			
			$aOptions['featured8-title'] = stripslashes($_POST['featured8-title']);
			$aOptions['featured8-link'] = stripslashes($_POST['featured8-link']);
			
			$aOptions['featured9-title'] = stripslashes($_POST['featured9-title']);
			$aOptions['featured9-link'] = stripslashes($_POST['featured9-link']);
			
			$aOptions['featured99-title'] = stripslashes($_POST['featured99-title']);
			$aOptions['featured99-link'] = stripslashes($_POST['featured99-link']);
			
			$aOptions['featured999-title'] = stripslashes($_POST['featured999-title']);
			$aOptions['featured999-link'] = stripslashes($_POST['featured999-link']);
			
			update_option('Detox_theme', $aOptions);
		}
		if (isset($_POST['Detox_upload'])) {

			$aOptions = Detox::initOptions(false);

            $whitelist = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
			
			if (!$_FILES['attachment_featured1-image']['type']=='') { 
				$up_file = 'featured1-image'; 
			}
			if (!$_FILES['attachment_featured11-image']['type']=='') { 
				$up_file = 'featured11-image'; 
			}
			if (!$_FILES['attachment_featured111-image']['type']=='') { 
				$up_file = 'featured111-image'; 
			}
			if (!$_FILES['attachment_featured1111-image']['type']=='') { 
				$up_file = 'featured1111-image'; 
			}
			
			if (!$_FILES['attachment_featured2-image']['type']=='') { 
				$up_file = 'featured2-image'; 
			}
			
			if (!$_FILES['attachment_featured3-image']['type']=='') { 
				$up_file = 'featured3-image'; 
			}
			
			if (!$_FILES['attachment_featured4-image']['type']=='') { 
				$up_file = 'featured4-image'; 
			}
			
			if (!$_FILES['attachment_featured5-image']['type']=='') { 
				$up_file = 'featured5-image'; 
			}
				if (!$_FILES['attachment_featured6-image']['type']=='') { 
				$up_file = 'featured6-image'; 
			}
			
            $filetype = $_FILES['attachment_' . $up_file]['type'];

            if (in_array($filetype, $whitelist)) {
              $upload = wp_handle_upload($_FILES['attachment_' . $up_file], array('test_form' => false));
			  $aOptions[$up_file] = stripslashes($upload['url']);
			  update_option('Detox_theme', $aOptions);
            }
		}
		add_theme_page("Detox Custom Options", "Detox Custom Options", 'edit_themes', basename(__FILE__), array('Detox', 'displayOptions'));
	}
	function initOptions ($bReset) {
		$aOptions = get_option('Detox_theme');
		if (!is_array($aOptions) || $bReset) {

      $aOptions['featured11-image'] = 'http://dl.dropbox.com/u/1933107/themes/detox/detoxlogo.jpg';
			$aOptions['featured111-image'] = 'http://dl.dropbox.com/u/1933107/themes/detox/favicon.png';
			$aOptions['featured1111-image'] = 'http://dl.dropbox.com/u/1933107/themes/detox/bge.gif';
			
      $aOptions['featured1-image'] = 'http://img20.imageshack.us/img20/199/faviconj.png';
			$aOptions['featured1-link'] = 'http://milo317.com/';
			
			$aOptions['featured2-image'] = 'http://img20.imageshack.us/img20/199/faviconj.png';
			$aOptions['featured2-link'] = 'http://milo317.com/';
		
			$aOptions['featured3-image'] = 'http://img20.imageshack.us/img20/199/faviconj.png';
			$aOptions['featured3-link'] = 'http://milo317.com/';
			
			$aOptions['featured4-image'] = 'http://img20.imageshack.us/img20/199/faviconj.png';
			$aOptions['featured4-link'] = 'http://milo317.com/';
			
			$aOptions['featured5-image'] = 'http://img20.imageshack.us/img20/199/faviconj.png';
			$aOptions['featured5-link'] = 'http://milo317.com/';
		
		  $aOptions['featured6-image'] = 'http://img20.imageshack.us/img20/199/faviconj.png';
			$aOptions['featured6-link'] = 'http://milo317.com/';
			
      $aOptions['featured8-title'] = 'FaceBook';
			$aOptions['featured8-link'] = 'http://www.facebook.com/milo317';
			
			$aOptions['featured9-title'] = 'Twitter';
			$aOptions['featured9-link'] = 'http://twitter.com/milo317';
			
			$aOptions['featured99-title'] = 'FlickR';
			$aOptions['featured99-link'] = 'http://www.flickr.com/photos/milo3oneseven/';
			
			$aOptions['featured999-title'] = 'YouTube';
			$aOptions['featured999-link'] = 'http://www.flickr.com/photos/milo3oneseven/';
           					
			update_option('Detox_theme', $aOptions);
		}
		return $aOptions;
	}
	function displayOptions () {
		$aOptions = Detox::initOptions(false);
?>
<div class="wrap">
	<h2>Detox Theme Options</h2>
	      
    <div style="margin-left:0px;">
    
     <div id="sideblock" style="float:right;width:220px;margin-left:10px;"> 
     <h3>Information</h3>
     <div id="dbx-content" style="text-decoration:none;">       
 			 <img src="<?php bloginfo('stylesheet_directory'); ?>/images/milo.png" /><a style="text-decoration:none;" href="http://3oneseven.com/"> 3oneseven</a><br /><br />
			 <img src="<?php bloginfo('stylesheet_directory'); ?>/images/32.png" /><a style="text-decoration:none;" href="http://feeds2.feedburner.com/milo317"> Stay updated</a><br /><br />		 	 
			 <img src="<?php bloginfo('stylesheet_directory'); ?>/images/more.png" /><a style="text-decoration:none;" href="http://wp.milo317.com"> Cool themes by milo317</a><br /><br />
			 <img src="<?php bloginfo('stylesheet_directory'); ?>/images/twit.png" /><a style="text-decoration:none;" href="http://twitter.com/milo317"> Follow updates on Twitter</a><br /><br />			
			 <img src="<?php bloginfo('stylesheet_directory'); ?>/images/idea.png" /><a style="text-decoration:none;" href="http://3oneseven.com/contact/"> Get special customization.</a>
</div>
</div>

			<h2>New DetoX theme activated, congrats.</h2>
                
				<h4>Twitter auto posting:</h4>
				<p>
        Locate header.php at /wp-content/detox/,<br />
				locate line 94 and edit the twitter name (milo317) according to yours.<br />
        or use simply the wtitter widget.
        </p>
				
        <h4>Advertising areas</h4>
        <p>Go to the theme options at widgets,<br /> 
        <strong>use your ad widget code</strong></p>
        
        <h4>Category & Slider items</h4>
        <p>Go to the theme options at Appearance,<br />
        select your categories, please keep in mind that you need at least 5 posts for the slider categories to work.</p>
        
        <h4>Widgets</h4>
        <p>Front, all sidebars, ad sections and footer columns are fully widgetized.</p>
<p>Need more help? Contact milo317 via her <a href="http://forum.milo317.com">forum</a>.</p> 

    <form action="#" method="post" enctype="multipart/form-data" name="massive_form" id="massive_form">
		<fieldset name="general_options" class="options">
		        	        
 <div style="border-bottom:1px solid #333;"></div>
     <h3 style="margin-bottom:0px;">Options:</h3>
     <p style="margin-top:0px;">You can add link and images by filling out the fields below.</p>

<h3>Favicon</h3>
       Add your favicon Location:<br />
		<div style="margin:0;padding:0;">
        <input name="featured111-image" id="featured111-image" size="99" value="<?php echo($aOptions['featured111-image']); ?>"></input> 
        </div><br />

        or upload your favicon:<br />
		<tr valign="top" class="upload <?php echo ($aOptions['featured111-image']); ?>">
          <th scope="row"><label for="<?php echo ($aOptions['featured111-image']); ?>"><?php echo __($value['name'],'Detox'); ?></label></th>
          <td>
		  <div id='imgupload'>
            <?php print get_upload_field('featured111-image', '', $aOptions['desc']); ?>
			</div>
          </td>
        </tr> 

<h3>Logo</h3>
       Add your logo image Location (<b>Max width & height:100px X 265px</b>):<br />
		<div style="margin:0;padding:0;">
        <input name="featured11-image" id="featured11-image" size="99" value="<?php echo($aOptions['featured11-image']); ?>"></input> 
        </div><br />

        or upload your logo:<br />
		<tr valign="top" class="upload <?php echo ($aOptions['featured11-image']); ?>">
          <th scope="row"><label for="<?php echo ($aOptions['featured11-image']); ?>"><?php echo __($value['name'],'Detox'); ?></label></th>
          <td>
		  <div id='imgupload'>
            <?php print get_upload_field('featured11-image', '', $aOptions['desc']); ?>
			</div>
          </td>
        </tr>
        
<h3>Background image</h3>
       Add your logo image Location (<b>Max width & height:80px X 30px</b>):<br />
		<div style="margin:0;padding:0;">
        <input name="featured1111-image" id="featured1111-image" size="99" value="<?php echo($aOptions['featured1111-image']); ?>"></input> 
        </div><br />

        or upload your logo:<br />
		<tr valign="top" class="upload <?php echo ($aOptions['featured1111-image']); ?>">
          <th scope="row"><label for="<?php echo ($aOptions['featured1111-image']); ?>"><?php echo __($value['name'],'Detox'); ?></label></th>
          <td>
		  <div id='imgupload'>
            <?php print get_upload_field('featured1111-image', '', $aOptions['desc']); ?>
			</div>
          </td>
        </tr>  
             
        <div style="border-bottom:1px solid #333;"></div> 
           
      <h3>FaceBook</h3>
      Add your social links, example: FaceBook
        <div style="margin:0;padding:0;">
        <input name="featured8-title" id="featured8-title" value="<?php echo($aOptions['featured8-title']); ?>"></input>
        </div><br />
        
        Links To:<br />
		<div style="margin:0;padding:0;">
        <input name="featured8-link" id="featured8-link" size="99" value="<?php echo($aOptions['featured8-link']); ?>"></input>   
        </div><br /> 
         
     <h3>Twitter</h3>
        Add your social links, example: Twitter
        <div style="margin:0;padding:0;">
        <input name="featured9-title" id="featured9-title" value="<?php echo($aOptions['featured9-title']); ?>"></input>
        </div><br />
        
        Links To:<br />
		<div style="margin:0;padding:0;">
        <input name="featured9-link" id="featured9-link" size="99" value="<?php echo($aOptions['featured9-link']); ?>"></input>   
        </div><br /> 
                   
     <h3>FlickR</h3>
         Add your social links, example: FlickR
        <div style="margin:0;padding:0;">
        <input name="featured99-title" id="featured99-title" value="<?php echo($aOptions['featured99-title']); ?>"></input>
        </div><br />
        
        Links To:<br />
		<div style="margin:0;padding:0;">
        <input name="featured99-link" id="featured99-link" size="99" value="<?php echo($aOptions['featured99-link']); ?>"></input>   
        </div><br /> 
              
       <h3>YouTube</h3>
        Add your social links, example: YouTube
        <div style="margin:0;padding:0;">
        <input name="featured999-title" id="featured999-title" value="<?php echo($aOptions['featured999-title']); ?>"></input>
        </div><br />
        
        Links To:<br />
		<div style="margin:0;padding:0;">
        <input name="featured999-link" id="featured999-link" size="99" value="<?php echo($aOptions['featured999-link']); ?>"></input>   
        </div><br /> 
                                 
        <div style="border-bottom:1px solid #333;"></div>
                                
<div style="border-bottom:1px solid #333;"></div>
		</fieldset>
		<p class="submit"><input type="submit" name="Detox_reset" value="Reset" /></p>
		<p class="submit"><input type="submit" name="Detox_save" value="Save" /></p>
	</form>      
</div>
<?php
	}
}
// Register functions
add_action('admin_menu', array('Detox', 'addOptions'));
?>