<?php
/*
Simple:Press
Admin Tags
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
{
	die('Access Denied');
}

include_once (SF_PLUGIN_DIR.'/admin/panel-tags/support/sfa-tags-support.php');

global $wpdb;

# Send good header HTTP
status_header(200);
header("Content-Type: text/javascript; charset=" . get_bloginfo('charset'));

$sort_order = sf_esc_str($_GET['order']);

# Build pagination
$current_page = sf_esc_int($_GET['pagination']);

# Get tags
$tags = sfa_database_get_tags($sort_order, '', $current_page);

# output tags
echo '<ul>';
foreach ($tags['tags'] as $tag)
{
	echo '<li><span>'.$tag->tag_name.'</span>&nbsp;('.$tag->tag_count.')</li>';
}
echo '</ul>';

# Build pagination
$ajax_url = SFADMINURL.'panel-tags/support/sfa-tags-tags.php';
$first = '?';

# Order
if (isset($_GET['order']))
{
	$ajax_url = $ajax_url.'?order='.$sort_order;
	$first = '&amp;';
}
?>
<div class="navigation">
	<?php if (($current_page * SFMANAGETAGSNUM)  + SFMANAGETAGSNUM > $tags['count']) : ?>
		<?php _e('Previous tags', 'sforum'); ?>
	<?php else : ?>
		<a href="<?php echo $ajax_url.$first.'pagination='.($current_page + 1); ?>"><?php _e('Previous tags', 'sforum'); ?></a>
	<?php endif; ?>
	|
	<?php if ($current_page == 0) : ?>
		<?php _e('Next tags', 'sforum'); ?>
	<?php else : ?>
	<a href="<?php echo $ajax_url.$first.'pagination='.($current_page - 1) ?>"><?php _e('Next tags', 'sforum'); ?></a>
	<?php endif; ?>
</div>
<?php
exit();

?>