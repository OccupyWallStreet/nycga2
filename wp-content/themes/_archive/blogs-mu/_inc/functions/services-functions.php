<?php
/*
* Copyright (c) 2008 http://www.webmotionuk.com / http://www.webmotionuk.co.uk
* "PHP & Jquery image upload & crop"
* Date: 2008-11-21
* Ver 1.2
* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
* ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
* WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
* IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
* PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
* STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF
* THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*
*/
##########################################################################################################
# IMAGE FUNCTIONS																						 #
# You do not need to alter these functions																 #
##########################################################################################################

function resizeImage($image,$width,$height,$scale) {
list($imagewidth, $imageheight, $imageType) = getimagesize($image);
$imageType = image_type_to_mime_type($imageType);
$newImageWidth = ceil($width * $scale);
$newImageHeight = ceil($height * $scale);
$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
switch($imageType) {
case "image/gif":
$source=imagecreatefromgif($image);
break;
case "image/pjpeg":
case "image/jpeg":
case "image/jpg":
$source=imagecreatefromjpeg($image);
break;
case "image/png":
case "image/x-png":
$source=imagecreatefrompng($image);
break;
}
imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);

switch($imageType) {
case "image/gif":
imagegif($newImage,$image);
break;
case "image/pjpeg":
case "image/jpeg":
case "image/jpg":
imagejpeg($newImage,$image,90);
break;
case "image/png":
case "image/x-png":
imagepng($newImage,$image);
break;
}
chmod($image, 0777);
return $image;
}

//You do not need to alter these functions

function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
list($imagewidth, $imageheight, $imageType) = getimagesize($image);
$imageType = image_type_to_mime_type($imageType);
$newImageWidth = ceil($width * $scale);
$newImageHeight = ceil($height * $scale);
$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
switch($imageType) {
case "image/gif":
$source=imagecreatefromgif($image);
break;
case "image/pjpeg":
case "image/jpeg":
case "image/jpg":
$source=imagecreatefromjpeg($image);
break;
case "image/png":
case "image/x-png":
$source=imagecreatefrompng($image);
break;
}

imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
switch($imageType) {
case "image/gif":
imagegif($newImage,$thumb_image_name);
break;
case "image/pjpeg":
case "image/jpeg":
case "image/jpg":
imagejpeg($newImage,$thumb_image_name,90);
break;
case "image/png":
case "image/x-png":
imagepng($newImage,$thumb_image_name);
break;
}
chmod($thumb_image_name, 0777);
return $thumb_image_name;
}

//You do not need to alter these functions

function getHeight($image) {
$size = getimagesize($image);
$height = $size[1];
return $height;
}

//You do not need to alter these functions

function getWidth($image) {
$size = getimagesize($image);
$width = $size[0];
return $width;
}

////////////////////////////////////////////////////////////////////////

////////////start img////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////


$options4 = array (

array (	"name" => "Service headline 1",
"id" => $shortname."_blogsmu_headline1",
"std" => "Service Headline 1",
"type" => "text"),

array (	"name" => "Service 1 link <em>*must have http://</em>",
"id" => $shortname."_blogsmu_headline1_link",
"std" => "",
"type" => "text"),

array (	"name" => "Service short text 1",
"id" => $shortname."_blogsmu_text1",
"std" => "",
"type" => "textarea")

);


$options5 = array (

array (	"name" => "Service headline 2",
"id" => $shortname."_blogsmu_headline2",
"std" => "Service Headline 2",
"type" => "text"),

array (	"name" => "Service 2 link <em>*must have http://</em>",
"id" => $shortname."_blogsmu_headline2_link",
"std" => "",
"type" => "text"),

array (	"name" => "Service short text 2",
"id" => $shortname."_blogsmu_text2",
"std" => "",
"type" => "textarea")

);


$options6 = array (

array (	"name" => "Service headline 3",
"id" => $shortname."_blogsmu_headline3",
"std" => "Service Headline 3",
"type" => "text"),

array (	"name" => "Service 3 link <em>*must have http://</em>",
"id" => $shortname."_blogsmu_headline3_link",
"std" => "",
"type" => "text"),

array (	"name" => "Service short text 3",
"id" => $shortname."_blogsmu_text3",
"std" => "",
"type" => "textarea")

);


$options7 = array (

array (	"name" => "Service headline 4",
"id" => $shortname."_blogsmu_headline4",
"std" => "Service Headline 4",
"type" => "text"),

array (	"name" => "Service 4 link <em>*must have http://</em>",
"id" => $shortname."_blogsmu_headline4_link",
"std" => "",
"type" => "text"),


array (	"name" => "Service short text 4",
"id" => $shortname."_blogsmu_text4",
"std" => "",
"type" => "textarea")

);

$options8 = array (

array (	"name" => "Service headline 5",
"id" => $shortname."_blogsmu_headline5",
"std" => "Service Headline 5",
"type" => "text"),

array (	"name" => "Service 5 link <em>*must have http://</em>",
"id" => $shortname."_blogsmu_headline5_link",
"std" => "",
"type" => "text"),

array (	"name" => "Service short text 5",
"id" => $shortname."_blogsmu_text5",
"std" => "",
"type" => "textarea")

);


$options9 = array (

array (	"name" => "Service headline 6",
"id" => $shortname."_blogsmu_headline6",
"std" => "Service Headline 6",
"type" => "text"),

array (	"name" => "Service 6 link <em>*must have http://</em>",
"id" => $shortname."_blogsmu_headline6_link",
"std" => "",
"type" => "text"),

array (	"name" => "Service short text 6",
"id" => $shortname."_blogsmu_text6",
"std" => "",
"type" => "textarea")

);


$options10 = array (

array (	"name" => "Service headline 7",
"id" => $shortname."_blogsmu_headline7",
"std" => "Service Headline 7",
"type" => "text"),

array (	"name" => "Service 7 link <em>*must have http://</em>",
"id" => $shortname."_blogsmu_headline7_link",
"std" => "",
"type" => "text"),

array (	"name" => "Service short text 7",
"id" => $shortname."_blogsmu_text7",
"std" => "",
"type" => "textarea")

);


$options11 = array (

array (	"name" => "Service headline 8",
"id" => $shortname."_blogsmu_headline8",
"std" => "Service Headline 8",
"type" => "text"),

array (	"name" => "Service 8 link <em>*must have http://</em>",
"id" => $shortname."_blogsmu_headline8_link",
"std" => "",
"type" => "text"),

array (	"name" => "Service short text 8",
"id" => $shortname."_blogsmu_text8",
"std" => "",
"type" => "textarea")

);


$options12 = array (

array (	"name" => "Service headline 9",
"id" => $shortname."_blogsmu_headline9",
"std" => "Service Headline 9",
"type" => "text"),

array (	"name" => "Service 9 link <em>*must have http://</em>",
"id" => $shortname."_blogsmu_headline9_link",
"std" => "",
"type" => "text"),

array (	"name" => "Service short text 9",
"id" => $shortname."_blogsmu_text9",
"std" => "",
"type" => "textarea")

);



function blogsmu_features_page() {
global $blog_id, $themename, $theme_version, $image_prefix_name;
$image_prefix_name = 'blogsmu';
$service_block = "Service";

////////////////////////////////////////////
$uploads = wp_upload_dir();
$upload_files_path = get_option('upload_path');
//echo wp_upload_dir() . '<br /><br />';
//echo get_option('upload_path'). '<br /><br />';
//echo $uploads['path'] . '<br /><br />';
//echo $uploads['url'] . '<br /><br />';
//echo $uploads['subdir'] . '<br /><br />';
//echo $uploads['basedir'] . '<br /><br />';
//echo $uploads['baseurl'] . '<br /><br />';
//echo $uploads['error'] . ' - ERROR<br /><br />';
//echo WP_CONTENT_DIR . '<br /><br />';
//echo WP_CONTENT_URL . '<br /><br />';
$upload_url_trim = str_replace( WP_CONTENT_DIR, "", $uploads['basedir'] );
//echo $upload_url_trim . '<br /><br />';
if (substr($upload_url_trim, -1) == '/') {
$upload_url_trim = rtrim($upload_url_trim, '/');
}
/////////////////////////////////////////////////////You can alter these options///////////////////////////
$tpl_url = get_site_url();
$ptp = get_template();
$uploads_folder = "thumb";
$upload_path = $uploads['basedir'] . '/' . $uploads_folder . "/";
$upload_path_check = $uploads['basedir'] . '/' . $uploads_folder;

$ttpl = get_template_directory_uri();
$ttpl_url = get_site_url();

$upload_url = WP_CONTENT_URL . $upload_url_trim  . '/' . $uploads_folder;
//echo $upload_url;

//Create the upload directory with the right permissions if it doesn't exist
if(!is_dir($upload_path_check)){
mkdir($upload_path_check, 0777);
}
chmod($upload_path_check, 0777);

// Only one of these image types should be allowed for upload
$allowed_image_types = array('image/pjpeg'=>"jpg",'image/jpeg'=>"jpg",'image/jpg'=>"jpg",'image/png'=>"png",'image/x-png'=>"png",'image/gif'=>"gif");
$allowed_image_ext = array_unique($allowed_image_types); // do not change this
$image_ext = "";	// initialise variable, do not change this.
foreach ($allowed_image_ext as $mime_type => $ext) {
$image_ext.= strtoupper($ext)." ";
}
?>

<div id="options-panel">

<div id="options-head"><h2><?php echo $themename; ?> <?php _e("Custom Homepage Options", TEMPLATE_DOMAIN); ?></h2>
<div class="theme-versions"><?php _e("Version",TEMPLATE_DOMAIN); ?> <?php echo $theme_version; ?></div>
</div>

<div id="sbtabs_uploads">


<div id='tab1' class="tabc">
<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////

$large_image_name = $image_prefix_name . '1.jpg'; 		     // New name of the large image
$normal_image_name = $image_prefix_name . '1_normal.jpg'; 		     // New name of the large image
$thumb_image_name = $image_prefix_name . '1_thumb.jpg'; 	// New name of the thumbnail image
$max_file = "1000000"; 						        // Approx below 1MB
$max_width = "850";							        // Max width allowed for the large image
$thumb_width = "260";						        // Width of thumbnail image
$thumb_height = "150";                              // Height of thumbnail image

//Image Locations
$normal_image_location = $upload_path . $normal_image_name;
$large_image_location = $upload_path . $large_image_name;
$thumb_image_location = $upload_path . $thumb_image_name;


//Check to see if any images with the same names already exist
if (file_exists($large_image_location)){
if (file_exists($thumb_image_location)){
$thumb_photo_exists = "<img src=\"".$upload_path.$thumb_image_name."\" alt=\"Thumbnail Image\"/>";
} else {
$thumb_photo_exists = "";
}
$large_photo_exists = "<img src=\"".$upload_path.$large_image_name."\" alt=\"Large Image\"/>";
} else {
$large_photo_exists = "";
$thumb_photo_exists = "";
}
// normal photo check
if (file_exists($normal_image_location)){
$normal_photo_exists = "<img src=\"".$upload_path.$normal_image_name."\" alt=\"Large Image\"/>";
} else {
$normal_photo_exists = "";
}


if (isset($_POST['upload1'])) {
//Get the file information
$userfile_name = $_FILES['image']['name'];
$userfile_tmp = $_FILES['image']['tmp_name'];
$userfile_type = $_FILES['image']['type'];
$userfile_size = $_FILES['image']['size'];
$filename = basename($_FILES['image']['name']);
$file_ext = substr($filename, strrpos($filename, '.') + 1);

//Only process if the file is a JPG, PNG or GIF and below the allowed limit

if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . "<strong>".$image_ext."</strong>" . __("images accepted for upload", TEMPLATE_DOMAIN) . "<br />";
}
}

//check if the file size is above the allowed limit

if ($userfile_size > $max_file) {
$error.= __("Images must be under 1 MB in size", TEMPLATE_DOMAIN);
}

} else {
$error= __("Select an image to upload", TEMPLATE_DOMAIN);
}

//Everything is ok, so we can upload the image.

if (strlen($error)==0){
if (isset($_FILES['image']['name'])){
move_uploaded_file($userfile_tmp, $large_image_location);
chmod($large_image_location, 0777);

$width = getWidth($large_image_location);
$height = getHeight($large_image_location);

//Scale the image if it is greater than the width set above
if ($width > $max_width){
$scale = $max_width/$width;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
} else {
$scale = 1;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
}

//Delete the thumbnail file so the user can create a new one
if (file_exists($thumb_image_location)) {
unlink($thumb_image_location);
}
}

//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab1\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done

}
}
///////////////////////////////////////////////////////////////////////////////
// if upload with no crop features
///////////////////////////////////////////////////////////////////////////////
if (isset($_POST["normal_upload1"])) {
//Get the file information
$userfile_name = $_FILES['image']['name'];
$userfile_type = $_FILES['image']['type'];
$userfile_tmp = $_FILES['image']['tmp_name'];
$userfile_size = $_FILES['image']['size'];
$filename = basename($_FILES['image']['name']);
$file_ext = substr($filename, strrpos($filename, '.') + 1);

//Only process if the file is a JPG and below the allowed limit
if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {

foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only", TEMPLATE_DOMAIN) . "<strong>".$image_ext."</strong>" . __("images accepted for upload", TEMPLATE_DOMAIN) . "<br />";
}
}

if($userfile_size > $max_file){
$error= __("ONLY images under 1MB are accepted for upload", TEMPLATE_DOMAIN);
}
} else {
$error= __("Select an image for upload", TEMPLATE_DOMAIN);
}

//Everything is ok, so we can upload the image.
if (strlen($error)==0){
if (isset($_FILES['image']['name'])){
move_uploaded_file($userfile_tmp, $normal_image_location);
chmod($normal_image_location, 0777);
//Delete the thumbnail file so the user can create a new one
if (file_exists($thumb_image_location)) {
unlink($thumb_image_location);
}
}
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab1\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}



if (isset($_POST['upload_thumbnail1']) && strlen($large_photo_exists) > 0) {
//Get the new coordinates to crop the image.
$x1 = $_POST["x1"];
$y1 = $_POST["y1"];
$x2 = $_POST["x2"];
$y2 = $_POST["y2"];
$w = $_POST["w"];
$h = $_POST["h"];

//Scale the image to the thumb_width set above
$scale = $thumb_width/$w;
$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);

//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab1\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}


?>

<?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
$current_large_image_width = getWidth($large_image_location);
$current_large_image_height = getHeight($large_image_location); ?>

