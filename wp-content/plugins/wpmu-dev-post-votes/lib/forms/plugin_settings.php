<div class="wrap">
	<h2>Post Voting settings</h2>

<?php if (WP_NETWORK_ADMIN) { ?>
	<form action="settings.php" method="post">
<?php } else { ?>
	<form action="options.php" method="post">
<?php } ?>

	<?php settings_fields('wdpv'); ?>
	<?php do_settings_sections('wdpv_options_page'); ?>
	<p class="submit">
		<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
	</p>
	</form>


<style type="text/css">
dl.item {
	margin-bottom: 2em;
}
dt.tag {
	font-weight: bold;
}
dt.attributes, dt.examples {
	font-style: italic;
}
dd.notes {
	font-style: italic;
	color: #666;
}
</style>

<h3>Shortcodes</h3>

<p>Regardless of your <em>Voting box position settings</em>, you can always use the shortcodes to insert
post voting in your content (as long as you have post voting allowed, obviously).</p>

<p>There are several shortcodes that offer a fine-grained control over what is displayed.</p>

<dl class="item">
	<dt class="tag">Tag: <code>[wdpv_vote]</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt> <dd>none</dd>
		</dl>
	</dd>
	<dd>This is the main voting shortcode. It will display all parts of voting gadget - "Vote up" link, "Vote down" link and results.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>[wdpv_vote]</code> - will display all parts of voting gadget - "Vote up" link, "Vote down" link and results.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> if you don't allow voting, only the results will be displayed.</dd>
</dl>

<p>If you wish to customize the gadget appearance, you may want to use one or more of the other shortcodes listed below.</p>

<dl class="item">
	<dt class="tag">Tag: <code>[wdpv_vote_up]</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt> <dd>none</dd>
		</dl>
	</dd>
	<dd>This will display just the "Vote up" link.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>[wdpv_vote_up]</code> - will display just the "Vote up" link.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> if you don't allow voting, nothing will be displayed.</dd>
</dl>
<dl class="item">
	<dt class="tag">Tag: <code>[wdpv_vote_down]</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt> <dd>none</dd>
		</dl>
	</dd>
	<dd>This will display just the "Vote down" link.</dd>
	<dd>
		<dl>
			<dt class="examples">Example:</dt>
			<dd><code>[wdpv_vote_down]</code> - will display just the "Vote down" link.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> if you don't allow voting, nothing will be displayed.</dd>
</dl>
<dl class="item">
	<dt class="tag">Tag: <code>[wdpv_vote_result]</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt> <dd>none</dd>
		</dl>
	</dd>
	<dd>This will display just the voting results.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>[wdpv_vote_result]</code> - will display just the voting results.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> results will be displayed even if you don't allow voting.</dd>
</dl>
<dl class="item">
	<dt class="tag">Tag: <code>[wdpv_popular]</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt>
			<dd>
				<dl>
					<dt><code>limit</code></dt>
					<dd><em>(optional)</em> Show only this many posts. Defaults to 5</dd>
					<dt><code>network</code></dt>
					<dd><em>(optional)</em> Show posts from entire network. Set to <code>yes</code> if you wish to display posts from entire network.</dd>
				</dl>
			</dd>
		</dl>
	</dd>
	<dd>This will display the list of posts with highest number of votes.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>[wdpv_popular]</code> - will display 5 highest rated posts on the current blog.</dd>
			<dd><code>[wdpv_popular limit="3"]</code> - will display 3 highest rated posts on the current blog.</dd>
			<dd><code>[wdpv_popular network="yes"]</code> - will display 5 highest rated posts on entire network.</dd>
			<dd><code>[wdpv_popular limit="10" network="yes"]</code> - will display 10 highest rated posts on entire network.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> popular posts will be displayed even if you don't allow voting.</dd>
</dl>


<h3>Template tags</h3>

<p>Template tags can be used in your themes within The Loop, regardless of your <em>Voting box position settings</em>.</p>

