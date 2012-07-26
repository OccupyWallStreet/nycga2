<?php
/**
 * Single Post Module
 *
 * @version $Id: single_module.php 523481 2012-03-25 19:49:08Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Single extends DWModule {
		protected static $info = 'When you use an author <b>AND</b> a category exception, both rules in the condition must be met. Otherwise the exception rule won\'t be applied. If you want to use the rules in a logical OR condition. Add the same widget again and apply the other rule to that.';
		public static $option = array( 'single' => 'Single Posts' );
		protected static $question = 'Show widget default on single posts?';
		protected static $type = 'custom';
		protected static $wpml = TRUE;

		public static function admin() {
			$DW = &$GLOBALS['DW'];

			parent::admin();

			self::$opt = $DW->getDWOpt($_GET['id'], 'single');
			$authors = DW_Author::getAuthors();

			if ( count($authors) > DW_LIST_LIMIT ) {
				$author_condition_select_style = DW_LIST_STYLE;
			}

			$js_count = 0;
			$opt_single_author = $DW->getDWOpt($_GET['id'], 'single-author');
			$js_author_array = array();
			if ( $opt_single_author->count > 0 ) {
				$js_count = $js_count + $opt_single_author->count - 1;
			}

			// -- Category
			$category = get_categories( array('hide_empty' => FALSE) );
			if ( count($category) > DW_LIST_LIMIT ) {
				$category_condition_select_style = DW_LIST_STYLE;
			}

			// For JS
			$js_category_array = array();
			foreach ( $category as $cat ) {
				$js_category_array[ ] = '\'single_category_act_' . $cat->cat_ID . '\'';
				$js_category_array[ ] = '\'single_category_childs_act_' . $cat->cat_ID . '\'';
			}

			$catmap = DW_Category::getCatChilds(array(), 0, array());

			$opt_single_category = $DW->getDWOpt($_GET['id'], 'single-category');
			if ( $opt_single_category->count > 0 ) {
				$js_count = $js_count + $opt_single_category->count - 1;
			}

			// -- Individual / Posts / Tags
			$opt_individual = $DW->getDWOpt($_GET['id'], 'individual');
			$opt_single_post = $DW->getDWOpt($_GET['id'], 'single-post');
			$opt_single_tag = $DW->getDWOpt($_GET['id'], 'single-tag');
			if ( $opt_individual->count > 0 ) {
				$individual = TRUE;
				$count_individual = '(' . __('Posts: ', DW_L10N_DOMAIN) . $opt_single_post->count . ', ' . __('Tags: ', DW_L10N_DOMAIN) . $opt_single_tag->count . ')';
			}

			self::GUIHeader(self::$option[self::$name], self::$question, self::$info);
			self::GUIOption();

			// Individual
			$DW->dumpOpt($opt_individual);
			echo '<br />';
			echo '<input type="checkbox" id="individual" name="individual" value="1" ' . ( (isset($individual) && $individual)  ? 'checked="checked"' : '' ) . ' onclick="chkInPosts()" />';
			echo '<label for="individual">' . __('Make exception rule available to individual posts and tags.', DW_L10N_DOMAIN) . ' ' . ( ($opt_individual->count > 0)  ? $count_individual : '' ) . '</label>';
			echo '<img src="' . $DW->plugin_url . 'img/info.gif" alt="info" title="' . __('Click to toggle info', DW_L10N_DOMAIN) . '" onclick="divToggle(\'individual_post_tag\')" />';
			echo '<div>';
			echo '<div id="individual_post_tag" class="infotext">';
			_e('When you enable this option, you have the ability to apply the exception rule for <em>Single Posts</em> to tags and individual posts.
								You can set the exception rule for tags in the single Edit Tag Panel (go to <a href="edit-tags.php?taxonomy=post_tag">Post Tags</a>,
								click a tag), For individual posts in the <a href="post-new.php">New</a> or <a href="edit.php">Edit</a> Posts panel.
								Exception rules for tags and individual posts in any combination work independantly, but will always be counted as one exception.<br />
		  					Please note when exception rules are set for Author and/or Category, these will be removed.
		  				', DW_L10N_DOMAIN);
			echo '</div></div>';

			// Individual posts and tags
			foreach ( $opt_single_post->act as $singlepost ) {
				echo '<input type="hidden" name="single_post_act[]" value="' . $singlepost . '" />';
			}

			foreach ( $opt_single_tag->act as $tag ) {
				echo '<input type="hidden" name="single_tag_act[]" value="'. $tag . '" />';
			}

			// JS array authors
			foreach ( array_keys($authors) as $id ) {
				$js_author_array[ ] = '\'single_author_act_' . $id . '\'';
			}
?>

<table border="0" cellspacing="0" cellpadding="0">
<tr>
  <td valign="top">
  	<?php  DW_Author::mkGUI(TRUE); ?>
  </td>
  <td style="width:10px"></td>
  <td valign="top">
  	<?php $opt = $DW->getDWOpt($_GET['id'], 'single-category'); ?>
  	<?php $DW->dumpOpt($opt); ?>
		<?php DW_Category::GUIComplex(TRUE, $opt); ?>
    </div>
  </td>
</tr>
</table>
<?php
			self::GUIFooter();
?>
<script type="text/javascript">
/* <![CDATA[ */
  function chkInPosts() {
    var posts = <?php echo $opt_single_post->count; ?>;
    var tags = <?php echo $opt_single_tag->count; ?>;

    if ( (posts > 0 || tags > 0) && jQuery('#individual').is(':checked') == false ) {
      if ( confirm('Are you sure you want to disable the exception rule for individual posts and tags?\nThis will remove the options set to individual posts and/or tags for this widget.\nOk = Yes; No = Cancel') ) {
        swChb(cAuthors, false);
        swChb(cCat, false);
      } else {
        jQuery('#individual').attr('checked', true);
      }
    } else if ( icount > 0 && jQuery('#individual').is(':checked') ) {
      if ( confirm('Are you sure you want to enable the exception rule for individual posts and tags?\nThis will remove the exceptions set for Author and/or Category on single posts for this widget.\nOk = Yes; No = Cancel') ) {
        swChb(cAuthors, true);
        swChb(cCat, true);
        icount = 0;
      } else {
        jQuery('#individual').attr('checked', false);
      }
    } else if ( jQuery('#individual').is(':checked') ) {
        swChb(cAuthors, true);
        swChb(cCat, true);
    } else {
        swChb(cAuthors, false);
        swChb(cCat, false);
    }
  }

 	function ci(id) {
    if ( jQuery('#'+id).is(':checked') ) {
      icount++;
    } else {
      icount--;
    }
  }

  var icount = <?php echo $js_count; ?>;
  var cAuthors = new Array(<?php echo implode(', ', $js_author_array); ?>);
  var cCat = new Array(<?php echo implode(', ', $js_category_array); ?>);

  if ( jQuery('#individual').is(':checked') ) {
    swChb(cAuthors, true);
    swChb(cCat, true);
  }
/* ]]> */
</script>
<?php
		}
	}
?>