<script type="text/javascript">
function preview(img, selection) {
var scaleX = <?php echo $thumb_width;?> / selection.width;
var scaleY = <?php echo $thumb_height;?> / selection.height;
$('#thumbnail + div > img').css({
width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px',
height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
});
$('#x1').val(selection.x1);
$('#y1').val(selection.y1);
$('#x2').val(selection.x2);
$('#y2').val(selection.y2);
$('#w').val(selection.width);
$('#h').val(selection.height);
}
$(document).ready(function () {
$('#save_thumb').click(function() {
var x1 = $('#x1').val();
var y1 = $('#y1').val();
var x2 = $('#x2').val();
var y2 = $('#y2').val();
var w = $('#w').val();
var h = $('#h').val();
if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
alert("You must make a selection first");
return false;
} else {
return true;
}
});
});
function selectionStart(img, selection) { width:260; height:150; }
$(window).load(function () {
$('#thumbnail').imgAreaSelect({ onSelectStart: selectionStart, resizable: true, x1: 20, y1: 20, x2: 280, y2: 170, aspectRatio: '28:17', onSelectChange: preview });
});
</script>
<?php } ?>


<div class="admin-options">

<?php global $themename, $shortname;
if ( !isset($_REQUEST['resetall']) ) {
    $_REQUEST['resetall'] = '';
}
if ( !isset($_REQUEST['saved4']) ) {
    $_REQUEST['saved4'] = '';
}
if ( !isset($_REQUEST['reset4']) ) {
    $_REQUEST['reset4'] = '';
}
if ( !isset($_REQUEST['saved5']) ) {
    $_REQUEST['saved5'] = '';
}
if ( !isset($_REQUEST['reset5']) ) {
    $_REQUEST['reset5'] = '';
}
if ( !isset($_REQUEST['saved6']) ) {
    $_REQUEST['saved6'] = '';
}
if ( !isset($_REQUEST['reset6']) ) {
    $_REQUEST['reset6'] = '';
}
if ( !isset($_REQUEST['saved7']) ) {
    $_REQUEST['saved7'] = '';
}
if ( !isset($_REQUEST['reset7']) ) {
    $_REQUEST['reset7'] = '';
}
if ( !isset($_REQUEST['saved8']) ) {
    $_REQUEST['saved8'] = '';
}
if ( !isset($_REQUEST['reset8']) ) {
    $_REQUEST['reset8'] = '';
}
if ( !isset($_REQUEST['saved9']) ) {
    $_REQUEST['saved9'] = '';
}
if ( !isset($_REQUEST['reset9']) ) {
    $_REQUEST['reset9'] = '';
}
if ( !isset($_REQUEST['saved10']) ) {
    $_REQUEST['saved10'] = '';
}
if ( !isset($_REQUEST['reset10']) ) {
    $_REQUEST['reset10'] = '';
}
if ( !isset($_REQUEST['saved11']) ) {
    $_REQUEST['saved11'] = '';
}
if ( !isset($_REQUEST['reset11']) ) {
    $_REQUEST['reset11'] = '';
}
if ( !isset($_REQUEST['saved12']) ) {
    $_REQUEST['saved12'] = '';
}
if ( !isset($_REQUEST['reset12']) ) {
    $_REQUEST['reset12'] = '';
}
if ( $_REQUEST['resetall'] )
echo '<div id="message" class="updated fade"><p><strong>' . __("All images deleted and settings reset", TEMPLATE_DOMAIN) . '</strong></p></div>';
?>

<?php
global $themename, $shortname, $options4;
if ( $_REQUEST['saved4'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 1 Settings saved.', TEMPLATE_DOMAIN) . '</strong></p></div>';
if ( $_REQUEST['reset4'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 1 Settings reset.', TEMPLATE_DOMAIN) . '</strong></p></div>';
?>

<h4><?php _e("Featured images 1 Setting",TEMPLATE_DOMAIN); ?></h4>
<div class="tab-option">
<div class="option-save">


<?php
if (isset($_POST['delete_thumbnail1'])){
unlink($upload_path . $large_image_name);
unlink($upload_path . $thumb_image_name);
echo "<h2 id='file-delete'>" . __("File successfully deleted", TEMPLATE_DOMAIN) . "</h2>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab1\">";
exit();
}
if (isset($_POST["delete_normal_upload1"])){
unlink("$upload_path/$normal_image_name");
echo "<h2 id='file-delete'>" . __('File successfully deleted', TEMPLATE_DOMAIN) . "</h2>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab1\">";
exit();
}
?>


<?php
//Display error message if there are any
if ( !isset($error) ) {
    $error = false;
}
if(strlen($error)>0){
echo "<p class=\"uperror\"><strong>" . __("Error!",TEMPLATE_DOMAIN) . "&nbsp;</strong>" . $error . "</p>";
}

if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0) { ?>
<img src="<?php echo "$upload_url/$thumb_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<div class="submit"><input type="submit" name="delete_thumbnail1" class="button-secondary" value="<?php _e("Delete This Image",TEMPLATE_DOMAIN); ?>" /></div>
</form>
<?php } else if( strlen($normal_photo_exists)>0 ){  ?>
<img src="<?php echo "$upload_url/$normal_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<input type="submit" class="button-secondary" name="delete_normal_upload1" value="<?php _e("Delete This Image",TEMPLATE_DOMAIN); ?>" />
</form>
<?php } ?>


<?php if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){ ?>
<?php } else { ?>
<?php if(strlen($large_photo_exists)>0) { ?>
<h3><?php _e('Crop And Save Your Thumbnail', TEMPLATE_DOMAIN); ?></h3>
<div>
<img src="<?php echo "$upload_url/$large_image_name"; ?>" style="clear: both; margin-bottom: 10px;" id="thumbnail" alt="Create Thumbnail" />
<br style="clear:both;"/>
<form name="thumbnail" action="" method="post">
<input type="hidden" name="x1" value="" id="x1" />
<input type="hidden" name="y1" value="" id="y1" />
<input type="hidden" name="x2" value="" id="x2" />
<input type="hidden" name="y2" value="" id="y2" />
<input type="hidden" name="w" value="" id="w" />
<input type="hidden" name="h" value="" id="h" />
<div class="submit"><input type="submit" class="sbutton button-secondary" name="upload_thumbnail1" value="<?php _e("Save Thumbnail", TEMPLATE_DOMAIN); ?>" id="save_thumb" /></div>
</form>
</div>
<?php } } ?>


<?php if(strlen($large_photo_exists)==0 && strlen($normal_photo_exists)==0){ ?>
<h3><?php _e("Upload Image", TEMPLATE_DOMAIN); ?> <?php echo $thumb_width . ' X ' . $thumb_height; ?></h3>
<form name="photo" enctype="multipart/form-data" action="<?php echo admin_url('themes.php?page=custom-homepage.php&#tab1'); ?>" method="post">
<input type="file" class="ups" name="image" />
<input type="submit" name="upload1" class="sbutton button-secondary" value="<?php _e("Upload and crop &raquo;",TEMPLATE_DOMAIN); ?>" />&nbsp;&nbsp;<input type="submit" name="normal_upload1" class="sbutton button-secondary" value="<?php _e("Upload &raquo;",TEMPLATE_DOMAIN); ?>" />
<p class="onlyjpg">* <?php _e("only",TEMPLATE_DOMAIN); ?> <?php echo $image_ext; ?> <?php _e("image file are allowed",TEMPLATE_DOMAIN); ?></p>
</form>
<?php } ?>

<br />
<form method="post">
<?php foreach ($options4 as $value) {   ?>

<?php
switch ( $value['type'] ) {
case 'text':
?>

<div class="description"><?php echo $value['name']; ?></div>
<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id']);
} else {
echo $value['std']; } ?>" />
</p>

<?php
break;
case 'textarea':
?>

<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$video_code = get_option($valuey);
?>
<div class="description"><?php echo $value['name']; ?></div>
<p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?></textarea></p>

<?php
break;
default;
?>



<?php
break;
} ?>


<?php } ?>


<p class="submit">
<input name="save" type="submit" class="sbutton button-primary" value="<?php _e("Save setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="save4" />
</p>
</form>

<form method="post">
<p class="submit">
<input name="reset" type="submit" class="sbutton button-primary" value="<?php _e("Reset setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="reset4" />
</p>
</form>

</div>
</div>

</div><!-- admin-options -->
</div><!-- end tabc -->


<?php
echo "<div id='tab2' class='tabc'>";

$large_image_name = $image_prefix_name . '2.jpg'; 		     // New name of the large image
$normal_image_name = $image_prefix_name . '2_normal.jpg'; 	// New name of the thumbnail image
$thumb_image_name = $image_prefix_name . '2_thumb.jpg'; 	// New name of the thumbnail image
$max_file = "1000000"; 						        // Approx below 1MB
$max_width = "850";							        // Max width allowed for the large image
$thumb_width = "260";						        // Width of thumbnail image
$thumb_height = "150";                              // Height of thumbnail image

//Image Locations
$normal_image_location = $upload_path . $normal_image_name;
$large_image_location = $upload_path . $large_image_name;
$thumb_image_location = $upload_path . $thumb_image_name;


//Check to see if any images with the same names already exist
if (file_exists($large_image_location)){
if (file_exists($thumb_image_location)){
$thumb_photo_exists = "<img src=\"".$upload_path.$thumb_image_name."\" alt=\"Thumbnail Image\"/>";
} else {
$thumb_photo_exists = "";
}
$large_photo_exists = "<img src=\"".$upload_path.$large_image_name."\" alt=\"Large Image\"/>";
} else {
$large_photo_exists = "";
$thumb_photo_exists = "";
}
//Check normal image
if (file_exists($normal_image_location)){
   	$normal_photo_exists = "<img src=\"".$upload_path.$normal_image_name."\" alt=\"Large Image\"/>";
} else {
   	$normal_photo_exists = "";
}



if (isset($_POST['upload2'])) {
//Get the file information
$userfile_name = $_FILES['image']['name'];
$userfile_tmp = $_FILES['image']['tmp_name'];
$userfile_type = $_FILES['image']['type'];
$userfile_size = $_FILES['image']['size'];
$filename = basename($_FILES['image']['name']);
$file_ext = substr($filename, strrpos($filename, '.') + 1);

//Only process if the file is a JPG, PNG or GIF and below the allowed limit

if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only", TEMPLATE_DOMAIN) . " <strong>".$image_ext."</strong>" . __(" images accepted for upload", TEMPLATE_DOMAIN) . "<br />";
}
}

//check if the file size is above the allowed limit

if ($userfile_size > $max_file) {
$error.= __("Images must be under 1 MB in size",TEMPLATE_DOMAIN);
}

} else {
$error= __("Select an image for upload",TEMPLATE_DOMAIN);
}

//Everything is ok, so we can upload the image.

if (strlen($error)==0){
if (isset($_FILES['image']['name'])){
move_uploaded_file($userfile_tmp, $large_image_location);
chmod($large_image_location, 0777);
$width = getWidth($large_image_location);
$height = getHeight($large_image_location);
//Scale the image if it is greater than the width set above
if ($width > $max_width){
$scale = $max_width/$width;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
} else {
$scale = 1;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
}
//Delete the thumbnail file so the user can create a new one
if (file_exists($thumb_image_location)) {
unlink($thumb_image_location);
}
}
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab2\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}


//normal image
if (isset($_POST["normal_upload2"])) {
	//Get the file information
	$userfile_name = $_FILES['image']['name'];
	$userfile_tmp = $_FILES['image']['tmp_name'];
    $userfile_type = $_FILES['image']['type'];
	$userfile_size = $_FILES['image']['size'];
	$filename = basename($_FILES['image']['name']);
	$file_ext = substr($filename, strrpos($filename, '.') + 1);

	//Only process if the file is a JPG and below the allowed limit
	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {

foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . "<strong>".$image_ext."</strong>" . __("images accepted for upload", TEMPLATE_DOMAIN) . "<br />";
}
}

        if($userfile_size > $max_file){
            $error= __("ONLY images under 1MB are accepted for upload", TEMPLATE_DOMAIN);
        }
	} else {
		$error= __("Select an image for upload", TEMPLATE_DOMAIN);
	}
	//Everything is ok, so we can upload the image.
	if (strlen($error)==0){

		if (isset($_FILES['image']['name'])){

			move_uploaded_file($userfile_tmp, $normal_image_location);
			chmod($normal_image_location, 0777);

			//Delete the thumbnail file so the user can create a new one
			if (file_exists($thumb_image_location)) {
				unlink($thumb_image_location);
			}
		}
		//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab2\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}

if ( isset($_POST['upload_thumbnail2']) && strlen($large_photo_exists) > 0 ) {
//Get the new coordinates to crop the image.
$x1 = $_POST["x1"];
$y1 = $_POST["y1"];
$x2 = $_POST["x2"];
$y2 = $_POST["y2"];
$w = $_POST["w"];
$h = $_POST["h"];
//Scale the image to the thumb_width set above
$scale = $thumb_width/$w;
$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab2\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
?>

<?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists) > 0){
$current_large_image_width = getWidth($large_image_location);
$current_large_image_height = getHeight($large_image_location);?>

<script type="text/javascript">
function preview(img, selection) {
var scaleX = <?php echo $thumb_width;?> / selection.width;
var scaleY = <?php echo $thumb_height;?> / selection.height;
$('#thumbnail + div > img').css({
width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px',
height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
});
$('#x1').val(selection.x1);
$('#y1').val(selection.y1);
$('#x2').val(selection.x2);
$('#y2').val(selection.y2);
$('#w').val(selection.width);
$('#h').val(selection.height);
}
$(document).ready(function () {
$('#save_thumb').click(function() {
var x1 = $('#x1').val();
var y1 = $('#y1').val();
var x2 = $('#x2').val();
var y2 = $('#y2').val();
var w = $('#w').val();
var h = $('#h').val();
if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
alert("You must make a selection first");
return false;
} else {
return true;
}
});
});
function selectionStart(img, selection) { width:260;height:150 }
$(window).load(function () {
$('#thumbnail').imgAreaSelect({ onSelectStart: selectionStart, resizable: true, x1: 20, y1: 20, x2: 280, y2: 170, aspectRatio: '28:17', onSelectChange: preview });
});
</script>
<?php } ?>


<div class="admin-options">

