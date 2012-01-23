<form method="get" id="searchform-header" action="<?php bloginfo( 'url' ); ?>" class="clearfix" >
		<input type="text" id="s" name="s" value="Search" onblur="if (this.value == '') {this.value = 'Search';}"  onfocus="if (this.value == 'Search') {this.value = '';}" />
		<input type="image" src="<?php bloginfo( 'template_directory' ); ?>/images/search.gif" id="go" alt="Search" title="Search" />
</form><!-- End searchform -->