<dl class="item">
	<dt class="tag">Tag: <code>wdpv_vote()</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt>
			<dd>Clear the floats, <code>true</code> or <code>false</code>. Defaults to <code>true</code></dd>
		</dl>
	</dd>
	<dd>This is the main voting template tag. It will display all parts of voting gadget - "Vote up" link, "Vote down" link and results.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>&lt;?php wdpv_vote(); ?&gt;</code> - will display all parts of voting gadget - "Vote up" link, "Vote down" link and results.</dd>
			<dd><code>&lt;?php wdpv_vote(false); ?&gt;</code> - same as above, without clearing the floats.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> if you don't allow voting, only the results will be displayed.</dd>
</dl>
<dl class="item">
	<dt class="tag">Tag: <code>wdpv_vote_up()</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt>
			<dd>Clear the floats, <code>true</code> or <code>false</code>. Defaults to <code>true</code></dd>
		</dl>
	</dd>
	<dd>This will display just the "Vote up" link.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>&lt;?php wdpv_vote_up(); ?&gt;</code> - will display just the "Vote up" link.</dd>
			<dd><code>&lt;?php wdpv_vote_up(false); ?&gt;</code> - same as above, without clearing the floats.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> if you don't allow voting, nothing will be displayed.</dd>
</dl>
<dl class="item">
	<dt class="tag">Tag: <code>wdpv_vote_down()</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt>
			<dd>Clear the floats, <code>true</code> or <code>false</code>. Defaults to <code>true</code></dd>
		</dl>
	</dd>
	<dd>This will display just the "Vote down" link.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>&lt;?php wdpv_vote_down(); ?&gt;</code> - will display just the "Vote down" link.</dd>
			<dd><code>&lt;?php wdpv_vote_down(false); ?&gt;</code> - same as above, without clearing the floats.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> if you don't allow voting, nothing will be displayed.</dd>
</dl>
<dl class="item">
	<dt class="tag">Tag: <code>wdpv_vote_result()</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt>
			<dd>Clear the floats, <code>true</code> or <code>false</code>. Defaults to <code>true</code></dd>
		</dl>
	</dd>
	<dd>This will display just the voting results.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>&lt;?php wdpv_vote_result(); ?&gt;</code> - will display just the voting results.</dd>
			<dd><code>&lt;?php wdpv_vote_result(false); ?&gt;</code> - same as above, without clearing the floats.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> results will be displayed even if you don't allow voting.</dd>
</dl>
<dl class="item">
	<dt class="tag">Tag: <code>wdpv_popular()</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt>
			<dd>int <code>limit</code> - <em>(optional)</em> Show only this many posts. Defaults to 5</dd>
			<dd>bool <code>network</code> - <em>(optional)</em> Show posts from entire network. Set to <code>true</code> if you wish to display posts from entire network.</dd>
		</dl>
	</dd>
	<dd>This will display the list of posts with highest number of votes.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>&lt;?php wdpv_popular(); ?&gt;</code> - will display 5 highest rated posts on the current blog.</dd>
			<dd><code>&lt;?php wdpv_popular(3); ?&gt;</code> - will display 3 highest rated posts on the current blog.</dd>
			<dd><code>&lt;?php wdpv_popular(5, true); ?&gt;</code> - will display 5 highest rated posts on entire network.</dd>
			<dd><code>&lt;?php wdpv_popular(10, true); ?&gt;</code> - will display 10 highest rated posts on entire network.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> popular posts will be displayed even if you don't allow voting.</dd>
</dl>


<h4>Custom post variations</h4>

<dl class="item">
	<dt class="tag">Tag: <code>wdpv_get_vote($standalone, $post_id)</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt>
			<dd>bool <code>standalone</code> - Clear the floats, <code>true</code> or <code>false</code>. Defaults to <code>true</code></dd>
			<dd>int <code>post_id</code> - Your post ID</dd>
		</dl>
	</dd>
	<dd>This is the main voting template tag. It will <em>return</em> all parts of voting gadget - "Vote up" link, "Vote down" link and results.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>&lt;?php wdpv_get_vote(); ?&gt;</code> - will <em>return</em> all parts of voting gadget - "Vote up" link, "Vote down" link and results.</dd>
			<dd><code>&lt;?php wdpv_get_vote(false); ?&gt;</code> - same as above, without clearing the floats.</dd>
			<dd><code>&lt;?php wdpv_get_vote(true, 12); ?&gt;</code> - will return all parts of voting gadget for your post with ID of 12.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> if you don't allow voting, only the results will be returned.</dd>