<?php
global $themename, $shortname, $options5;
if ( $_REQUEST['saved5'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 2 Settings saved.', TEMPLATE_DOMAIN) . '</strong></p></div>';
if ( $_REQUEST['reset5'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 2 Settings reset.', TEMPLATE_DOMAIN) . '</strong></p></div>';
?>

<h4><?php _e("Featured image 2 Setting",TEMPLATE_DOMAIN); ?></h4>
<div class="tab-option">
<div class="option-save">



<?php
if (isset($_POST['delete_thumbnail2'])){
unlink($upload_path . $large_image_name);
unlink($upload_path . $thumb_image_name);
echo "<h5 id='file-delete'>" . __('File successfully deleted', TEMPLATE_DOMAIN) . "</h4>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab2\">";
exit();
}
if (isset($_POST["delete_normal_upload2"])){
unlink("$upload_path/$normal_image_name");
echo "<h5 id='file-delete'>" . __('File successfully deleted', TEMPLATE_DOMAIN) . "</h4>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab2\">";
exit();
}
?>


<?php
//Display error message if there are any
if(strlen($error)>0){
echo "<p class=\"uperror\"><strong>" . __("Error!",TEMPLATE_DOMAIN) . "&nbsp;</strong>" . $error . "</p>";
}

if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0) { ?>
<img src="<?php echo "$upload_url/$thumb_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<div class="submit"><input type="submit" name="delete_thumbnail2" class="sbutton button-secondary" value="<?php _e("Delete This Image",TEMPLATE_DOMAIN); ?>" /></div>
</form>
<?php } else if( strlen($normal_photo_exists)>0 ){ ?>
<img src="<?php echo "$upload_url/$normal_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<input type="submit" name="delete_normal_upload2" class="button-secondary" value="<?php _e("Delete This Image",TEMPLATE_DOMAIN); ?>" />
</form>
<?php } ?>


<?php if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){ ?>
<?php } else {
if(strlen($large_photo_exists) > 0 ) { ?>
<h3><?php _e('Crop And Save Your Thumbnail', TEMPLATE_DOMAIN); ?></h3>
<div>
<img src="<?php echo "$upload_url/$large_image_name"; ?>" style="clear: both; margin-bottom: 10px;" id="thumbnail" alt="Create Thumbnail" />
<br style="clear:both;"/>
<form name="thumbnail" action="" method="post">
<input type="hidden" name="x1" value="" id="x1" />
<input type="hidden" name="y1" value="" id="y1" />
<input type="hidden" name="x2" value="" id="x2" />
<input type="hidden" name="y2" value="" id="y2" />
<input type="hidden" name="w" value="" id="w" />
<input type="hidden" name="h" value="" id="h" />
<div class="submit"><input type="submit" class="sbutton button-secondary" name="upload_thumbnail2" value="<?php _e("Save Thumbnail",TEMPLATE_DOMAIN); ?>" id="save_thumb" /></div>
</form>
</div>
<?php } } ?>


<?php if(strlen($large_photo_exists)==0 && strlen($normal_photo_exists)==0){ ?>
<h3><?php _e("Upload Image", TEMPLATE_DOMAIN); ?> <?php echo $thumb_width . ' X ' . $thumb_height; ?></h3>
<form name="photo" enctype="multipart/form-data" action="<?php echo admin_url('themes.php?page=custom-homepage.php&#tab2'); ?>" method="post">
<input type="file" class="ups" name="image" />
<input type="submit" class="button-secondary" name="upload2" value="Upload and crop &raquo;" />&nbsp;&nbsp;&nbsp;<input type="submit" class="button-secondary" name="normal_upload2" value="Upload &raquo;" />
<p class="onlyjpg">* only <?php echo $image_ext; ?> image file are allowed</p>
</form>
<?php } ?>

<br />

<form method="post">

<?php foreach ($options5 as $value) {   ?>

<?php
switch ( $value['type'] ) {
case 'text':
?>

<div class="description"><?php echo $value['name']; ?></div>
<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id']);
} else {
echo $value['std']; } ?>" />
</p>

<?php
break;
case 'textarea':
?>

<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$video_code = get_option($valuey);
?>
<div class="description"><?php echo $value['name']; ?></div>
<p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?></textarea></p>

<?php
break;
default;
?>



<?php
break;
} ?>


<?php } ?>

<p class="submit">
<input name="save" type="submit" class="sbutton button-primary" value="<?php _e("Save setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="save5" />
</p>
</form>

<form method="post">
<p class="submit">
<input name="reset" type="submit" class="sbutton button-primary" value="<?php _e("Reset setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="reset5" />
</p>
</form>

</div>
</div>
</div>

</div><!-- end tabc -->
<?php
echo "<div id='tab3' class='tabc'>";
$normal_image_name = $image_prefix_name . '3_normal.jpg'; 		     // New name of the large image
$large_image_name = $image_prefix_name . '3.jpg'; 		     // New name of the large image
$thumb_image_name = $image_prefix_name . '3_thumb.jpg'; 	// New name of the thumbnail image
$max_file = "1000000"; 						        // Approx below 1MB
$max_width = "850";							        // Max width allowed for the large image
$thumb_width = "260";						        // Width of thumbnail image
$thumb_height = "150";                              // Height of thumbnail image

//Image Locations
$normal_image_location = $upload_path . $normal_image_name;
$large_image_location = $upload_path . $large_image_name;
$thumb_image_location = $upload_path . $thumb_image_name;

//Check to see if any images with the same names already exist
if (file_exists($large_image_location)){
if (file_exists($thumb_image_location)){
$thumb_photo_exists = "<img src=\"".$upload_path.$thumb_image_name."\" alt=\"Thumbnail Image\"/>";
} else {
$thumb_photo_exists = "";
}
$large_photo_exists = "<img src=\"".$upload_path.$large_image_name."\" alt=\"Large Image\"/>";
} else {
$large_photo_exists = "";
$thumb_photo_exists = "";
}
//Check normal image
if (file_exists($normal_image_location)){
   	$normal_photo_exists = "<img src=\"".$upload_path.$normal_image_name."\" alt=\"Large Image\"/>";
} else {
   	$normal_photo_exists = "";
}
?>


<div class="admin-options">

<?php
global $themename, $shortname, $options6;
if ( $_REQUEST['saved6'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 3 Settings saved.',TEMPLATE_DOMAIN) . '</strong></p></div>';
if ( $_REQUEST['reset6'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 3 Settings reset.',TEMPLATE_DOMAIN) . '</strong></p></div>';
?>

<h4><?php _e("Featured image 3 Setting",TEMPLATE_DOMAIN); ?></h4>
<div class="tab-option">
<div class="option-save">

<?php

if (isset($_POST['upload3'])) {
//Get the file information
$userfile_name = $_FILES['image']['name'];
$userfile_tmp = $_FILES['image']['tmp_name'];
$userfile_type = $_FILES['image']['type'];
$userfile_size = $_FILES['image']['size'];
$filename = basename($_FILES['image']['name']);
$file_ext = substr($filename, strrpos($filename, '.') + 1);

//Only process if the file is a JPG, PNG or GIF and below the allowed limit

if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . " <strong>".$image_ext."</strong>" . __(" images accepted for upload",TEMPLATE_DOMAIN) . "<br />";
}
}

//check if the file size is above the allowed limit

if ($userfile_size > $max_file ) {
$error.= __("Images must be under 1 MB in size",TEMPLATE_DOMAIN);
}

} else {
$error= __("Select an image for upload",TEMPLATE_DOMAIN);
}

//Everything is ok, so we can upload the image.

if (strlen($error)==0){
if (isset($_FILES['image']['name'])){
move_uploaded_file($userfile_tmp, $large_image_location);
chmod($large_image_location, 0777);
$width = getWidth($large_image_location);
$height = getHeight($large_image_location);
//Scale the image if it is greater than the width set above
if ($width > $max_width){
$scale = $max_width/$width;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
} else {
$scale = 1;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
}
//Delete the thumbnail file so the user can create a new one
if (file_exists($thumb_image_location)) {
unlink($thumb_image_location);
}
}
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab3\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}

//normal upload
if (isset($_POST["normal_upload3"])) {
	//Get the file information
	$userfile_name = $_FILES['image']['name'];
	$userfile_tmp = $_FILES['image']['tmp_name'];
    $userfile_type = $_FILES['image']['type'];
	$userfile_size = $_FILES['image']['size'];
	$filename = basename($_FILES['image']['name']);
	$file_ext = substr($filename, strrpos($filename, '.') + 1);

	//Only process if the file is a JPG and below the allowed limit
	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {

foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . "<strong>".$image_ext."</strong>" . __("images accepted for upload", TEMPLATE_DOMAIN) . "<br />";
}
}


if($userfile_size > $max_file){
$error= __("ONLY images under 1MB are accepted for upload", TEMPLATE_DOMAIN);
}
	} else {
		$error= __("Select an image for upload", TEMPLATE_DOMAIN);
	}
	//Everything is ok, so we can upload the image.
	if (strlen($error)==0){

		if (isset($_FILES['image']['name'])){

			move_uploaded_file($userfile_tmp, $normal_image_location);
			chmod($normal_image_location, 0777);


			//Delete the thumbnail file so the user can create a new one
			if (file_exists($thumb_image_location)) {
				unlink($thumb_image_location);
			}
		}

//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab3\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}

if (isset($_POST['upload_thumbnail3']) && strlen($large_photo_exists)>0) {
//Get the new coordinates to crop the image.
$x1 = $_POST["x1"];
$y1 = $_POST["y1"];
$x2 = $_POST["x2"];
$y2 = $_POST["y2"];
$w = $_POST["w"];
$h = $_POST["h"];
//Scale the image to the thumb_width set above
$scale = $thumb_width/$w;
$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
//Reload the page again to view the thumbnail

//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab3\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
?>

<?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
$current_large_image_width = getWidth($large_image_location);
$current_large_image_height = getHeight($large_image_location);?>

<script type="text/javascript">
function preview(img, selection) {
var scaleX = <?php echo $thumb_width;?> / selection.width;
var scaleY = <?php echo $thumb_height;?> / selection.height;
$('#thumbnail + div > img').css({
width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px',
height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
});
$('#x1').val(selection.x1);
$('#y1').val(selection.y1);
$('#x2').val(selection.x2);
$('#y2').val(selection.y2);
$('#w').val(selection.width);
$('#h').val(selection.height);
}
$(document).ready(function () {
$('#save_thumb').click(function() {
var x1 = $('#x1').val();
var y1 = $('#y1').val();
var x2 = $('#x2').val();
var y2 = $('#y2').val();
var w = $('#w').val();
var h = $('#h').val();
if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
alert("You must make a selection first");
return false;
} else {
return true;
}
});
});
function selectionStart(img, selection) { width:260;height:150 }
$(window).load(function () {
$('#thumbnail').imgAreaSelect({ onSelectStart: selectionStart, resizable: true, x1: 20, y1: 20, x2: 280, y2: 170, aspectRatio: '28:17', onSelectChange: preview });
});
</script>
<?php } ?>



<?php
if (isset($_POST['delete_thumbnail3'])){
unlink($upload_path . $large_image_name);
unlink($upload_path . $thumb_image_name);
echo "<h5 id='file-delete'>" . __('File successfully deleted', TEMPLATE_DOMAIN) . "</h4>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab3\">";
exit();
}
if (isset($_POST["delete_normal_upload3"])){
unlink("$upload_path/$normal_image_name");
echo "<h5 id='file-delete'>" . __('File successfully deleted', TEMPLATE_DOMAIN) . "</h4>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab3\">";
exit();
}
?>


<?php
//Display error message if there are any
if(strlen($error)>0){
echo "<p class=\"uperror\"><strong>" . __("Error!",TEMPLATE_DOMAIN) . "&nbsp;</strong>" . $error . "</p>";
}

if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0) { ?>
<img src="<?php echo "$upload_url/$thumb_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<div class="submit"><input type="submit" name="delete_thumbnail3" class="sbutton button-secondary" value="<?php _e("Delete This Image",TEMPLATE_DOMAIN); ?>" /></div>
</form>

<?php } else if( strlen($normal_photo_exists)>0 ){  ?>
<img src="<?php echo "$upload_url/$normal_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<input type="submit" name="delete_normal_upload3" class="button-secondary" value="Delete This Image" />
</form>
<?php } ?>

<?php if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){
} else {
if(strlen($large_photo_exists)>0){ ?>
<h3><?php _e('Crop And Save Your Thumbnail', TEMPLATE_DOMAIN); ?></h3>
<div>
<img src="<?php echo "$upload_url/$large_image_name"; ?>" style="clear: both; margin-bottom: 10px;" id="thumbnail" alt="Create Thumbnail" />
<br style="clear:both;"/>

<form name="thumbnail" action="" method="post">
<input type="hidden" name="x1" value="" id="x1" />
<input type="hidden" name="y1" value="" id="y1" />
<input type="hidden" name="x2" value="" id="x2" />
<input type="hidden" name="y2" value="" id="y2" />
<input type="hidden" name="w" value="" id="w" />
<input type="hidden" name="h" value="" id="h" />
<input type="submit" name="upload_thumbnail3" class="button-secondary" value="Save Thumbnail" id="save_thumb" />
</form>
</div>

<?php } } ?>

<?php if(strlen($large_photo_exists)==0 && strlen($normal_photo_exists)==0){ ?>
<h3><?php _e("Upload Image",TEMPLATE_DOMAIN); ?> <?php echo $thumb_width . ' X ' . $thumb_height; ?></h3>
<form name="photo" enctype="multipart/form-data" action="<?php echo admin_url('themes.php?page=custom-homepage.php&#tab3'); ?>" method="post">
<input type="file" class="ups" name="image" />
<input type="submit" class="button-secondary" name="upload3" value="Upload and crop &raquo;" />&nbsp;&nbsp;&nbsp;<input type="submit" class="button-secondary" name="normal_upload3" value="Upload &raquo;" />
<p class="onlyjpg">* only <?php echo $image_ext; ?> image file are allowed</p>
</form>
<?php } ?>


<br />

<form method="post">

<?php foreach ($options6 as $value) {   ?>

<?php
switch ( $value['type'] ) {
case 'text':
?>

<div class="description"><?php echo $value['name']; ?></div>
<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id']);
} else {
echo $value['std']; } ?>" />
</p>

<?php
break;
case 'textarea':
?>

<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$video_code = get_option($valuey);
?>
<div class="description"><?php echo $value['name']; ?></div>
<p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?></textarea></p>

<?php
break;
default;
?>



<?php
break;
} ?>

<?php } ?>

<p class="submit">
<input name="save" type="submit" class="sbutton button-primary" value="<?php _e("Save setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="save6" />
</p>
</form>

<form method="post">
<p class="submit">
<input name="reset" type="submit" class="sbutton button-primary" value="<?php _e("Reset setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="reset6" />
</p>
</form>

</div>
</div>
</div>

</div><!-- end tabc -->

<?php
echo "<div id='tab4' class='tabc'>";

$normal_image_name = $image_prefix_name . '4_normal.jpg'; 		     // New name of the normal image
$large_image_name = $image_prefix_name . '4.jpg'; 		     // New name of the large image
$thumb_image_name = $image_prefix_name . '4_thumb.jpg'; 	// New name of the thumbnail image
$max_file = "1000000"; 						        // Approx below 1MB
$max_width = "850";							        // Max width allowed for the large image
$thumb_width = "260";						        // Width of thumbnail image
$thumb_height = "150";                              // Height of thumbnail image

