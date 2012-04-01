<form action="<?php bloginfo('url'); ?>" method="get" id="search-form">
	<label><input type="text" name="s" id="site_search" value="Search this site…"  onfocus="if (this.value == 'Search this site…') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search this site…';}" /></label>
	<input type="submit" id="search-submit" value="Search" />
</form>