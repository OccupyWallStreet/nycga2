<?php // Action target that displays the popup to insert a content block to a post/page

function add_content_block_popup(){ ?>

	<script>
		function InsertContentBlockForm(){
			var content_id = jQuery("#add_content_block_id").val();
			if(content_id == ""){
				alert("<?php _e("Please select a Content Block", 'custom-post-widget') ?>");
				return;
			}
			var win = window.dialogArguments || opener || parent || top;
			win.send_to_editor("[content_block id=" + content_id + "]");
		}
	</script>

	<div id="content_block_form" style="display:none;">
		<div class="wrap">
			<div>
				<div style="padding:15px 15px 0 15px;">
					<h3 style="color:#5A5A5A!important; font-family:Georgia,Times New Roman,Times,serif!important; font-size:1.8em!important; font-weight:normal!important;">
						<?php _e("Insert Content Block", 'custom-post-widget'); ?>
					</h3>
					<span>
						<?php _e("Select a Content Block below to add it to your post or page.", 'custom-post-widget'); ?>
					</span>
				</div>
				<div style="padding:15px 15px 0 15px;">
					<select id="add_content_block_id">
						<option value="">
							<?php _e("Select a Content Block", 'custom-post-widget'); ?>
						</option>
						<?php query_posts('post_type=content_block&orderby=ID&order=ASC&showposts=-1');
							if ( have_posts() ) : while ( have_posts() ) : the_post();
								$currentID = get_the_ID();
								if($currentID == $custom_post_id)
									$extra = 'selected' and
									$widgetExtraTitle = get_the_title();
								else
									$extra = '';
									echo '<option value="'.$currentID.'" '.$extra.'>'.get_the_title().'</option>';
								endwhile; else:
								echo '<option value="empty">' . __('No content blocks available', 'custom-post-widget') . '</option>';
							endif; ?>
					</select>
					<br />
					<div style="padding:15px;">
						<input type="button" class="button-primary" value="<?php _e("Insert Content Block", 'custom-post-widget') ?>" onclick="InsertContentBlockForm();"/>
						&nbsp;&nbsp;&nbsp; <a class="button" style="color:#bbb;" href="#" onclick="tb_remove(); return false;">
						<?php _e("Cancel", 'custom-post-widget'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>