//Image Locations
$normal_image_location = $upload_path . $normal_image_name;
$large_image_location = $upload_path . $large_image_name;
$thumb_image_location = $upload_path . $thumb_image_name;

//Check to see if any images with the same names already exist
if (file_exists($large_image_location)){
if (file_exists($thumb_image_location)){
$thumb_photo_exists = "<img src=\"".$upload_path.$thumb_image_name."\" alt=\"Thumbnail Image\"/>";
} else {
$thumb_photo_exists = "";
}
$large_photo_exists = "<img src=\"".$upload_path.$large_image_name."\" alt=\"Large Image\"/>";
} else {
$large_photo_exists = "";
$thumb_photo_exists = "";
}
//Check normal image
if (file_exists($normal_image_location)){
   	$normal_photo_exists = "<img src=\"".$upload_path.$normal_image_name."\" alt=\"Large Image\"/>";
} else {
   	$normal_photo_exists = "";
}
?>


<div class="admin-options">

<?php
global $themename, $shortname, $options7;
if ( $_REQUEST['saved7'] ) echo '<div id="message" class="info"><p><strong>'.$themename. __(' Slider 4 Settings saved.',TEMPLATE_DOMAIN) . '</strong></p></div>';
if ( $_REQUEST['reset7'] ) echo '<div id="message" class="info"><p><strong>'.$themename. __(' Slider 4 Settings reset.', TEMPLATE_DOMAIN) . '</strong></p></div>';
?>

<h4><?php _e("Featured image 4 Setting",TEMPLATE_DOMAIN); ?></h4>
<div class="tab-option">
<div class="option-save">
<?php

if (isset($_POST['upload4'])) {
//Get the file information
$userfile_name = $_FILES['image']['name'];
$userfile_tmp = $_FILES['image']['tmp_name'];
$userfile_type = $_FILES['image']['type'];
$userfile_size = $_FILES['image']['size'];
$filename = basename($_FILES['image']['name']);
$file_ext = substr($filename, strrpos($filename, '.') + 1);

//Only process if the file is a JPG, PNG or GIF and below the allowed limit

if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . " <strong>".$image_ext."</strong>" . __(" images accepted for upload", TEMPLATE_DOMAIN) . "<br />";
}
}

//check if the file size is above the allowed limit

if ($userfile_size > $max_file ) {
$error.= __("Images must be under 1 MB in size",TEMPLATE_DOMAIN);
}

} else {
$error= __("Select an image for upload",TEMPLATE_DOMAIN);
}

//Everything is ok, so we can upload the image.

if (strlen($error)==0){
if (isset($_FILES['image']['name'])){
move_uploaded_file($userfile_tmp, $large_image_location);
chmod($large_image_location, 0777);
$width = getWidth($large_image_location);
$height = getHeight($large_image_location);
//Scale the image if it is greater than the width set above
if ($width > $max_width){
$scale = $max_width/$width;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
} else {
$scale = 1;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
}
//Delete the thumbnail file so the user can create a new one
if (file_exists($thumb_image_location)) {
unlink($thumb_image_location);
}
}
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab4\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}

if (isset($_POST["normal_upload4"])) {
	//Get the file information
	$userfile_name = $_FILES['image']['name'];
	$userfile_tmp = $_FILES['image']['tmp_name'];
    $userfile_type = $_FILES['image']['type'];
	$userfile_size = $_FILES['image']['size'];
	$filename = basename($_FILES['image']['name']);
	$file_ext = substr($filename, strrpos($filename, '.') + 1);

	//Only process if the file is a JPG and below the allowed limit
	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {

foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . "<strong>".$image_ext."</strong>" . __("images accepted for upload", TEMPLATE_DOMAIN) . "<br />";
}
}

       if ($userfile_size > $max_file ) {
 $error= __("ONLY images under 1MB are accepted for upload", TEMPLATE_DOMAIN);
}

} else {
$error= __("Select an image for upload", TEMPLATE_DOMAIN);
}
	//Everything is ok, so we can upload the image.
	if (strlen($error)==0){

		if (isset($_FILES['image']['name'])){

			move_uploaded_file($userfile_tmp, $normal_image_location);
			chmod($normal_image_location, 0777);


			//Delete the thumbnail file so the user can create a new one
			if (file_exists($thumb_image_location)) {
				unlink($thumb_image_location);
			}
		}
		//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab4\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}

if (isset($_POST['upload_thumbnail4']) && strlen($large_photo_exists)>0) {
//Get the new coordinates to crop the image.
$x1 = $_POST["x1"];
$y1 = $_POST["y1"];
$x2 = $_POST["x2"];
$y2 = $_POST["y2"];
$w = $_POST["w"];
$h = $_POST["h"];
//Scale the image to the thumb_width set above
$scale = $thumb_width/$w;
$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab4\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
?>

<?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
$current_large_image_width = getWidth($large_image_location);
$current_large_image_height = getHeight($large_image_location);?>

<script type="text/javascript">
function preview(img, selection) {
var scaleX = <?php echo $thumb_width;?> / selection.width;
var scaleY = <?php echo $thumb_height;?> / selection.height;
$('#thumbnail + div > img').css({
width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px',
height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
});
$('#x1').val(selection.x1);
$('#y1').val(selection.y1);
$('#x2').val(selection.x2);
$('#y2').val(selection.y2);
$('#w').val(selection.width);
$('#h').val(selection.height);
}
$(document).ready(function () {
$('#save_thumb').click(function() {
var x1 = $('#x1').val();
var y1 = $('#y1').val();
var x2 = $('#x2').val();
var y2 = $('#y2').val();
var w = $('#w').val();
var h = $('#h').val();
if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
alert("You must make a selection first");
return false;
} else {
return true;
}
});
});
function selectionStart(img, selection) { width:260;height:150 }
$(window).load(function () {
$('#thumbnail').imgAreaSelect({ onSelectStart: selectionStart, resizable: true, x1: 20, y1: 20, x2: 280, y2: 170, aspectRatio: '28:17', onSelectChange: preview });
});
</script>
<?php } ?>

<?php
if (isset($_POST['delete_thumbnail4'])){
unlink($upload_path . $large_image_name);
unlink($upload_path . $thumb_image_name);
echo "<h5 id='file-delete'>" . __('File successfully deleted', TEMPLATE_DOMAIN) . "</h4>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab4\">";
exit();
}

if (isset($_POST["delete_normal_upload4"])){
unlink("$upload_path/$normal_image_name");
echo "<h5 id='file-delete'>" . __('File successfully deleted', TEMPLATE_DOMAIN) . "</h4>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab4\">";
exit();
}
?>


<?php
//Display error message if there are any
if(strlen($error)>0){
echo "<p class=\"uperror\"><strong>" . __("Error!",TEMPLATE_DOMAIN) . "&nbsp;</strong>" . $error . "</p>";
}

if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0) { ?>
<img src="<?php echo "$upload_url/$thumb_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<div class="submit"><input type="submit" name="delete_thumbnail4" class="sbutton button-secondary" value="<?php _e("Delete This Image",TEMPLATE_DOMAIN); ?>" /></div>
</form>

<?php } else if(strlen($normal_photo_exists)>0 ){ ?>
<img src="<?php echo "$upload_url/$normal_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<input type="submit" name="delete_normal_upload4" class="button-secondary" value="Delete This Image" />
</form>
<?php } ?>

<?php
if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){ ?>
<?php } else { ?>
<?php if(strlen($large_photo_exists)>0){ ?>
<h3><?php _e('Crop And Save Your Thumbnail', TEMPLATE_DOMAIN); ?></h3>
<div>
<img src="<?php echo "$upload_url/$large_image_name"; ?>" style="clear: both; margin-bottom: 10px;" id="thumbnail" alt="Create Thumbnail" />
<br style="clear:both;"/>
<form name="thumbnail" action="" method="post">
<input type="hidden" name="x1" value="" id="x1" />
<input type="hidden" name="y1" value="" id="y1" />
<input type="hidden" name="x2" value="" id="x2" />
<input type="hidden" name="y2" value="" id="y2" />
<input type="hidden" name="w" value="" id="w" />
<input type="hidden" name="h" value="" id="h" />
<input type="submit" name="upload_thumbnail4" class="button-secondary" value="Save Thumbnail" id="save_thumb" />
</form>
</div>

<?php } } ?>


<?php if(strlen($large_photo_exists)==0 && strlen($normal_photo_exists)==0){ ?>
<h3><?php _e("Upload Image",TEMPLATE_DOMAIN); ?> <?php echo $thumb_width . ' X ' . $thumb_height; ?></h3>
<form name="photo" enctype="multipart/form-data" action="<?php echo admin_url('themes.php?page=custom-homepage.php&#tab4'); ?>" method="post">
<input type="file" class="ups" name="image" />
<input type="submit" class="button-secondary" name="upload4" value="Upload and crop &raquo;" />&nbsp;&nbsp;&nbsp;<input type="submit" class="button-secondary" name="normal_upload4" value="Upload &raquo;" />
<p class="onlyjpg">* only <?php echo $image_ext; ?> image file are allowed</p>
</form>
<?php } ?>

<br />

<form method="post">

<?php foreach ($options7 as $value) {   ?>

<?php
switch ( $value['type'] ) {
case 'text':
?>

<div class="description"><?php echo $value['name']; ?></div>
<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id']);
} else {
echo $value['std']; } ?>" />
</p>

<?php
break;
case 'textarea':
?>

<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$video_code = get_option($valuey);
?>
<div class="description"><?php echo $value['name']; ?></div>
<p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?></textarea></p>

<?php
break;
default;
?>



<?php
break;
} ?>

<?php } ?>

<p class="submit">
<input name="save" type="submit" class="sbutton button-primary" value="<?php _e("Save setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="save7" />
</p>
</form>

<form method="post">
<p class="submit">
<input name="reset" type="submit" class="sbutton button-primary" value="<?php _e("Reset setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="reset7" />
</p>
</form>

</div>
</div>
</div>

</div><!-- end tabc -->

<?php
echo "<div id='tab5' class='tabc'>";

$normal_image_name = $image_prefix_name . '5_normal.jpg'; 		     // New name of the large image
$large_image_name = $image_prefix_name . '5.jpg'; 		     // New name of the large image
$thumb_image_name = $image_prefix_name . '5_thumb.jpg'; 	// New name of the thumbnail image
$max_file = "1000000"; 						        // Approx below 1MB
$max_width = "850";							        // Max width allowed for the large image
$thumb_width = "260";						        // Width of thumbnail image
$thumb_height = "150";                              // Height of thumbnail image

//Image Locations
$normal_image_location = $upload_path.$normal_image_name;
$large_image_location = $upload_path . $large_image_name;
$thumb_image_location = $upload_path . $thumb_image_name;

//Check to see if any images with the same names already exist
if (file_exists($large_image_location)){
if (file_exists($thumb_image_location)){
$thumb_photo_exists = "<img src=\"".$upload_path.$thumb_image_name."\" alt=\"Thumbnail Image\"/>";
} else {
$thumb_photo_exists = "";
}
$large_photo_exists = "<img src=\"".$upload_path.$large_image_name."\" alt=\"Large Image\"/>";
} else {
$large_photo_exists = "";
$thumb_photo_exists = "";
}

?>


<div class="admin-options">

<?php
global $themename, $shortname, $options8;
if ( $_REQUEST['saved8'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 5 Settings saved.',TEMPLATE_DOMAIN) . '</strong></p></div>';
if ( $_REQUEST['reset8'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 5 Settings reset.',TEMPLATE_DOMAIN) . '</strong></p></div>';
?>

<h4><?php _e("Featured image 5 Setting",TEMPLATE_DOMAIN); ?></h4>
<div class="tab-option">
<div class="option-save">

<?php

if (isset($_POST['upload5'])) {
//Get the file information
$userfile_name = $_FILES['image']['name'];
$userfile_tmp = $_FILES['image']['tmp_name'];
$userfile_type = $_FILES['image']['type'];
$userfile_size = $_FILES['image']['size'];
$filename = basename($_FILES['image']['name']);
$file_ext = substr($filename, strrpos($filename, '.') + 1);

//Only process if the file is a JPG, PNG or GIF and below the allowed limit

if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . " <strong>".$image_ext."</strong>" . __(" images accepted for upload",TEMPLATE_DOMAIN) . "<br />";
}
}

//check if the file size is above the allowed limit

if ($userfile_size > $max_file ) {
$error.= __("Images must be under 1 MB in size",TEMPLATE_DOMAIN);
}

} else {
$error= __("Select an image for upload",TEMPLATE_DOMAIN);
}

//Everything is ok, so we can upload the image.

if (strlen($error)==0){
if (isset($_FILES['image']['name'])){
move_uploaded_file($userfile_tmp, $large_image_location);
chmod($large_image_location, 0777);
$width = getWidth($large_image_location);
$height = getHeight($large_image_location);
//Scale the image if it is greater than the width set above
if ($width > $max_width){
$scale = $max_width/$width;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
} else {
$scale = 1;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
}
//Delete the thumbnail file so the user can create a new one
if (file_exists($thumb_image_location)) {
unlink($thumb_image_location);
}
}
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab5\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}

// normal upload
if (isset($_POST["normal_upload5"])) {
	//Get the file information
	$userfile_name = $_FILES['image']['name'];
	$userfile_tmp = $_FILES['image']['tmp_name'];
    $userfile_type = $_FILES['image']['type'];
	$userfile_size = $_FILES['image']['size'];
	$filename = basename($_FILES['image']['name']);
	$file_ext = substr($filename, strrpos($filename, '.') + 1);

	//Only process if the file is a JPG and below the allowed limit
	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {

foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . "<strong>".$image_ext."</strong>" . __("images accepted for upload", TEMPLATE_DOMAIN) . "<br />";
}
}



if ($userfile_size > $max_file ) {
 $error= __("ONLY images under 1MB are accepted for upload", TEMPLATE_DOMAIN);
}

} else {
$error= __("Select an image for upload", TEMPLATE_DOMAIN);
}
	//Everything is ok, so we can upload the image.
	if (strlen($error)==0){

		if (isset($_FILES['image']['name'])){

			move_uploaded_file($userfile_tmp, $normal_image_location);
			chmod($normal_image_location, 0777);


			//Delete the thumbnail file so the user can create a new one
			if (file_exists($thumb_image_location)) {
				unlink($thumb_image_location);
			}
		}
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab5\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}


if (isset($_POST['upload_thumbnail5']) && strlen($large_photo_exists)>0) {
//Get the new coordinates to crop the image.
$x1 = $_POST["x1"];
$y1 = $_POST["y1"];
$x2 = $_POST["x2"];
$y2 = $_POST["y2"];
$w = $_POST["w"];
$h = $_POST["h"];
//Scale the image to the thumb_width set above
$scale = $thumb_width/$w;
$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab5\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
?>

<?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
$current_large_image_width = getWidth($large_image_location);
$current_large_image_height = getHeight($large_image_location);?>

<script type="text/javascript">
function preview(img, selection) {
var scaleX = <?php echo $thumb_width;?> / selection.width;
var scaleY = <?php echo $thumb_height;?> / selection.height;
$('#thumbnail + div > img').css({
width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px',
height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
});
$('#x1').val(selection.x1);
$('#y1').val(selection.y1);
$('#x2').val(selection.x2);
$('#y2').val(selection.y2);
$('#w').val(selection.width);
$('#h').val(selection.height);
}
$(document).ready(function () {
$('#save_thumb').click(function() {
var x1 = $('#x1').val();
var y1 = $('#y1').val();
var x2 = $('#x2').val();
var y2 = $('#y2').val();
var w = $('#w').val();
var h = $('#h').val();
if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
alert("You must make a selection first");
return false;
} else {
return true;
}
});
});
function selectionStart(img, selection) { width:260;height:150 }
$(window).load(function () {
$('#thumbnail').imgAreaSelect({ onSelectStart: selectionStart, resizable: true, x1: 20, y1: 20, x2: 280, y2: 170, aspectRatio: '28:17', onSelectChange: preview });
});
</script>
<?php } ?>



