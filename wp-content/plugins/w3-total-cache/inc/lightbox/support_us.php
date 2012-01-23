<?php if (!defined('W3TC')) die(); ?>
<h3>Support Us</h3>

<p>We noticed you've been using W3 Total cache for at least 30 days, would you like to help us improve WordPress?</p>

<form action="admin.php?page=<?php echo $this->_page; ?>&amp;w3tc_save_support_us" method="post">
    <p>
    	<label>
        	Link to us:
        	<select name="support" class="select-support-type">
        		<option value="">select one</option>
                <?php foreach ($supports as $support_id => $support_name): ?>
            	<option value="<?php echo $support_id; ?>"<?php echo selected($this->_config->get_string('common.support'), $support_id); ?>><?php echo htmlspecialchars($support_name); ?></option>
            	<?php endforeach; ?>
        	</select>
        </label>, tell your friends with a <input type="button" class="button button-tweet" value="tweet" />
        (<input type="hidden" name="tweeted" value="0" /><label><input type="checkbox" name="tweeted" value="1"<?php checked($this->_config->get_boolean('common.tweeted', true)); ?> /> I've tweeted</label>)
        and give us a great <input type="button" class="button button-rating" value="rating" />.
    </p>

    <div style="text-align: center;">
        <h3>THANK YOU!</h3>
        <p>
            <?php echo $this->nonce_field('w3tc'); ?>
        	<input type="submit" class="button-primary" value="Save and close"> or
        	<?php echo $this->button_hide_note('Don\'t show this message again', 'support_us'); ?>
        </p>
    </div>
</form>