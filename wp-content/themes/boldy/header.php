<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="keywords" content="<?php echo get_option('boldy_keywords'); ?>" />
<meta name="description" content="<?php echo get_option('boldy_description'); ?>" />
<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link href="<?php bloginfo('template_directory'); ?>/css/ddsmoothmenu.css" rel="stylesheet" type="text/css" />
<link href="<?php bloginfo('template_directory'); ?>/css/prettyPhoto.css" rel="stylesheet" type="text/css" />
<link href="<?php bloginfo('template_directory'); ?>/css/nivo-slider.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.form.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/ddsmoothmenu.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.nivo.slider.pack.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.prettyPhoto.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/custom.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/cufon-yui.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/Museo_Slab_500_400.font.js"></script>
<!-- Cufon init -->
	<script type="text/javascript">
		<?php if(get_option('boldy_cufon')!="no"):?>
			Cufon.replace('h1',{hover: true})('h2',{hover: true})('h3')('.reply',{hover:true})('.more-link');
		 <?php endif ?>
	</script>
	<script type="text/javascript">
		 $(document).ready(function(){
			  $('#quickContactForm').ajaxForm(function(data) {
				 if (data==1){
					 $('#success').fadeIn("slow");
					 $('#bademail').fadeOut("slow");
					 $('#badserver').fadeOut("slow");
					 $('#contact').resetForm();
					 }
				 else if (data==2){
						 $('#badserver').fadeIn("slow");
					  }
				 else if (data==3)
					{
					 $('#bademail').fadeIn("slow");
					}
					});
				 });
		</script>
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php wp_get_archives('type=monthly&format=link'); ?>
	<?php //comments_popup_script(); // off by default ?>
	<?php wp_head(); ?>
</head>

<body <?php if(is_home()){
				echo 'id="home"';
			}elseif(is_category(get_option('boldy_portfolio')) || post_is_in_descendant_category( get_option('boldy_portfolio')) && !is_single()){
				echo 'id="portfolio"';
			}?>>
<!-- BEGINN MAINWRAPPER -->
<div id="mainWrapper">
	<!-- BEGIN WRAPPER -->
    <div id="wrapper">
		<!-- BEGIN HEADER -->
        <div id="header">
            <div id="logo"><a href="<?php bloginfo('url'); ?>/"><img src="<?php echo get_option('boldy_logo_img'); ?>" alt="<?php echo get_option('boldy_logo_alt'); ?>" /></a></div>
			<!-- BEGIN MAIN MENU -->
			<?php if ( function_exists( 'wp_nav_menu' ) ){
					wp_nav_menu( array( 'theme_location' => 'main-menu', 'container_id' => 'mainMenu', 'container_class' => 'ddsmoothmenu', 'fallback_cb'=>'primarymenu') );
				}else{
					primarymenu();
			}?>
            <!-- END MAIN MENU -->
			<!-- BEGIN TOP SEARCH -->
			<div id="topSearch">
				<form id="searchform" action="<?php bloginfo('url'); ?>/" method="get">
					<input type="submit" value="" id="searchsubmit"/>
					<input type="text" id="s" name="s" value="type your search" />
				</form>
			</div>
			<!-- END TOP SEARCH -->
			<!-- BEGIN TOP SOCIAL LINKS -->
			<div id="topSocial">
				<ul>
					<?php if(get_option('boldy_linkedin_link')!=""){ ?>
					<li><a href="<?php echo get_option('boldy_linkedin_link'); ?>" class="linkedin" title="Join us on LinkedIn!"><img src="<?php bloginfo('template_directory'); ?>/images/ico_linkedin.png" alt="LinkedIn" /></a></li>
					<?php }?>
					<?php if(get_option('boldy_twitter_user')!=""){ ?>
					<li><a href="http://www.twitter.com/<?php echo get_option('boldy_twitter_user'); ?>" class="twitter" title="Follow Us on Twitter!"><img src="<?php bloginfo('template_directory'); ?>/images/ico_twitter.png" alt="Follow Us on Twitter!" /></a></li>
					<?php }?>
					<?php if(get_option('boldy_facebook_link')!=""){ ?>
					<li><a href="<?php echo get_option('boldy_facebook_link'); ?>" class="twitter" title="Join Us on Facebook!"><img src="<?php bloginfo('template_directory'); ?>/images/ico_facebook.png" alt="Join Us on Facebook!" /></a></li>
					<?php }?>
					<li><a href="<?php bloginfo('rss2_url'); ?>" title="RSS" class="rss"><img src="<?php bloginfo('template_directory'); ?>/images/ico_rss.png" alt="Subcribe to Our RSS Feed" /></a></li>
				</ul>
			</div>	
			<!-- END TOP SOCIAL LINKS -->
        </div>
        <!-- END HEADER -->
		
		<!-- BEGIN CONTENT -->
		<div id="content">