<?php
if (isset($_POST['delete_thumbnail5'])){
unlink($upload_path . $large_image_name);
unlink($upload_path . $thumb_image_name);
echo "<h5 id='file-delete'>" . __('File successfully deleted', TEMPLATE_DOMAIN) . "</h4>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab5\">";
exit();
}
if (isset($_POST["delete_normal_upload5"])){
unlink("$upload_path/$normal_image_name");
echo "<h5 id='file-delete'>" . __('File successfully deleted', TEMPLATE_DOMAIN) . "</h4>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab5\">";
exit();
}
?>


<?php
//Display error message if there are any
if(strlen($error)>0){
echo "<p class=\"uperror\"><strong>" . __("Error!",TEMPLATE_DOMAIN) . "&nbsp;</strong>" . $error . "</p>";
}

if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0) { ?>
<img src="<?php echo "$upload_url/$thumb_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<div class="submit"><input type="submit" name="delete_thumbnail5" class="sbutton button-secondary" value="<?php _e("Delete This Image",TEMPLATE_DOMAIN); ?>" /></div>
</form>

<?php } else if(strlen($normal_photo_exists)>0 ){ ?>
<img src="<?php echo "$upload_url/$normal_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<input type="submit" name="delete_normal_upload5" class="button-secondary" value="Delete This Image" />
</form>
<?php } ?>


<?php
if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){ ?>
<?php } else { ?>
<?php if(strlen($large_photo_exists)>0){ ?>
<h3><?php _e('Crop And Save Your Thumbnail', TEMPLATE_DOMAIN); ?></h3>
<div>
<img src="<?php echo "$upload_url/$large_image_name"; ?>" style="clear: both; margin-bottom: 10px;" id="thumbnail" alt="Create Thumbnail" />
<br style="clear:both;"/>

<form name="thumbnail" action="" method="post">
<input type="hidden" name="x1" value="" id="x1" />
<input type="hidden" name="y1" value="" id="y1" />
<input type="hidden" name="x2" value="" id="x2" />
<input type="hidden" name="y2" value="" id="y2" />
<input type="hidden" name="w" value="" id="w" />
<input type="hidden" name="h" value="" id="h" />
<input type="submit" name="upload_thumbnail5" class="button-secondary" value="Save Thumbnail" id="save_thumb" />
</form>
</div>

<?php } } ?>


<?php if(strlen($large_photo_exists)==0 && strlen($normal_photo_exists)==0){ ?>
<h3><?php _e("Upload Image",TEMPLATE_DOMAIN); ?> <?php echo $thumb_width . ' X ' . $thumb_height; ?></h3>
<form name="photo" enctype="multipart/form-data" action="<?php echo admin_url('themes.php?page=custom-homepage.php&#tab5'); ?>" method="post">
<input type="file" class="ups" name="image" />
<input type="submit" class="button-secondary" name="upload5" value="Upload and crop&raquo;" />&nbsp;&nbsp;&nbsp;<input type="submit" class="button-secondary" name="normal_upload5" value="Upload &raquo;" />
<p class="onlyjpg">* only <?php echo $image_ext; ?> image file are allowed</p>
</form>
<?php } ?>

<br />

<form method="post">

<?php foreach ($options8 as $value) {   ?>

<?php
switch ( $value['type'] ) {
case 'text':
?>

<div class="description"><?php echo $value['name']; ?></div>
<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id']);
} else {
echo $value['std']; } ?>" />
</p>

<?php
break;
case 'textarea':
?>

<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$video_code = get_option($valuey);
?>
<div class="description"><?php echo $value['name']; ?></div>
<p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?></textarea></p>

<?php
break;
default;
?>



<?php
break;
} ?>

<?php } ?>

<p class="submit">
<input name="save" type="submit" class="sbutton button-primary" value="<?php _e("Save setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="save8" />
</p>
</form>

<form method="post">
<p class="submit">
<input name="reset" type="submit" class="sbutton button-primary" value="<?php _e("Reset setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="reset8" />
</p>
</form>

</div>
</div>
</div>

</div><!-- end tabc -->

<?php
echo "<div id='tab6' class='tabc'>";

$normal_image_name = $image_prefix_name . '6_normal.jpg'; 		     // New name of the large image
$large_image_name = $image_prefix_name . '6.jpg'; 		     // New name of the large image
$thumb_image_name = $image_prefix_name . '6_thumb.jpg'; 	// New name of the thumbnail image
$max_file = "1000000"; 						        // Approx below 1MB
$max_width = "850";							        // Max width allowed for the large image
$thumb_width = "260";						        // Width of thumbnail image
$thumb_height = "150";                              // Height of thumbnail image

//Image Locations
$normal_image_location = $upload_path . $normal_image_name;
$large_image_location = $upload_path . $large_image_name;
$thumb_image_location = $upload_path . $thumb_image_name;


//Check to see if any images with the same names already exist
if (file_exists($large_image_location)){
if (file_exists($thumb_image_location)){
$thumb_photo_exists = "<img src=\"".$upload_path.$thumb_image_name."\" alt=\"Thumbnail Image\"/>";
} else {
$thumb_photo_exists = "";
}
$large_photo_exists = "<img src=\"".$upload_path.$large_image_name."\" alt=\"Large Image\"/>";
} else {
$large_photo_exists = "";
$thumb_photo_exists = "";
}
//Check normal img
if (file_exists($normal_image_location)){
   	$normal_photo_exists = "<img src=\"".$upload_path.$normal_image_name."\" alt=\"Large Image\"/>";
} else {
   	$normal_photo_exists = "";
}

?>


<?php
global $themename, $shortname, $options9;
if ( $_REQUEST['saved9'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 6 Settings saved.',TEMPLATE_DOMAIN) . '</strong></p></div>';
if ( $_REQUEST['reset9'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 6 Settings reset.',TEMPLATE_DOMAIN) . '</strong></p></div>';
?>


<h4><?php _e("Featured image 6 Setting",TEMPLATE_DOMAIN); ?></h4>
<div class="tab-option">
<div class="option-save">

<?php

if (isset($_POST['upload6'])) {
//Get the file information
$userfile_name = $_FILES['image']['name'];
$userfile_tmp = $_FILES['image']['tmp_name'];
$userfile_type = $_FILES['image']['type'];
$userfile_size = $_FILES['image']['size'];
$filename = basename($_FILES['image']['name']);
$file_ext = substr($filename, strrpos($filename, '.') + 1);

//Only process if the file is a JPG, PNG or GIF and below the allowed limit

if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . " <strong>".$image_ext."</strong>" . __(" images accepted for upload",TEMPLATE_DOMAIN) . "<br />";
}
}

//check if the file size is above the allowed limit

if ($userfile_size > $max_file ) {
$error.= __("Images must be under 1 MB in size",TEMPLATE_DOMAIN);
}

} else {
$error= __("Select an image for upload",TEMPLATE_DOMAIN);
}

//Everything is ok, so we can upload the image.

if (strlen($error)==0){
if (isset($_FILES['image']['name'])){
move_uploaded_file($userfile_tmp, $large_image_location);
chmod($large_image_location, 0777);
$width = getWidth($large_image_location);
$height = getHeight($large_image_location);
//Scale the image if it is greater than the width set above
if ($width > $max_width){
$scale = $max_width/$width;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
} else {
$scale = 1;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
}
//Delete the thumbnail file so the user can create a new one
if (file_exists($thumb_image_location)) {
unlink($thumb_image_location);
}
}
//Refresh the page to show the new uploaded image
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab6\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}

// normal upload
if (isset($_POST["normal_upload6"])) {
	//Get the file information
	$userfile_name = $_FILES['image']['name'];
	$userfile_tmp = $_FILES['image']['tmp_name'];
    $userfile_type = $_FILES['image']['type'];
	$userfile_size = $_FILES['image']['size'];
	$filename = basename($_FILES['image']['name']);
	$file_ext = substr($filename, strrpos($filename, '.') + 1);

	//Only process if the file is a JPG and below the allowed limit
	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {

foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . "<strong>".$image_ext."</strong>" . __("images accepted for upload", TEMPLATE_DOMAIN) . "<br />";
}
}



if($userfile_size > $max_file){
            $error= __("ONLY images under 1MB are accepted for upload", TEMPLATE_DOMAIN);
        }
	} else {
		$error= __("Select an image for upload", TEMPLATE_DOMAIN);
	}
	//Everything is ok, so we can upload the image.
	if (strlen($error)==0){

		if (isset($_FILES['image']['name'])){

			move_uploaded_file($userfile_tmp, $normal_image_location);
			chmod($normal_image_location, 0777);


			//Delete the thumbnail file so the user can create a new one
			if (file_exists($thumb_image_location)) {
				unlink($thumb_image_location);
			}
		}

//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab6\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}


if (isset($_POST['upload_thumbnail6']) && strlen($large_photo_exists)>0) {
//Get the new coordinates to crop the image.
$x1 = $_POST["x1"];
$y1 = $_POST["y1"];
$x2 = $_POST["x2"];
$y2 = $_POST["y2"];
$w = $_POST["w"];
$h = $_POST["h"];
//Scale the image to the thumb_width set above
$scale = $thumb_width/$w;
$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab6\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
?>

<?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
$current_large_image_width = getWidth($large_image_location);
$current_large_image_height = getHeight($large_image_location);?>

<script type="text/javascript">
function preview(img, selection) {
var scaleX = <?php echo $thumb_width;?> / selection.width;
var scaleY = <?php echo $thumb_height;?> / selection.height;
$('#thumbnail + div > img').css({
width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px',
height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
});
$('#x1').val(selection.x1);
$('#y1').val(selection.y1);
$('#x2').val(selection.x2);
$('#y2').val(selection.y2);
$('#w').val(selection.width);
$('#h').val(selection.height);
}
$(document).ready(function () {
$('#save_thumb').click(function() {
var x1 = $('#x1').val();
var y1 = $('#y1').val();
var x2 = $('#x2').val();
var y2 = $('#y2').val();
var w = $('#w').val();
var h = $('#h').val();
if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
alert("You must make a selection first");
return false;
} else {
return true;
}
});
});
function selectionStart(img, selection) { width:260;height:150 }
$(window).load(function () {
$('#thumbnail').imgAreaSelect({ onSelectStart: selectionStart, resizable: true, x1: 20, y1: 20, x2: 280, y2: 170, aspectRatio: '28:17', onSelectChange: preview });
});
</script>
<?php } ?>



<?php
if (isset($_POST['delete_thumbnail6'])){
unlink($upload_path . $large_image_name);
unlink($upload_path . $thumb_image_name);
echo "<h5 id='file-delete'>" . __('File successfully deleted',TEMPLATE_DOMAIN) . "</h5>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab6\">";
exit();
}
if (isset($_POST["delete_normal_upload6"])){
unlink("$upload_path/$normal_image_name");
echo "<h5 id='file-delete'>" . __('File successfully deleted',TEMPLATE_DOMAIN) . "</h5>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab6\">";
exit();
}
?>


<?php
//Display error message if there are any
if(strlen($error)>0){
echo "<p class=\"uperror\"><strong>" . __("Error!",TEMPLATE_DOMAIN) . "&nbsp;</strong>" . $error . "</p>";
}

if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0) { ?>
<img src="<?php echo "$upload_url/$thumb_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<div class="submit"><input type="submit" name="delete_thumbnail6" class="sbutton button-secondary" value="<?php _e("Delete This Image",TEMPLATE_DOMAIN); ?>" /></div>
</form>

<?php } else if(strlen($normal_photo_exists)>0 ){ ?>
<img src="<?php echo "$upload_url/$normal_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<input type="submit" name="delete_normal_upload6" class="button-secondary" value="Delete This Image" />
</form>
<?php } ?>

<?php if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){ ?>
<?php } else { ?>
<?php if(strlen($large_photo_exists)>0){ ?>
<h3><?php _e('Crop And Save Your Thumbnail', TEMPLATE_DOMAIN); ?></h3>
<div>
<img src="<?php echo "$upload_url/$large_image_name"; ?>" style="clear: both; margin-bottom: 10px;" id="thumbnail" alt="Create Thumbnail" />
<br style="clear:both;"/>

<form name="thumbnail" action="" method="post">
<input type="hidden" name="x1" value="" id="x1" />
<input type="hidden" name="y1" value="" id="y1" />
<input type="hidden" name="x2" value="" id="x2" />
<input type="hidden" name="y2" value="" id="y2" />
<input type="hidden" name="w" value="" id="w" />
<input type="hidden" name="h" value="" id="h" />
<input type="submit" name="upload_thumbnail6" class="button-secondary" value="Save Thumbnail" id="save_thumb" />
</form>
</div>

<?php } } ?>

<?php if(strlen($large_photo_exists)==0 && strlen($normal_photo_exists)==0){ ?>
<h3><?php _e("Upload Image",TEMPLATE_DOMAIN); ?> <?php echo $thumb_width . ' X ' . $thumb_height; ?></h3>
<form name="photo" enctype="multipart/form-data" action="<?php echo admin_url('themes.php?page=custom-homepage.php&#tab6'); ?>" method="post">
<input type="file" class="ups" name="image" />
<input type="submit" class="button-secondary" name="upload6" value="Upload and crop&raquo;" />&nbsp;&nbsp;&nbsp;<input type="submit" class="button-secondary" name="normal_upload6" value="Upload &raquo;" />
<p class="onlyjpg">* <?php _e("only",TEMPLATE_DOMAIN); ?> <?php echo $image_ext; ?> <?php _e("image file are allowed",TEMPLATE_DOMAIN); ?></p>
</form>
<?php } ?>

