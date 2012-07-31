<?php
	get_header();
?>

<div style="margin-top:2em; text-align:center;">

<?php
if (!$wp_grant_blog) {
	printf(
		apply_filters(
			'wdfb-registration_message',
			__('<p>Thank you for registering. <a href="%s">Proceed to main site.</a></p>', 'wdfb')
		), home_url()
	);
} else {
	printf(__('<p>Your new blog &quot;%s&quot; is created.</p>', 'wdfb'), $new_blog_title);
	printf(__('<p><a href="%s">Proceed to your blog.</a></p>', 'wdfb'), $new_blog_url);
}
?>

</div>

<?php
	get_footer();
?>