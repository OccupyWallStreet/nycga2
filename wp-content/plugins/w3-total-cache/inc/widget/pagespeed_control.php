<?php if (!defined('W3TC')) die(); ?>
<p>
	<label>
        Page Speed API Key:
	    <input type="text" name="w3tc_widget_pagespeed_key" value="<?php echo $this->_config->get_string('widget.pagespeed.key'); ?>" size="60" />
    </label>
</p>
<p>To acquire an API key, visit the <a href="https://code.google.com/apis/console" target="_blank">APIs Console</a>. Go to the Project Home tab, activate the Page Speed Online API, and accept the Terms of Service.</p>
<p>Then go to the API Access tab. The API key is in the Simple API Access section.</p>