<br />

<form method="post">

<?php foreach ($options9 as $value) {   ?>

<?php
switch ( $value['type'] ) {
case 'text':
?>

<div class="description"><?php echo $value['name']; ?></div>
<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id']);
} else {
echo $value['std']; } ?>" />
</p>

<?php
break;
case 'textarea':
?>

<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$video_code = get_option($valuey);
?>
<div class="description"><?php echo $value['name']; ?></div>
<p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?></textarea></p>

<?php
break;
default;
?>



<?php
break;
} ?>

<?php } ?>

<p class="submit">
<input name="save" type="submit" class="sbutton button-primary" value="<?php _e("Save setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="save9" />
</p>
</form>

<form method="post">
<p class="submit">
<input name="reset" type="submit" class="sbutton button-primary" value="<?php _e("Reset setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="reset9" />
</p>
</form>

</div>
</div>
</div>






<?php
echo "<div id='tab7' class='tabc'>";

$normal_image_name = $image_prefix_name . '7_normal.jpg'; 		     // New name of the large image
$large_image_name = $image_prefix_name . '7.jpg'; 		     // New name of the large image
$thumb_image_name = $image_prefix_name . '7_thumb.jpg'; 	// New name of the thumbnail image
$max_file = "1000000"; 						        // Approx below 1MB
$max_width = "850";							        // Max width allowed for the large image
$thumb_width = "260";						        // Width of thumbnail image
$thumb_height = "150";                              // Height of thumbnail image

//Image Locations
$normal_image_location = $upload_path . $normal_image_name;
$large_image_location = $upload_path . $large_image_name;
$thumb_image_location = $upload_path . $thumb_image_name;


//Check to see if any images with the same names already exist
if (file_exists($large_image_location)){
if (file_exists($thumb_image_location)){
$thumb_photo_exists = "<img src=\"".$upload_path.$thumb_image_name."\" alt=\"Thumbnail Image\"/>";
} else {
$thumb_photo_exists = "";
}
$large_photo_exists = "<img src=\"".$upload_path.$large_image_name."\" alt=\"Large Image\"/>";
} else {
$large_photo_exists = "";
$thumb_photo_exists = "";
}
//Check normal img
if (file_exists($normal_image_location)){
   	$normal_photo_exists = "<img src=\"".$upload_path.$normal_image_name."\" alt=\"Large Image\"/>";
} else {
   	$normal_photo_exists = "";
}

?>


<?php
global $themename, $shortname, $options10;
if ( $_REQUEST['saved10'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 7 Settings saved.',TEMPLATE_DOMAIN) . '</strong></p></div>';
if ( $_REQUEST['reset10'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 7 Settings reset.',TEMPLATE_DOMAIN) . '</strong></p></div>';
?>


<h4><?php _e("Featured image 7 Setting",TEMPLATE_DOMAIN); ?></h4>
<div class="tab-option">
<div class="option-save">

<?php

if (isset($_POST['upload7'])) {
//Get the file information
$userfile_name = $_FILES['image']['name'];
$userfile_tmp = $_FILES['image']['tmp_name'];
$userfile_type = $_FILES['image']['type'];
$userfile_size = $_FILES['image']['size'];
$filename = basename($_FILES['image']['name']);
$file_ext = substr($filename, strrpos($filename, '.') + 1);

//Only process if the file is a JPG, PNG or GIF and below the allowed limit

if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . " <strong>".$image_ext."</strong>" . __(" images accepted for upload",TEMPLATE_DOMAIN) . "<br />";
}
}

//check if the file size is above the allowed limit

if ($userfile_size > $max_file ) {
$error.= __("Images must be under 1 MB in size",TEMPLATE_DOMAIN);
}

} else {
$error= __("Select an image for upload",TEMPLATE_DOMAIN);
}

//Everything is ok, so we can upload the image.

if (strlen($error)==0){
if (isset($_FILES['image']['name'])){
move_uploaded_file($userfile_tmp, $large_image_location);
chmod($large_image_location, 0777);
$width = getWidth($large_image_location);
$height = getHeight($large_image_location);
//Scale the image if it is greater than the width set above
if ($width > $max_width){
$scale = $max_width/$width;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
} else {
$scale = 1;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
}
//Delete the thumbnail file so the user can create a new one
if (file_exists($thumb_image_location)) {
unlink($thumb_image_location);
}
}
//Refresh the page to show the new uploaded image
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab7\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}

// normal upload
if (isset($_POST["normal_upload7"])) {
	//Get the file information
	$userfile_name = $_FILES['image']['name'];
	$userfile_tmp = $_FILES['image']['tmp_name'];
    $userfile_type = $_FILES['image']['type'];
	$userfile_size = $_FILES['image']['size'];
	$filename = basename($_FILES['image']['name']);
	$file_ext = substr($filename, strrpos($filename, '.') + 1);

	//Only process if the file is a JPG and below the allowed limit
	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {

foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . "<strong>".$image_ext."</strong>" . __("images accepted for upload", TEMPLATE_DOMAIN) . "<br />";
}
}



if($userfile_size > $max_file){
            $error= __("ONLY images under 1MB are accepted for upload", TEMPLATE_DOMAIN);
        }
	} else {
		$error= __("Select an image for upload", TEMPLATE_DOMAIN);
	}
	//Everything is ok, so we can upload the image.
	if (strlen($error)==0){

		if (isset($_FILES['image']['name'])){

			move_uploaded_file($userfile_tmp, $normal_image_location);
			chmod($normal_image_location, 0777);


			//Delete the thumbnail file so the user can create a new one
			if (file_exists($thumb_image_location)) {
				unlink($thumb_image_location);
			}
		}

//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab7\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}


if (isset($_POST['upload_thumbnail7']) && strlen($large_photo_exists)>0) {
//Get the new coordinates to crop the image.
$x1 = $_POST["x1"];
$y1 = $_POST["y1"];
$x2 = $_POST["x2"];
$y2 = $_POST["y2"];
$w = $_POST["w"];
$h = $_POST["h"];
//Scale the image to the thumb_width set above
$scale = $thumb_width/$w;
$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab7\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
?>

<?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
$current_large_image_width = getWidth($large_image_location);
$current_large_image_height = getHeight($large_image_location);?>

<script type="text/javascript">
function preview(img, selection) {
var scaleX = <?php echo $thumb_width;?> / selection.width;
var scaleY = <?php echo $thumb_height;?> / selection.height;
$('#thumbnail + div > img').css({
width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px',
height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
});
$('#x1').val(selection.x1);
$('#y1').val(selection.y1);
$('#x2').val(selection.x2);
$('#y2').val(selection.y2);
$('#w').val(selection.width);
$('#h').val(selection.height);
}
$(document).ready(function () {
$('#save_thumb').click(function() {
var x1 = $('#x1').val();
var y1 = $('#y1').val();
var x2 = $('#x2').val();
var y2 = $('#y2').val();
var w = $('#w').val();
var h = $('#h').val();
if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
alert("You must make a selection first");
return false;
} else {
return true;
}
});
});
function selectionStart(img, selection) { width:260;height:150 }
$(window).load(function () {
$('#thumbnail').imgAreaSelect({ onSelectStart: selectionStart, resizable: true, x1: 20, y1: 20, x2: 280, y2: 170, aspectRatio: '28:17', onSelectChange: preview });
});
</script>
<?php } ?>



<?php
if (isset($_POST['delete_thumbnail7'])){
unlink($upload_path . $large_image_name);
unlink($upload_path . $thumb_image_name);
echo "<h5 id='file-delete'>" . __('File successfully deleted',TEMPLATE_DOMAIN) . "</h5>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab7\">";
exit();
}
if (isset($_POST["delete_normal_upload7"])){
unlink("$upload_path/$normal_image_name");
echo "<h5 id='file-delete'>" . __('File successfully deleted',TEMPLATE_DOMAIN) . "</h5>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab7\">";
exit();
}
?>


<?php
//Display error message if there are any
if(strlen($error)>0){
echo "<p class=\"uperror\"><strong>" . __("Error!",TEMPLATE_DOMAIN) . "&nbsp;</strong>" . $error . "</p>";
}

if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0) { ?>
<img src="<?php echo "$upload_url/$thumb_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<div class="submit"><input type="submit" name="delete_thumbnail7" class="sbutton button-secondary" value="<?php _e("Delete This Image",TEMPLATE_DOMAIN); ?>" /></div>
</form>

<?php } else if(strlen($normal_photo_exists)>0 ){ ?>
<img src="<?php echo "$upload_url/$normal_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<input type="submit" name="delete_normal_upload7" class="button-secondary" value="Delete This Image" />
</form>
<?php } ?>

<?php if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){ ?>
<?php } else { ?>
<?php if(strlen($large_photo_exists)>0){ ?>
<h3><?php _e('Crop And Save Your Thumbnail', TEMPLATE_DOMAIN); ?></h3>
<div>
<img src="<?php echo "$upload_url/$large_image_name"; ?>" style="clear: both; margin-bottom: 10px;" id="thumbnail" alt="Create Thumbnail" />
<br style="clear:both;"/>

<form name="thumbnail" action="" method="post">
<input type="hidden" name="x1" value="" id="x1" />
<input type="hidden" name="y1" value="" id="y1" />
<input type="hidden" name="x2" value="" id="x2" />
<input type="hidden" name="y2" value="" id="y2" />
<input type="hidden" name="w" value="" id="w" />
<input type="hidden" name="h" value="" id="h" />
<input type="submit" name="upload_thumbnail7" class="button-secondary" value="Save Thumbnail" id="save_thumb" />
</form>
</div>

<?php } } ?>

<?php if(strlen($large_photo_exists)==0 && strlen($normal_photo_exists)==0){ ?>
<h3><?php _e("Upload Image",TEMPLATE_DOMAIN); ?> <?php echo $thumb_width . ' X ' . $thumb_height; ?></h3>
<form name="photo" enctype="multipart/form-data" action="<?php echo admin_url('themes.php?page=custom-homepage.php&#tab7'); ?>" method="post">
<input type="file" class="ups" name="image" />
<input type="submit" class="button-secondary" name="upload7" value="Upload and crop&raquo;" />&nbsp;&nbsp;&nbsp;<input type="submit" class="button-secondary" name="normal_upload7" value="Upload &raquo;" />
<p class="onlyjpg">* <?php _e("only",TEMPLATE_DOMAIN); ?> <?php echo $image_ext; ?> <?php _e("image file are allowed",TEMPLATE_DOMAIN); ?></p>
</form>
<?php } ?>

<br />

<form method="post">

<?php foreach ($options10 as $value) {   ?>

<?php
switch ( $value['type'] ) {
case 'text':
?>

<div class="description"><?php echo $value['name']; ?></div>
<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id']);
} else {
echo $value['std']; } ?>" />
</p>

<?php
break;
case 'textarea':
?>

<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$video_code = get_option($valuey);
?>
<div class="description"><?php echo $value['name']; ?></div>
<p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?></textarea></p>

<?php
break;
default;
?>



<?php
break;
} ?>

<?php } ?>

<p class="submit">
<input name="save" type="submit" class="sbutton button-primary" value="<?php _e("Save setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="save10" />
</p>
</form>

<form method="post">
<p class="submit">
<input name="reset" type="submit" class="sbutton button-primary" value="<?php _e("Reset setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="reset10" />
</p>
</form>

</div>
</div>
</div>





<?php
echo "<div id='tab8' class='tabc'>";

$normal_image_name = $image_prefix_name . '8_normal.jpg'; 		     // New name of the large image
$large_image_name = $image_prefix_name . '8.jpg'; 		     // New name of the large image
$thumb_image_name = $image_prefix_name . '8_thumb.jpg'; 	// New name of the thumbnail image
$max_file = "1000000"; 						        // Approx below 1MB
$max_width = "850";							        // Max width allowed for the large image
$thumb_width = "260";						        // Width of thumbnail image
$thumb_height = "150";                              // Height of thumbnail image

//Image Locations
$normal_image_location = $upload_path . $normal_image_name;
$large_image_location = $upload_path . $large_image_name;
$thumb_image_location = $upload_path . $thumb_image_name;


//Check to see if any images with the same names already exist
if (file_exists($large_image_location)){
if (file_exists($thumb_image_location)){
$thumb_photo_exists = "<img src=\"".$upload_path.$thumb_image_name."\" alt=\"Thumbnail Image\"/>";
} else {
$thumb_photo_exists = "";
}
$large_photo_exists = "<img src=\"".$upload_path.$large_image_name."\" alt=\"Large Image\"/>";
} else {
$large_photo_exists = "";
$thumb_photo_exists = "";
}
//Check normal img
if (file_exists($normal_image_location)){
   	$normal_photo_exists = "<img src=\"".$upload_path.$normal_image_name."\" alt=\"Large Image\"/>";
} else {
   	$normal_photo_exists = "";
}

?>


