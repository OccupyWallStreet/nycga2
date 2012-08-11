<div id="sec">
<?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(3) ) : else : ?>

<h3 class="featured"><?php _e( 'Communi<span>ty</span>' ) ?></h3>
<div class="domtab">
<ul class="domtabs">
<li><a title="<?php _e( 'Members' ) ?>" href="#tab1"><?php _e( 'Members' ) ?></a></li>
<li><a title="<?php _e( 'Forum' ) ?>" href="#tab2"><?php _e( 'Forum' ) ?></a></li>
<li><a title="<?php _e( 'Groups' ) ?>" href="#tab3"><?php _e( 'Groups' ) ?></a></li>
</ul>
<div class="clear"><br /></div>

<div><a name="tab1" id="tab1"></a>
<?php if ( bp_has_members('type=random&max=3') ) : ?>
<?php do_action( 'bp_before_directory_members_list' ) ?>
<?php while ( bp_members() ) : bp_the_member(); ?>

<div class="mentry">
<a href="<?php bp_member_permalink() ?>"><?php bp_member_avatar('type=full&amp;width=60&amp;height=60') ?></a>
<div class="read"><a title="<?php _e( 'Read' ) ?>" href="<?php bp_member_permalink() ?>"><?php _e( 'View Profile' ) ?></a></div>
</div>

<?php do_action( 'bp_directory_members_item' ) ?>
<?php endwhile; ?>
<?php do_action( 'bp_after_directory_members_list' ) ?>
<?php bp_member_hidden_fields() ?>
<?php else: ?>

<div class="read"><?php _e( "Sorry, no members were found.", 'buddypress' ) ?></div>
<?php endif; ?>
</div>

<div><a name="tab2" id="tab2"></a>

<?php if ( bp_has_forum_topics('max=1') ) : ?>

	<table class="sforum">
		<tr>
			<th id="th-title"><?php _e( 'Topic', 'buddypress' ) ?></th>
			<th id="th-poster"><?php _e( 'Latest', 'buddypress' ) ?></th>
			<?php if ( !bp_is_group_forum() ) : ?>
				<th id="th-group"><?php _e( 'Group', 'buddypress' ) ?></th>
			<?php endif; ?>
			<th id="th-postcount"><?php _e( 'Posts', 'buddypress' ) ?></th>
			<th id="th-freshness"><?php _e( 'Freshness', 'buddypress' ) ?></th>
		</tr>
		<?php while ( bp_forum_topics() ) : bp_the_forum_topic(); ?>
		<tr class="<?php bp_the_topic_css_class() ?>">
			<td class="td-title">
				<a class="topic-title" href="<?php bp_the_topic_permalink() ?>" title="<?php bp_the_topic_title() ?> - <?php _e( 'Permalink', 'buddypress' ) ?>">
					<?php bp_the_topic_title() ?>
				</a>
			</td>
			<td class="td-poster">
				<a href="<?php bp_the_topic_permalink() ?>"><?php bp_the_topic_last_poster_avatar( 'type=thumb&width=20&height=20' ) ?></a>
				<div class="poster-name"><?php bp_the_topic_last_poster_name() ?></div>
			</td>
			<?php if ( !bp_is_group_forum() ) : ?>
				<td class="td-group">
					<a href="<?php bp_the_topic_object_permalink() ?>"><?php bp_the_topic_object_avatar( 'type=thumb&width=20&height=20' ) ?></a>
					<div class="object-name"><a href="<?php bp_the_topic_object_permalink() ?>" title="<?php bp_the_topic_object_name() ?>"><?php bp_the_topic_object_name() ?></a></div>
				</td>
			<?php endif; ?>
			<td class="td-postcount">
				<?php bp_the_topic_total_posts() ?>
			</td>
			<td class="td-freshness">
				<?php bp_the_topic_time_since_last_post() ?>
			</td>
		</tr>
		<?php endwhile; ?>
	</table>
<?php else: ?>
	<div id="message" class="info">
		<p><?php _e( 'Sorry, there were no forum topics found.', 'buddypress' ) ?></p>
	</div>
<?php endif;?>

</div>

<div><a name="tab3" id="tab3"></a>

<?php if ( bp_has_groups('type=random&max=1') ) : ?>
<?php while ( bp_groups() ) : bp_the_group(); ?>
<h3><a href="<?php bp_group_permalink() ?>"><?php the_title() ?></a></h3><br />
<div class="walker"><?php bp_group_avatar( 'type=thumb&width=120&height=120' ) ?></div>
<div class="entry">
<?php bp_group_type() ?> / <?php bp_group_member_count() ?>
</div>
<div class="read"><a title="<?php _e( 'Read' ) ?>" href="<?php bp_group_permalink() ?>"><?php _e( 'View Group' ) ?></a></div>
<?php endwhile; ?>
<?php do_action( 'bp_after_groups_loop' ) ?>
<?php else: ?>
<div class="read"><?php _e( 'There were no groups found.', 'buddypress' ) ?></div>
 <?php endif; ?>
</div>

</div>

<?php endif; ?>
</div>