</dl>
<dl class="item">
	<dt class="tag">Tag: <code>wdpv_get_vote_up($standalone, $post_id)</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt>
			<dd>bool <code>standalone</code> - Clear the floats, <code>true</code> or <code>false</code>. Defaults to <code>true</code></dd>
			<dd>int <code>post_id</code> - Your post ID</dd>
		</dl>
	</dd>
	<dd>This will <em>return</em> just the "Vote up" link.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>&lt;?php wdpv_get_vote_up(); ?&gt;</code> - will <em>return</em> just the "Vote up" link.</dd>
			<dd><code>&lt;?php wdpv_get_vote_up(false); ?&gt;</code> - same as above, without clearing the floats.</dd>
			<dd><code>&lt;?php wdpv_get_vote_up(true, 12); ?&gt;</code> - will return "Vote up" link for your post with ID of 12.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> if you don't allow voting, nothing will be returned.</dd>
</dl>
<dl class="item">
	<dt class="tag">Tag: <code>wdpv_get_vote_down($standalone, $post_id)</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt>
			<dd>bool <code>standalone</code> - Clear the floats, <code>true</code> or <code>false</code>. Defaults to <code>true</code></dd>
			<dd>int <code>post_id</code> - Your post ID</dd>
		</dl>
	</dd>
	<dd>This will <em>return</em> just the "Vote down" link.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>&lt;?php wdpv_get_vote_down(); ?&gt;</code> - will <em>return</em> just the "Vote down" link.</dd>
			<dd><code>&lt;?php wdpv_get_vote_down(false); ?&gt;</code> - same as above, without clearing the floats.</dd>
			<dd><code>&lt;?php wdpv_get_vote_down(true, 12); ?&gt;</code> - will return "Vote down" link for your post with ID of 12.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> if you don't allow voting, nothing will be returned.</dd>
</dl>
<dl class="item">
	<dt class="tag">Tag: <code>wdpv_get_vote_result($standalone, $post_id)</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt>
			<dd>bool <code>standalone</code> - Clear the floats, <code>true</code> or <code>false</code>. Defaults to <code>true</code></dd>
			<dd>int <code>post_id</code> - Your post ID</dd>
		</dl>
	</dd>
	<dd>This will <em>return</em> just the voting results.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>&lt;?php wdpv_get_vote_result(); ?&gt;</code> - will <em>return</em> just the voting results.</dd>
			<dd><code>&lt;?php wdpv_get_vote_result(false); ?&gt;</code> - same as above, without clearing the floats.</dd>
			<dd><code>&lt;?php wdpv_get_vote_result(true, 12); ?&gt;</code> - will return voting results for your post with ID of 12.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> results will be returned even if you don't allow voting.</dd>
</dl>


<h4>Multisite variations</h4>

<dl class="item">
	<dt class="tag">Tag: <code>wdpv_get_vote_ms($standalone, $blog_id, $post_id)</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt>
			<dd>bool <code>standalone</code> - Clear the floats, <code>true</code> or <code>false</code>. Defaults to <code>true</code></dd>
			<dd>int <code>blog_id</code> - Your blog ID</dd>
			<dd>int <code>post_id</code> - Your post ID</dd>
		</dl>
	</dd>
	<dd>This is the main voting template tag. It will <em>return</em> all parts of voting gadget - "Vote up" link, "Vote down" link and results.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>&lt;?php wdpv_get_vote_ms(); ?&gt;</code> - will <em>return</em> all parts of voting gadget - "Vote up" link, "Vote down" link and results.</dd>
			<dd><code>&lt;?php wdpv_get_vote_ms(false); ?&gt;</code> - same as above, without clearing the floats.</dd>
			<dd><code>&lt;?php wdpv_get_vote_ms(true, 4, 12); ?&gt;</code> - will return all parts of voting gadget for your post with ID of 12 from the blog on your network with ID of 4.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> if you don't allow voting, only the results will be returned.</dd>
