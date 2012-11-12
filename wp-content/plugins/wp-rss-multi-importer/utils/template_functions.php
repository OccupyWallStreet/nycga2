<?php


function wprssmi_ajax_process_request() {
	
	if ($_POST["restore_var"]==1){
		//echo $_POST["restore_var"];
		restore_template();
		
		echo "Your templates have been restored.  Go back to the settings options tab and select your template.";
		die();
		}else{
		if ($_POST["save_var"]==2){	
			delete_option('rss_template_item');	
		save_template_function($_POST['post_var']);
		die();	
			
		}else{
		if ($_POST["save_var"]==3){		
				save_css();
			die();		
			
			}
		}
	}	
			

	
	
	// first check if data is being sent and that it is the data we want
  	if ( !empty( $_POST["post_var"] ) && !empty( $_POST["post_name"] ) ) {

$templateCSSname='templates.css';
$templatename=$_POST['post_var'];
$newtemplatename=$_POST['post_name'];
$templateCODE=file_get_contents(WP_RSS_MULTI_TEMPLATES . $templatename);
$templateCSS=file_get_contents(WP_RSS_MULTI_TEMPLATES . $templateCSSname);
$newtemplatename = str_replace(' ', '_', $newtemplatename).'.php';


//now, put in DB


$myTemplateOptions = array(
'template_name' => $newtemplatename,
'template_code' => $templateCODE,
'template_css'=> $templateCSS
);
delete_option('rss_template_item');
add_option('rss_template_item', $myTemplateOptions);

echo "<p>SUCCESS. Your template has been stored in the database.  After the next update of the plugin, simply come back to this page, hit RESTORE, and you'll have access to the same template again.</p><p>If you make changes to your template, just return to this page and save it again. It's that simple.</p>";


	}else{
		
	save_css();
	
//$templateCSSname='templates.css';
//$templateCSS=file_get_contents(WP_RSS_MULTI_TEMPLATES . $templateCSSname);		
//$myTemplateOptions = array(
//	'template_name' => 0,
//	'template_code' => 0,
//	'template_css'=> $templateCSS
//		);
	//	delete_option('rss_template_item');
	//	add_option('rss_template_item', $myTemplateOptions);
		
//echo 'Only the CSS template was saved.';
}

	die();
	

}
add_action('wp_ajax_wprssmi_response', 'wprssmi_ajax_process_request');




function save_css(){
	
	$templateCSSname='templates.css';
	$templateCSS=file_get_contents(WP_RSS_MULTI_TEMPLATES . $templateCSSname);		
	$myTemplateOptions = array(
		'template_name' => 0,
		'template_code' => 0,
		'template_css'=> $templateCSS
			);
			delete_option('rss_template_item');
			add_option('rss_template_item', $myTemplateOptions);

	echo 'Only the CSS template was saved.';
	
	
}





function view_template(){
	
	$templateOptions=get_option('rss_template_item');
	
	echo $templateOptions['template_code'];
	
}


function restore_template(){
	
	$path=str_replace('utils','templates',plugin_dir_path( __FILE__));	
	$templateOptions=get_option('rss_template_item');
	

	
	if ($templateOptions['template_name']!=0 && $templateOptions['template_code']!=0){
	
	file_put_contents(WP_RSS_MULTI_TEMPLATES .$templateOptions['template_name'], $templateOptions['template_code']);
	
	}

file_put_contents(WP_RSS_MULTI_TEMPLATES .'templates.css', $templateOptions['template_css']);
	
	 delete_option('rss_template_item');
}




// add slashes to html if magic quotes is not on
function atf_slashit($stringvar){
    if (!get_magic_quotes_gpc()){
        $stringvar = addslashes($stringvar);  //need this
    }
    return $stringvar;
}
// remove slashes if magic quotes is on
function atf_deslashit($stringvar){
    if (1 == get_magic_quotes_gpc()){
        $stringvar = stripslashes($stringvar);
    }
    return $stringvar;
}








function get_template_function($thistemplate){

	$path=str_replace('utils','templates',plugin_dir_path( __FILE__));
	
	$dir_handle = @opendir($path) or die("Cannot open the file $path");
	
echo '<p>Choose a template for your output.  Choose DEFAULT if you want the typical template.  You can also customize your own template.  <a href="options-general.php?page=wp_rss_multi_importer_admin&tab=template_options">Go here to learn more about this.</a>  NOTE:  all options below are available for the DEFAULT template only.</p>';
echo '<p><label class="o_textinput" for="template">Template</label>';
echo 	'<SELECT NAME="rss_import_options[template]" id="template">';
while ($file = readdir($dir_handle)) {
	if (eregi("\.php",$file)){
		
		$friendlyfile=strtoupper(str_replace('.php','',$file));
		$friendlyfile=str_replace('_',' ',$friendlyfile);
		
		if (($file=='default.php') && ($thistemplate=='')){
	echo 	'<OPTION VALUE="'.$file.'" selected>'.$friendlyfile.'</OPTION>';	
		}else{
		
	echo 	'<OPTION VALUE="'.$file.'" '.($file==$thistemplate ? 'selected':'').'>'.$friendlyfile.'</OPTION>';
		}
}
}

echo 	'</p></SELECT>';	
closedir($dir_handle);

//showphp($thistemplate);   //this works
//echo WP_RSS_MULTI_TEMPLATES;
}





function save_template_function($thistemplate){
$templateOptions=get_option('rss_template_item');
	
	$friendlyfile=strtoupper(str_replace('.php','',$thistemplate));
	$friendlyfile=str_replace('_',' ',$friendlyfile);

	



if (empty($templateOptions)){
	
	
?>

<div id="save_template">
	
	<p>Here is the template you are currently using.  If you changed the template, you can save it by giving it a new name and click Save This Template. When you do this, you will also be saving the CSS template.</p>
	<p><label class="o_textinput" for="template">Your Current Template:</label> 
	<?php echo $friendlyfile?>
	</p>
	
 <form method="POST" id="template-form">
  Give your current template a unique name:  <input type="text" id="inputtext" value="" name="filename" >
<input type="hidden" id="inputtext" value="<?php echo $thistemplate?>" name="filetemplate">
    <input type="submit" value="Save This Template" name="Save">
    </form>
<p>OR</p>
<button type="button" name="csssave" id="css-save" value="">CLICK TO JUST SAVE CSS FILE</button>	
</div>
<?php
}else{
echo '<div id="show_action_options">';	
echo '<button type="button" name="theSubmitButton" id="template-restore" value="Restore My Template">Restore</button>';	
	
echo '<button type="button" name="filetemplate" id="template-save" value="'.$thistemplate.'">Save Again</button>';	
echo '</div>';
}
?>
<div id="note"></div>


<?php


//restore_template();

}


?>