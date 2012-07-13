<?php
/*
Simple:Press
Global ahah loader support
$LastChangedDate: 2010-03-26 16:38:27 -0700 (Fri, 26 Mar 2010) $
$Rev: 3818 $
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('Access Denied');
}

# ------------------------------------------------------------------
# sfa_admin_ahah_support()
#
# Loads admin constants and includes to support AHAH calls
# ------------------------------------------------------------------
function sfa_admin_ahah_support()
{
	sf_setup_sitewide_constants();
	sf_setup_global_constants();
	sf_setup_admin_constants();
	sf_setup_global_includes();
	sf_setup_admin_includes();
}

# ------------------------------------------------------------------
# sf_forum_ahah_support()
#
# Loads forum constants and includes to support AHAH calls
# ------------------------------------------------------------------
function sf_forum_ahah_support()
{
	sf_setup_sitewide_constants();
	sf_setup_global_constants();
	sf_setup_forum_constants();
	sf_setup_global_includes();
	sf_setup_ahah_includes();
}

?>