</dl>
<dl class="item">
	<dt class="tag">Tag: <code>wdpv_get_vote_up_ms($standalone, $blog_id, $post_id)</code></dt>
	<dd>
		<dl>
			<dt class="attributes">Attributes:</dt>
			<dd>bool <code>standalone</code> - Clear the floats, <code>true</code> or <code>false</code>. Defaults to <code>true</code></dd>
			<dd>int <code>blog_id</code> - Your blog ID</dd>
			<dd>int <code>post_id</code> - Your post ID</dd>
		</dl>
	</dd>
	<dd>This will <em>return</em> just the "Vote up" link.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>&lt;?php wdpv_get_vote_up(); ?&gt;</code> - will <em>return</em> just the "Vote up" link.</dd>
			<dd><code>&lt;?php wdpv_get_vote_up(false); ?&gt;</code> - same as above, without clearing the floats.</dd>
			<dd><code>&lt;?php wdpv_get_vote_up(true, 4, 12); ?&gt;</code> - will return "Vote up" link for your post with ID of 12 from the blog on your network with ID of 4.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> if you don't allow voting, nothing will be returned.</dd>
</dl>
<dl class="item">
	<dt class="tag">Tag: <code>wdpv_get_vote_down_ms($standalone, $blog_id, $post_id)</code></dt>
	<dd>
			<dl>
				<dt class="attributes">Attributes:</dt>
				<dd>bool <code>standalone</code> - Clear the floats, <code>true</code> or <code>false</code>. Defaults to <code>true</code></dd>
				<dd>int <code>blog_id</code> - Your blog ID</dd>
				<dd>int <code>post_id</code> - Your post ID</dd>
			</dl>
		</dd>
	<dd>This will <em>return</em> just the "Vote down" link.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>&lt;?php wdpv_get_vote_down(); ?&gt;</code> - will <em>return</em> just the "Vote down" link.</dd>
			<dd><code>&lt;?php wdpv_get_vote_down(false); ?&gt;</code> - same as above, without clearing the floats.</dd>
			<dd><code>&lt;?php wdpv_get_vote_down(true, 4, 12); ?&gt;</code> - will return "Vote down" link for your post with ID of 12 from the blog on your network with ID of 4.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> if you don't allow voting, nothing will be returned.</dd>
</dl>
<dl class="item">
	<dt class="tag">Tag: <code>wdpv_get_vote_result_ms($standalone, $blog_id, $post_id)</code></dt>
	<dd>
			<dl>
				<dt class="attributes">Attributes:</dt>
				<dd>bool <code>standalone</code> - Clear the floats, <code>true</code> or <code>false</code>. Defaults to <code>true</code></dd>
				<dd>int <code>blog_id</code> - Your blog ID</dd>
				<dd>int <code>post_id</code> - Your post ID</dd>
			</dl>
		</dd>
	<dd>This will <em>return</em> just the voting results.</dd>
	<dd>
		<dl>
			<dt class="examples">Examples:</dt>
			<dd><code>&lt;?php wdpv_get_vote_result(); ?&gt;</code> - will <em>return</em> just the voting results.</dd>
			<dd><code>&lt;?php wdpv_get_vote_result(false); ?&gt;</code> - same as above, without clearing the floats.</dd>
			<dd><code>&lt;?php wdpv_get_vote_result(true, 4, 12); ?&gt;</code> - will return voting results for your post with ID of 12 from the blog on your network with ID of 4.</dd>
		</dl>
	</dd>
	<dd class="notes"><strong>Note:</strong> results will be returned even if you don't allow voting.</dd>
</dl>

</div>