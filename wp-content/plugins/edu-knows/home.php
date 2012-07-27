<?php get_header(); ?>
<div class="entry-box">
<div id="intro">

<?php
$edufaq_text = get_option('tn_edufaq_top_faq');
$edufaq_text_header = get_option('tn_edufaq_top_faq_header'); ?>

<h2>
<?php
if($edufaq_text_header == '') { ?>
<?php _e('Change this header intro title in theme option.','edu-knows'); ?>
<?php } else { ?>
<?php echo stripslashes( wp_filter_post_kses( $edufaq_text_header ) ); ?>
<?php } ?>
</h2>

<?php
if($edufaq_text == '') { ?>
<?php printf(__( "A FAQ is a <a href=\"#\">Frequently Asked Question</a>. This is our collection of FAQs, to get started click one of the topics below or use our search.",'edu-knows') ); ?>
<?php } else { ?>
<?php echo stripslashes( wp_filter_post_kses( $edufaq_text ) ); ?>
<?php } ?>

</div>


<div id="searchbar">
<h3><?php _e('Search', TEMPLATE_DOMAIN); ?></h3>
<form method="get" action="<?php echo site_url(); ?>" id="mysearch">
<p>
<input name="s" type="text" class="sbar" value="Search Keyword Here" onfocus="if (this.value == 'Search Keyword Here') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search Keyword Here';}" size="10" tabindex="1" />
<input class="sinput" type="submit" value="<?php esc_attr(_e('Search', TEMPLATE_DOMAIN)); ?>" /></p>
</form>
</div>

<h3><?php _e('Tags',TEMPLATE_DOMAIN); ?></h3>
<ul class="nolist">
<li>
<?php if(function_exists("wp_tag_cloud")) { ?>
<?php wp_tag_cloud('smallest=12&largest=24&number=50'); ?>
<?php } ?>
</li>
<li id="fulltag"><a href="<?php echo site_url(); ?>/full-tag/"><?php _e('View All Tags &raquo;', TEMPLATE_DOMAIN); ?></a></li>
</ul>

</div>


<div class="entry-box">

<?php if ( is_active_sidebar( __('left-content', TEMPLATE_DOMAIN ) ) ) : ?>
<div class="ebox">
<?php dynamic_sidebar( __('left-content', TEMPLATE_DOMAIN ) ); ?>
</div>
<?php endif; ?>


<?php if ( is_active_sidebar( __('right-content', TEMPLATE_DOMAIN ) ) ) : ?>
<div class="ebox" id="cbox">
<?php dynamic_sidebar( __('right-content', TEMPLATE_DOMAIN ) ); ?>
</div>
<?php endif; ?>

</div>

<?php get_footer(); ?>