<?php
global $themename, $shortname, $options11;
if ( $_REQUEST['saved11'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 8 Settings saved.',TEMPLATE_DOMAIN) . '</strong></p></div>';
if ( $_REQUEST['reset11'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 8 Settings reset.',TEMPLATE_DOMAIN) . '</strong></p></div>';
?>


<h4><?php _e("Featured image 8 Setting",TEMPLATE_DOMAIN); ?></h4>
<div class="tab-option">
<div class="option-save">

<?php

if (isset($_POST['upload8'])) {
//Get the file information
$userfile_name = $_FILES['image']['name'];
$userfile_tmp = $_FILES['image']['tmp_name'];
$userfile_type = $_FILES['image']['type'];
$userfile_size = $_FILES['image']['size'];
$filename = basename($_FILES['image']['name']);
$file_ext = substr($filename, strrpos($filename, '.') + 1);

//Only process if the file is a JPG, PNG or GIF and below the allowed limit

if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . " <strong>".$image_ext."</strong>" . __(" images accepted for upload",TEMPLATE_DOMAIN) . "<br />";
}
}

//check if the file size is above the allowed limit

if ($userfile_size > $max_file ) {
$error.= __("Images must be under 1 MB in size",TEMPLATE_DOMAIN);
}

} else {
$error= __("Select an image for upload",TEMPLATE_DOMAIN);
}

//Everything is ok, so we can upload the image.

if (strlen($error)==0){
if (isset($_FILES['image']['name'])){
move_uploaded_file($userfile_tmp, $large_image_location);
chmod($large_image_location, 0777);
$width = getWidth($large_image_location);
$height = getHeight($large_image_location);
//Scale the image if it is greater than the width set above
if ($width > $max_width){
$scale = $max_width/$width;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
} else {
$scale = 1;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
}
//Delete the thumbnail file so the user can create a new one
if (file_exists($thumb_image_location)) {
unlink($thumb_image_location);
}
}
//Refresh the page to show the new uploaded image
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab8\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}

// normal upload
if (isset($_POST["normal_upload8"])) {
	//Get the file information
	$userfile_name = $_FILES['image']['name'];
	$userfile_tmp = $_FILES['image']['tmp_name'];
    $userfile_type = $_FILES['image']['type'];
	$userfile_size = $_FILES['image']['size'];
	$filename = basename($_FILES['image']['name']);
	$file_ext = substr($filename, strrpos($filename, '.') + 1);

	//Only process if the file is a JPG and below the allowed limit
	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {

foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . "<strong>".$image_ext."</strong>" . __("images accepted for upload", TEMPLATE_DOMAIN) . "<br />";
}
}



if($userfile_size > $max_file){
            $error= __("ONLY images under 1MB are accepted for upload", TEMPLATE_DOMAIN);
        }
	} else {
		$error= __("Select an image for upload", TEMPLATE_DOMAIN);
	}
	//Everything is ok, so we can upload the image.
	if (strlen($error)==0){

		if (isset($_FILES['image']['name'])){

			move_uploaded_file($userfile_tmp, $normal_image_location);
			chmod($normal_image_location, 0777);


			//Delete the thumbnail file so the user can create a new one
			if (file_exists($thumb_image_location)) {
				unlink($thumb_image_location);
			}
		}

//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab8\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}


if (isset($_POST['upload_thumbnail8']) && strlen($large_photo_exists)>0) {
//Get the new coordinates to crop the image.
$x1 = $_POST["x1"];
$y1 = $_POST["y1"];
$x2 = $_POST["x2"];
$y2 = $_POST["y2"];
$w = $_POST["w"];
$h = $_POST["h"];
//Scale the image to the thumb_width set above
$scale = $thumb_width/$w;
$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab8\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
?>

<?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
$current_large_image_width = getWidth($large_image_location);
$current_large_image_height = getHeight($large_image_location);?>

<script type="text/javascript">
function preview(img, selection) {
var scaleX = <?php echo $thumb_width;?> / selection.width;
var scaleY = <?php echo $thumb_height;?> / selection.height;
$('#thumbnail + div > img').css({
width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px',
height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
});
$('#x1').val(selection.x1);
$('#y1').val(selection.y1);
$('#x2').val(selection.x2);
$('#y2').val(selection.y2);
$('#w').val(selection.width);
$('#h').val(selection.height);
}
$(document).ready(function () {
$('#save_thumb').click(function() {
var x1 = $('#x1').val();
var y1 = $('#y1').val();
var x2 = $('#x2').val();
var y2 = $('#y2').val();
var w = $('#w').val();
var h = $('#h').val();
if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
alert("You must make a selection first");
return false;
} else {
return true;
}
});
});
function selectionStart(img, selection) { width:260;height:150 }
$(window).load(function () {
$('#thumbnail').imgAreaSelect({ onSelectStart: selectionStart, resizable: true, x1: 20, y1: 20, x2: 280, y2: 170, aspectRatio: '28:17', onSelectChange: preview });
});
</script>
<?php } ?>



<?php
if (isset($_POST['delete_thumbnail8'])){
unlink($upload_path . $large_image_name);
unlink($upload_path . $thumb_image_name);
echo "<h5 id='file-delete'>" . __('File successfully deleted',TEMPLATE_DOMAIN) . "</h5>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab8\">";
exit();
}
if (isset($_POST["delete_normal_upload8"])){
unlink("$upload_path/$normal_image_name");
echo "<h5 id='file-delete'>" . __('File successfully deleted',TEMPLATE_DOMAIN) . "</h5>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab8\">";
exit();
}
?>


<?php
//Display error message if there are any
if(strlen($error)>0){
echo "<p class=\"uperror\"><strong>" . __("Error!",TEMPLATE_DOMAIN) . "&nbsp;</strong>" . $error . "</p>";
}

if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0) { ?>
<img src="<?php echo "$upload_url/$thumb_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<div class="submit"><input type="submit" name="delete_thumbnail8" class="sbutton button-secondary" value="<?php _e("Delete This Image",TEMPLATE_DOMAIN); ?>" /></div>
</form>

<?php } else if(strlen($normal_photo_exists)>0 ){ ?>
<img src="<?php echo "$upload_url/$normal_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<input type="submit" name="delete_normal_upload8" class="button-secondary" value="Delete This Image" />
</form>
<?php } ?>

<?php if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){ ?>
<?php } else { ?>
<?php if(strlen($large_photo_exists)>0){ ?>
<h3><?php _e('Crop And Save Your Thumbnail', TEMPLATE_DOMAIN); ?></h3>
<div>
<img src="<?php echo "$upload_url/$large_image_name"; ?>" style="clear: both; margin-bottom: 10px;" id="thumbnail" alt="Create Thumbnail" />
<br style="clear:both;"/>

<form name="thumbnail" action="" method="post">
<input type="hidden" name="x1" value="" id="x1" />
<input type="hidden" name="y1" value="" id="y1" />
<input type="hidden" name="x2" value="" id="x2" />
<input type="hidden" name="y2" value="" id="y2" />
<input type="hidden" name="w" value="" id="w" />
<input type="hidden" name="h" value="" id="h" />
<input type="submit" name="upload_thumbnail8" class="button-secondary" value="Save Thumbnail" id="save_thumb" />
</form>
</div>

<?php } } ?>

<?php if(strlen($large_photo_exists)==0 && strlen($normal_photo_exists)==0){ ?>
<h3><?php _e("Upload Image",TEMPLATE_DOMAIN); ?> <?php echo $thumb_width . ' X ' . $thumb_height; ?></h3>
<form name="photo" enctype="multipart/form-data" action="<?php echo admin_url('themes.php?page=custom-homepage.php&#tab8'); ?>" method="post">
<input type="file" class="ups" name="image" />
<input type="submit" class="button-secondary" name="upload8" value="Upload and crop&raquo;" />&nbsp;&nbsp;&nbsp;<input type="submit" class="button-secondary" name="normal_upload8" value="Upload &raquo;" />
<p class="onlyjpg">* <?php _e("only",TEMPLATE_DOMAIN); ?> <?php echo $image_ext; ?> <?php _e("image file are allowed",TEMPLATE_DOMAIN); ?></p>
</form>
<?php } ?>

<br />

<form method="post">

<?php foreach ($options11 as $value) {   ?>

<?php
switch ( $value['type'] ) {
case 'text':
?>

<div class="description"><?php echo $value['name']; ?></div>
<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id']);
} else {
echo $value['std']; } ?>" />
</p>

<?php
break;
case 'textarea':
?>

<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$video_code = get_option($valuey);
?>
<div class="description"><?php echo $value['name']; ?></div>
<p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?></textarea></p>

<?php
break;
default;
?>



<?php
break;
} ?>

<?php } ?>

<p class="submit">
<input name="save" type="submit" class="sbutton button-primary" value="<?php _e("Save setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="save11" />
</p>
</form>

<form method="post">
<p class="submit">
<input name="reset" type="submit" class="sbutton button-primary" value="<?php _e("Reset setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="reset11" />
</p>
</form>

</div>
</div>
</div>






<?php
echo "<div id='tab9' class='tabc'>";

$normal_image_name = $image_prefix_name . '9_normal.jpg'; 		     // New name of the large image
$large_image_name = $image_prefix_name . '9.jpg'; 		     // New name of the large image
$thumb_image_name = $image_prefix_name . '9_thumb.jpg'; 	// New name of the thumbnail image
$max_file = "1000000"; 						        // Approx below 1MB
$max_width = "850";							        // Max width allowed for the large image
$thumb_width = "260";						        // Width of thumbnail image
$thumb_height = "150";                              // Height of thumbnail image

//Image Locations
$normal_image_location = $upload_path . $normal_image_name;
$large_image_location = $upload_path . $large_image_name;
$thumb_image_location = $upload_path . $thumb_image_name;


//Check to see if any images with the same names already exist
if (file_exists($large_image_location)){
if (file_exists($thumb_image_location)){
$thumb_photo_exists = "<img src=\"".$upload_path.$thumb_image_name."\" alt=\"Thumbnail Image\"/>";
} else {
$thumb_photo_exists = "";
}
$large_photo_exists = "<img src=\"".$upload_path.$large_image_name."\" alt=\"Large Image\"/>";
} else {
$large_photo_exists = "";
$thumb_photo_exists = "";
}
//Check normal img
if (file_exists($normal_image_location)){
   	$normal_photo_exists = "<img src=\"".$upload_path.$normal_image_name."\" alt=\"Large Image\"/>";
} else {
   	$normal_photo_exists = "";
}

?>


<?php
global $themename, $shortname, $options12;
if ( $_REQUEST['saved12'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 9 Settings saved.',TEMPLATE_DOMAIN) . '</strong></p></div>';
if ( $_REQUEST['reset12'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename. __(' Featured image 9 Settings reset.',TEMPLATE_DOMAIN) . '</strong></p></div>';
?>


<h4><?php _e("Featured image 9 Setting",TEMPLATE_DOMAIN); ?></h4>
<div class="tab-option">
<div class="option-save">

<?php

if (isset($_POST['upload9'])) {
//Get the file information
$userfile_name = $_FILES['image']['name'];
$userfile_tmp = $_FILES['image']['tmp_name'];
$userfile_type = $_FILES['image']['type'];
$userfile_size = $_FILES['image']['size'];
$filename = basename($_FILES['image']['name']);
$file_ext = substr($filename, strrpos($filename, '.') + 1);

//Only process if the file is a JPG, PNG or GIF and below the allowed limit

if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . " <strong>".$image_ext."</strong>" . __(" images accepted for upload",TEMPLATE_DOMAIN) . "<br />";
}
}

//check if the file size is above the allowed limit

if ($userfile_size > $max_file ) {
$error.= __("Images must be under 1 MB in size",TEMPLATE_DOMAIN);
}

} else {
$error= __("Select an image for upload",TEMPLATE_DOMAIN);
}

//Everything is ok, so we can upload the image.

if (strlen($error)==0){
if (isset($_FILES['image']['name'])){
move_uploaded_file($userfile_tmp, $large_image_location);
chmod($large_image_location, 0777);
$width = getWidth($large_image_location);
$height = getHeight($large_image_location);
//Scale the image if it is greater than the width set above
if ($width > $max_width){
$scale = $max_width/$width;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
} else {
$scale = 1;
$uploaded = resizeImage($large_image_location,$width,$height,$scale);
}
//Delete the thumbnail file so the user can create a new one
if (file_exists($thumb_image_location)) {
unlink($thumb_image_location);
}
}
//Refresh the page to show the new uploaded image
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab9\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}

// normal upload
if (isset($_POST["normal_upload9"])) {
	//Get the file information
	$userfile_name = $_FILES['image']['name'];
	$userfile_tmp = $_FILES['image']['tmp_name'];
    $userfile_type = $_FILES['image']['type'];
	$userfile_size = $_FILES['image']['size'];
	$filename = basename($_FILES['image']['name']);
	$file_ext = substr($filename, strrpos($filename, '.') + 1);

	//Only process if the file is a JPG and below the allowed limit
	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {

foreach ($allowed_image_types as $mime_type => $ext) {
//loop through the specified image types and if they match the extension then break out
//everything is ok so go and check file size
if($file_ext==$ext && $userfile_type==$mime_type){
$error = "";
break;
} else {
$error = __("Only",TEMPLATE_DOMAIN) . "<strong>".$image_ext."</strong>" . __("images accepted for upload", TEMPLATE_DOMAIN) . "<br />";
}
}



if($userfile_size > $max_file){
            $error= __("ONLY images under 1MB are accepted for upload", TEMPLATE_DOMAIN);
        }
	} else {
		$error= __("Select an image for upload", TEMPLATE_DOMAIN);
	}
	//Everything is ok, so we can upload the image.
	if (strlen($error)==0){

		if (isset($_FILES['image']['name'])){

			move_uploaded_file($userfile_tmp, $normal_image_location);
			chmod($normal_image_location, 0777);


			//Delete the thumbnail file so the user can create a new one
			if (file_exists($thumb_image_location)) {
				unlink($thumb_image_location);
			}
		}

//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab9\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
}


if (isset($_POST['upload_thumbnail9']) && strlen($large_photo_exists)>0) {
//Get the new coordinates to crop the image.
$x1 = $_POST["x1"];
$y1 = $_POST["y1"];
$x2 = $_POST["x2"];
$y2 = $_POST["y2"];
$w = $_POST["w"];
$h = $_POST["h"];
//Scale the image to the thumb_width set above
$scale = $thumb_width/$w;
$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);
//Refresh the page to show the new uploaded image
print '<meta http-equiv="Pragma" content="no-cache">';
echo "<h4 id=\"loading-bar\">Please wait..Your Image Currently Processing</h4>";
echo '<img src="' . get_template_directory_uri() . '/_inc/admin/loading_bar.gif' . '">';
print "<meta http-equiv=\"refresh\" content=\"5;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab9\">";
exit();
//double refresh to clear cache..its a bit catchy but it get the job done
}
?>

<?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
$current_large_image_width = getWidth($large_image_location);
$current_large_image_height = getHeight($large_image_location);?>

<script type="text/javascript">
function preview(img, selection) {
var scaleX = <?php echo $thumb_width;?> / selection.width;
var scaleY = <?php echo $thumb_height;?> / selection.height;
$('#thumbnail + div > img').css({
width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px',
height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
});
$('#x1').val(selection.x1);
$('#y1').val(selection.y1);
$('#x2').val(selection.x2);
$('#y2').val(selection.y2);
$('#w').val(selection.width);
$('#h').val(selection.height);
}
$(document).ready(function () {
$('#save_thumb').click(function() {
var x1 = $('#x1').val();
var y1 = $('#y1').val();
var x2 = $('#x2').val();
var y2 = $('#y2').val();
var w = $('#w').val();
var h = $('#h').val();
if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
alert("You must make a selection first");
return false;
} else {
return true;
}
});
});
function selectionStart(img, selection) { width:260;height:150 }
$(window).load(function () {
$('#thumbnail').imgAreaSelect({ onSelectStart: selectionStart, resizable: true, x1: 20, y1: 20, x2: 280, y2: 170, aspectRatio: '28:17', onSelectChange: preview });
});
</script>
<?php } ?>



