<?php if (!defined('W3TC')) die(); ?>

<?php if ($this->_support_reminder): ?>
<script type="text/javascript">/*<![CDATA[*/
jQuery(function($) {
    w3tc_lightbox_support_us('<?php echo wp_create_nonce('w3tc'); ?>');
});
/*]]>*/</script>
<?php endif; ?>

<div class="wrap" id="w3tc">
    <?php screen_icon('w3tc-logo'); ?>

	<h2>W3 Total Cache <span>by W3 EDGE <sup>&reg;</sup></span></h2>

	<?php foreach ($this->_errors as $error): ?>
    <div class="error">
    	<p><?php echo $error; ?></p>
    </div>
	<?php endforeach; ?>

	<?php foreach ($this->_notes as $note): ?>
	<div class="updated fade">
		<p><?php echo $note; ?></p>
    </div>
    <?php endforeach; ?>

	<p id="w3tc-options-menu">
    	<a href="?page=w3tc_general"<?php if ($this->_page == 'w3tc_general'): ?> class="w3tc-options-menu-selected"<?php endif; ?>>General</a> |
    	<a href="?page=w3tc_pgcache"<?php if ($this->_page == 'w3tc_pgcache'): ?> class="w3tc-options-menu-selected"<?php endif; ?>>Page Cache</a> |
    	<?php if (W3TC_PHP5): ?>
    	<a href="?page=w3tc_minify"<?php if ($this->_page == 'w3tc_minify'): ?> class="w3tc-options-menu-selected"<?php endif; ?>>Minify</a> |
    	<?php else: ?>
    	Minify |
    	<?php endif; ?>
    	<a href="?page=w3tc_dbcache"<?php if ($this->_page == 'w3tc_dbcache'): ?> class="w3tc-options-menu-selected"<?php endif; ?>>Database Cache</a> |
        <a href="?page=w3tc_objectcache"<?php if ($this->_page == 'w3tc_objectcache'): ?> class="w3tc-options-menu-selected"<?php endif; ?>>Object Cache</a> |
        <a href="?page=w3tc_browsercache"<?php if ($this->_page == 'w3tc_browsercache'): ?> class="w3tc-options-menu-selected"<?php endif; ?>>Browser Cache</a> |
        <a href="?page=w3tc_mobile"<?php if ($this->_page == 'w3tc_mobile'): ?> class="w3tc-options-menu-selected"<?php endif; ?>>User Agent Groups</a> |
        <a href="?page=w3tc_referrer"<?php if ($this->_page == 'w3tc_referrer'): ?> class="w3tc-options-menu-selected"<?php endif; ?>>Referrer Groups</a> |
    	<a href="?page=w3tc_cdn"<?php if ($this->_page == 'w3tc_cdn'): ?> class="w3tc-options-menu-selected"<?php endif; ?>><acronym title="Content Delivery Network">CDN</acronym></a> |
    	<a href="?page=w3tc_faq"<?php if ($this->_page == 'w3tc_faq'): ?> class="w3tc-options-menu-selected"<?php endif; ?>><acronym title="Frequently Asked Questions">FAQ</acronym></a> |
    	<a href="?page=w3tc_support"<?php if ($this->_page == 'w3tc_support'): ?> class="w3tc-options-menu-selected"<?php endif; ?> style="color: red;">Support</a> |
    	<a href="?page=w3tc_install"<?php if ($this->_page == 'w3tc_install'): ?> class="w3tc-options-menu-selected"<?php endif; ?>>Install</a> |
    	<a href="?page=w3tc_about"<?php if ($this->_page == 'w3tc_about'): ?> class="w3tc-options-menu-selected"<?php endif; ?>>About</a>
    </p>