<?php
/*
Template Name: Redirect
*/
?>

<?php 
/* 

USAGE INSTRUCTIONS:

1. Create a new page in WordPress
2. Add a title to the page (e.g. Theme Junkie)
3. Add an URL to the content of the page (e.g. http://www.theme-junkie.com OR theme-junkie.com OR www.theme-junkie.com)
4. Select "Redirect" as the page template
5. Publish!

*/
?>

<?php if (have_posts()) : the_post(); ?>
<?php $URL = get_the_excerpt(); if (!preg_match('/^http:\/\//', $URL)) $URL = 'http://' . $URL; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Refresh" content="0; url=<?php echo $URL; ?>"> 
</head>

<body>
</body>
</html>

<?php endif; ?>
						
