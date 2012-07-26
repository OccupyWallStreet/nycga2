<?php
// don't load directly 
//if ( !defined('ABSPATH') ) 
//	die('-1');

$nonce=$_REQUEST['_wpnonce'];
if ( !isset( $nonce ) ) {
	include('wp-includes/pluggable.php');
	if(!wp_verify_nonce($nonce, 'clog-nonce') ) wp_die('Are you sure?'); 
}
include('../../../../wp-config.php');
if ( isset( $_GET['p'] ) )
 	$post_id = $post_ID = (int) $_GET['p'];
elseif ( isset( $_POST['post_ID'] ) )
 	$post_id = $post_ID = (int) $_POST['post_ID'];
else
 	$post_id = $post_ID = 0;

$log = get_post_meta( $post_id , 'last_campaign_log', true);

?><h1>Last Log of Campaign <?php echo $post_id.": ".get_the_title($post_id); ?></h1>
<?php
echo $log;

?>