<?php
if (isset($_POST['delete_thumbnail9'])){
unlink($upload_path . $large_image_name);
unlink($upload_path . $thumb_image_name);
echo "<h5 id='file-delete'>" . __('File successfully deleted',TEMPLATE_DOMAIN) . "</h5>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab9\">";
exit();
}
if (isset($_POST["delete_normal_upload9"])){
unlink("$upload_path/$normal_image_name");
echo "<h5 id='file-delete'>" . __('File successfully deleted',TEMPLATE_DOMAIN) . "</h5>";
print "<meta http-equiv=\"refresh\" content=\"1;url=$ttpl_url/wp-admin/themes.php?page=custom-homepage.php&#tab9\">";
exit();
}
?>


<?php
//Display error message if there are any
if(strlen($error)>0){
echo "<p class=\"uperror\"><strong>" . __("Error!",TEMPLATE_DOMAIN) . "&nbsp;</strong>" . $error . "</p>";
}

if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0) { ?>
<img src="<?php echo "$upload_url/$thumb_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<div class="submit"><input type="submit" name="delete_thumbnail9" class="sbutton button-secondary" value="<?php _e("Delete This Image",TEMPLATE_DOMAIN); ?>" /></div>
</form>

<?php } else if(strlen($normal_photo_exists)>0 ){ ?>
<img src="<?php echo "$upload_url/$normal_image_name"; ?>" class="timg"/><br /><br />
<form id="form-del" name="thumbnail" action="" method="post">
<input type="submit" name="delete_normal_upload9" class="button-secondary" value="Delete This Image" />
</form>
<?php } ?>

<?php if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){ ?>
<?php } else { ?>
<?php if(strlen($large_photo_exists)>0){ ?>
<h3><?php _e('Crop And Save Your Thumbnail', TEMPLATE_DOMAIN); ?></h3>
<div>
<img src="<?php echo "$upload_url/$large_image_name"; ?>" style="clear: both; margin-bottom: 10px;" id="thumbnail" alt="Create Thumbnail" />
<br style="clear:both;"/>

<form name="thumbnail" action="" method="post">
<input type="hidden" name="x1" value="" id="x1" />
<input type="hidden" name="y1" value="" id="y1" />
<input type="hidden" name="x2" value="" id="x2" />
<input type="hidden" name="y2" value="" id="y2" />
<input type="hidden" name="w" value="" id="w" />
<input type="hidden" name="h" value="" id="h" />
<input type="submit" name="upload_thumbnail9" class="button-secondary" value="Save Thumbnail" id="save_thumb" />
</form>
</div>

<?php } } ?>

<?php if(strlen($large_photo_exists)==0 && strlen($normal_photo_exists)==0){ ?>
<h3><?php _e("Upload Image",TEMPLATE_DOMAIN); ?> <?php echo $thumb_width . ' X ' . $thumb_height; ?></h3>
<form name="photo" enctype="multipart/form-data" action="<?php echo admin_url('themes.php?page=custom-homepage.php&#tab9'); ?>" method="post">
<input type="file" class="ups" name="image" />
<input type="submit" class="button-secondary" name="upload9" value="Upload and crop&raquo;" />&nbsp;&nbsp;&nbsp;<input type="submit" class="button-secondary" name="normal_upload9" value="Upload &raquo;" />
<p class="onlyjpg">* <?php _e("only",TEMPLATE_DOMAIN); ?> <?php echo $image_ext; ?> <?php _e("image file are allowed",TEMPLATE_DOMAIN); ?></p>
</form>
<?php } ?>

<br />

<form method="post">

<?php foreach ($options12 as $value) {   ?>

<?php
switch ( $value['type'] ) {
case 'text':
?>

<div class="description"><?php echo $value['name']; ?></div>
<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo get_option( $value['id']);
} else {
echo $value['std']; } ?>" />
</p>

<?php
break;
case 'textarea':
?>

<?php
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$video_code = get_option($valuey);
?>
<div class="description"><?php echo $value['name']; ?></div>
<p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); } else { echo $value['std']; } ?></textarea></p>

<?php
break;
default;
?>



<?php
break;
} ?>

<?php } ?>

<p class="submit">
<input name="save" type="submit" class="sbutton button-primary" value="<?php _e("Save setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="save12" />
</p>
</form>

<form method="post">
<p class="submit">
<input name="reset" type="submit" class="sbutton button-primary" value="<?php _e("Reset setting",TEMPLATE_DOMAIN); ?>" />
<input type="hidden" name="action" value="reset12" />
</p>
</form>

</div>
</div>
</div>

</div><!-- end tabc -->




<div id="reset-box">
<form method="post">
<div class="submit">
<input name="reset" type="submit" class="sbutton button-secondary" onclick="return confirm('Are you sure you want to delete all images and reset all text options?. This action cannot be restore.')" value="Delete all images and reset all text options" />
<input type="hidden" name="action" value="resetall" />&nbsp;&nbsp;<?php _e("by pressing this reset button, all your uploaded services images and saved text settings will be deleted.",TEMPLATE_DOMAIN); ?>
</div>
</form>
</div>

</div>
</div>

<?php }



function blogsmu_features_register() {
global $themename, $shortname, $options4, $options5, $options6, $options7, $options8, $options9, $options10, $options11, $options12, $image_prefix_name;
$image_prefix_name ='blogsmu';
if ( !isset($_GET['page']) ) {
    $_GET['page'] = '';
}
if ( $_GET['page'] == "custom-homepage.php" ) {
if ( 'save4' == $_REQUEST['action'] ) {
foreach ($options4 as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
foreach ($options4 as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } }
header("Location: themes.php?page=custom-homepage.php&saved4=true");
die;
} else if( 'reset4' == $_REQUEST['action'] ) {
foreach ($options4 as $value) {
delete_option( $value['id'] ); }
header("Location: themes.php?page=custom-homepage.php&reset4=true");
die;
}
}
if ( !isset($_GET['page']) ) {
    $_GET['page'] = '';
}
if ( $_GET['page'] == "custom-homepage.php" ) {
if ( 'save5' == $_REQUEST['action'] ) {
foreach ($options5 as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
foreach ($options5 as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } }
header("Location: themes.php?page=custom-homepage.php&saved5=true");
die;
} else if( 'reset5' == $_REQUEST['action'] ) {
foreach ($options5 as $value) {
delete_option( $value['id'] ); }
header("Location: themes.php?page=custom-homepage.php&reset5=true");
die;
}
}
if ( !isset($_GET['page']) ) {
    $_GET['page'] = '';
}
if ( $_GET['page'] == "custom-homepage.php" ) {
if ( 'save6' == $_REQUEST['action'] ) {
foreach ($options6 as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
foreach ($options6 as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } }
header("Location: themes.php?page=custom-homepage.php&saved6=true");
die;
} else if( 'reset6' == $_REQUEST['action'] ) {
foreach ($options6 as $value) {
delete_option( $value['id'] ); }
header("Location: themes.php?page=custom-homepage.php&reset6=true");
die;
}
}


if ( $_GET['page'] == "custom-homepage.php" ) {
if ( 'save7' == $_REQUEST['action'] ) {
foreach ($options7 as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
foreach ($options7 as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } }
header("Location: themes.php?page=custom-homepage.php&saved7=true");
die;
} else if( 'reset7' == $_REQUEST['action'] ) {
foreach ($options7 as $value) {
delete_option( $value['id'] ); }
header("Location: themes.php?page=custom-homepage.php&reset7=true");
die;
}
}


if ( $_GET['page'] == "custom-homepage.php" ) {
if ( 'save8' == $_REQUEST['action'] ) {
foreach ($options8 as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
foreach ($options8 as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } }
header("Location: themes.php?page=custom-homepage.php&saved8=true");
die;
} else if( 'reset8' == $_REQUEST['action'] ) {
foreach ($options8 as $value) {
delete_option( $value['id'] ); }
header("Location: themes.php?page=custom-homepage.php&reset8=true");
die;
}
}


if ( $_GET['page'] == "custom-homepage.php" ) {
if ( 'save9' == $_REQUEST['action'] ) {
foreach ($options9 as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
foreach ($options9 as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } }
header("Location: themes.php?page=custom-homepage.php&saved9=true");
die;
} else if( 'reset9' == $_REQUEST['action'] ) {
foreach ($options9 as $value) {
delete_option( $value['id'] ); }
header("Location: themes.php?page=custom-homepage.php&reset9=true");
die;
}
}

if ( $_GET['page'] == "custom-homepage.php" ) {
if ( 'save10' == $_REQUEST['action'] ) {
foreach ($options10 as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
foreach ($options10 as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } }
header("Location: themes.php?page=custom-homepage.php&saved10=true");
die;
} else if( 'reset10' == $_REQUEST['action'] ) {
foreach ($options10 as $value) {
delete_option( $value['id'] ); }
header("Location: themes.php?page=custom-homepage.php&reset10=true");
die;
}
}

if ( $_GET['page'] == "custom-homepage.php" ) {
if ( 'save11' == $_REQUEST['action'] ) {
foreach ($options11 as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
foreach ($options11 as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } }
header("Location: themes.php?page=custom-homepage.php&saved11=true");
die;
} else if( 'reset11' == $_REQUEST['action'] ) {
foreach ($options11 as $value) {
delete_option( $value['id'] ); }
header("Location: themes.php?page=custom-homepage.php&reset11=true");
die;
}
}

if ( $_GET['page'] == "custom-homepage.php" ) {
if ( 'save12' == $_REQUEST['action'] ) {
foreach ($options12 as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }
foreach ($options12 as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } }
header("Location: themes.php?page=custom-homepage.php&saved12=true");
die;
} else if( 'reset12' == $_REQUEST['action'] ) {
foreach ($options12 as $value) {
delete_option( $value['id'] ); }
header("Location: themes.php?page=custom-homepage.php&reset12=true");
die;
}
}


if ( $_GET['page'] == "custom-homepage.php" ) {
if( 'resetall' == $_REQUEST['action'] ) {
foreach ($options4 as $value){ delete_option( $value['id'] ); }
foreach ($options5 as $value){ delete_option( $value['id'] ); }
foreach ($options6 as $value){ delete_option( $value['id'] ); }
foreach ($options7 as $value){ delete_option( $value['id'] ); }
foreach ($options8 as $value){ delete_option( $value['id'] ); }
foreach ($options9 as $value){ delete_option( $value['id'] ); }
foreach ($options10 as $value){ delete_option( $value['id'] ); }
foreach ($options11 as $value){ delete_option( $value['id'] ); }
foreach ($options12 as $value){ delete_option( $value['id'] ); }

////////////////////////////////////////////
$uploads = wp_upload_dir();
$upload_files_path = get_option('upload_path');
//echo wp_upload_dir() . '<br /><br />';
//echo get_option('upload_path'). '<br /><br />';
//echo $uploads['path'] . '<br /><br />';
//echo $uploads['url'] . '<br /><br />';
//echo $uploads['subdir'] . '<br /><br />';
//echo $uploads['basedir'] . '<br /><br />';
//echo $uploads['baseurl'] . '<br /><br />';
//echo $uploads['error'] . ' - ERROR<br /><br />';
//echo WP_CONTENT_DIR . '<br /><br />';
//echo WP_CONTENT_URL . '<br /><br />';
$upload_url_trim = str_replace( WP_CONTENT_DIR, "", $uploads['basedir'] );
//echo $upload_url_trim . '<br /><br />';
if (substr($upload_url_trim, -1) == '/') {
$upload_url_trim = rtrim($upload_url_trim, '/');
}
/////////////////////////////////////////////////////You can alter these options///////////////////////////
$tpl_url = get_site_url();
$ptp = get_template();
$uploads_folder = "thumb";
$upload_path = $uploads['basedir'] . '/' . $uploads_folder . "/";
$upload_path_check = $uploads['basedir'] . '/' . $uploads_folder;

$ttpl = get_template_directory_uri();
$ttpl_url = get_site_url();

$upload_url = WP_CONTENT_URL . $upload_url_trim  . '/' . $uploads_folder;
//echo $upload_url;



if(file_exists($upload_path . $image_prefix_name . "1.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "1.jpg");
unlink("$upload_path_check/" . $image_prefix_name . "1_thumb.jpg");
}

if(file_exists($upload_path . $image_prefix_name . "2.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "2.jpg");
unlink("$upload_path_check/" . $image_prefix_name . "2_thumb.jpg");
}

if(file_exists($upload_path . $image_prefix_name . "3.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "3.jpg");
unlink("$upload_path_check/" . $image_prefix_name . "3_thumb.jpg");
}

if(file_exists($upload_path . $image_prefix_name . "4.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "4.jpg");
unlink("$upload_path_check/" . $image_prefix_name . "4_thumb.jpg");
}

if(file_exists($upload_path . $image_prefix_name . "5.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "5.jpg");
unlink("$upload_path_check/" . $image_prefix_name . "5_thumb.jpg");
}

if(file_exists($upload_path . $image_prefix_name . "6.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "6.jpg");
unlink("$upload_path_check/" . $image_prefix_name . "6_thumb.jpg");
}

if(file_exists($upload_path . $image_prefix_name . "7.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "7.jpg");
unlink("$upload_path_check/" . $image_prefix_name . "7_thumb.jpg");
}

if(file_exists($upload_path . $image_prefix_name . "8.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "8.jpg");
unlink("$upload_path_check/" . $image_prefix_name . "8_thumb.jpg");
}

if(file_exists($upload_path . $image_prefix_name . "9.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "9.jpg");
unlink("$upload_path_check/" . $image_prefix_name . "9_thumb.jpg");
}


//deleting normal image
if(file_exists($upload_path . $image_prefix_name . "1_normal.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "1_normal.jpg");
}
if(file_exists($upload_path . $image_prefix_name . "2_normal.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "2_normal.jpg");
}
if(file_exists($upload_path . $image_prefix_name . "3_normal.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "3_normal.jpg");
}
if(file_exists($upload_path . $image_prefix_name . "4_normal.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "4_normal.jpg");
}
if(file_exists($upload_path . $image_prefix_name . "5_normal.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "5_normal.jpg");
}
if(file_exists($upload_path . $image_prefix_name . "6_normal.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "6_normal.jpg");
}
if(file_exists($upload_path . $image_prefix_name . "7_normal.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "7_normal.jpg");
}
if(file_exists($upload_path . $image_prefix_name . "8_normal.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "8_normal.jpg");
}
if(file_exists($upload_path . $image_prefix_name . "9_normal.jpg")) {
unlink("$upload_path_check/" . $image_prefix_name . "9_normal.jpg");
}

header("Location: themes.php?page=custom-homepage.php&resetall=true");
die;
}
}

add_theme_page(_g ( $themename . __(' Services Options',TEMPLATE_DOMAIN)),  _g ( __('Services Options',TEMPLATE_DOMAIN)),  'edit_theme_options', 'custom-homepage.php', 'blogsmu_features_page');
}
add_action('admin_menu', 'blogsmu_features_register');
?>