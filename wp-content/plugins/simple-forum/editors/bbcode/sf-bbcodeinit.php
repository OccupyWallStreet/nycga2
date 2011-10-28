<?php
/*
Simple:Press
BB Code init
$LastChangedDate: 2010-08-14 04:52:34 -0700 (Sat, 14 Aug 2010) $
$Rev: 4424 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

	echo '<link rel="stylesheet" type="text/css" href="'.esc_url(SFEDSTYLE.'bbcode/'.$sfglobals["editor"]["SFbbCSS"]).'" />'."\n";
	echo '<script type="text/javascript" src="'.SFEDITORURL.'bbcode/bbcodeEditor.js"></script>'. "\n";
?>
	<script type='text/javascript'>
/* <![CDATA[ */
	quicktagsL10n = {
		quickLinks: "<?php echo esc_js(__("Quick Links", "sforum")); ?>",
		closeAllOpenTags: "<?php echo esc_js(__("Close all open tags", "sforum")); ?>",
		closeTags: "<?php echo esc_js(__("close tags", "sforum")); ?>",
		enterURL: "<?php echo esc_js(__("Enter the URL", "sforum")); ?>",
		enterImageURL: "<?php echo esc_js(__("Enter the URL of the image", "sforum")); ?>",
		enterImageDescription: "<?php echo esc_js(__("Enter a description of the image", "sforum")); ?>"
	}
	/* ]]> */
	</script>
<?php
?>