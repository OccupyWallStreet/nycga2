<?php
/**
 * List Creator.
 *
 * Loosely based on a version of the doLists and related functions from the Markdown PHP Library
 * @see http://michelf.com/projects/php-markdown/
 *
 * Parses unordered and ordered lists on save (storing the HTML in the database).
 * If you switch themes, the list markup is preserved.
 *
 * Parses task lists on display: if you switch themes, you no
 * longer have a handler for checkbox state toggles, so just
 * show ASCII x's and o's.
 *
 * The checked state for each task list item is initially stored
 * as an x (checked) or o (unchecked) in the post/comment text.
 *
 * When a task list item is updated from the blog (via the HTML form),
 * the checked state is stored in post/comment meta.
 *
 * When the post/comment is edited, the checked states stored in
 * post/comment meta in copied to the post/comment text
 * (updating x's and o's) and deleted.
 *
 * This way, the order of the task list items can be
 * changed while editing the post/comment without breaking
 * the data stored in post/comment meta.
 *
 * @package P2
 */

/**
 * Parses lists for posts.
 *
 * @package P2
 */
class P2_Post_List_Creator extends P2_List_Creator {
	var $form_action_name = 'p2-post-task-list';

	function P2_Post_List_Creator() {
		parent::P2_List_Creator();

		// Parse everything on display
		add_filter( 'the_content', array( $this, 'parse_list' ), 1 );

		// Renormalize task list meta into ASCII x's and o's
		add_filter( 'edit_post_content', array( $this, 'edit_post_content' ), 10, 2 );
		add_action( 'post_updated', array( $this, 'delete_all_item_data' ) );

		// Parse UL/OL on save
		add_filter( 'content_save_pre', array( $this, 'parse_list' ), 11 );
	}

	/**
	 * Returns ID of current/given post
	 *
	 * @param int $post_id (optional) post ID
	 * @return int post ID
	 */
	function get_object_id( $post_id = 0 ) {
		$post = get_post( $post_id );
		return (int) $post->ID;
	}

	/**
	 * Determines if the current user has permission to edit the current/given post
	 *
	 * @param int $post_id (optional) post ID
	 * @return bool
	 */
	function current_user_can( $post_id = 0 ) {
		return current_user_can( 'edit_post', $this->get_object_id( $post_id ) );
	}

	/**
	 * Whether we should look for and parse task lists
	 *
	 * @return bool
	 */
	function parse_task_lists() {
		return 'content_save_pre' != current_filter();
	}

	/**
	 * Gets the (meta) checked state for the given task list item on the current/given post.
	 *
	 * @param int $task_id
	 * @param int $post_id (optional)
	 * @return array ( checked, checked_by_user_id, checked_timestamp )
	 */
	function get_item_data( $task_id, $post_id = 0 ) {
		$meta = get_post_meta( $this->get_object_id( $post_id ), "p2_task_{$task_id}", true );
		if ( !$meta ) {
			return array();
		}

		return explode( ':', $meta );
	}

	/**
	 * Sets the (meta) checked state for the given task list item on the current/given post
	 *
	 * @param int  $task_id
	 * @param bool $done
	 * @param int  $post_id (optional)
	 */
	function put_item_data( $task_id, $done = true, $post_id = 0 ) {
		update_post_meta( $this->get_object_id( $post_id ), "p2_task_{$task_id}", sprintf( '%d:%d:%s', $done, get_current_user_id(), time() ) );
	}

	/**
	 * Deletes the (meta) checked state for the given task list item on the current/given post.
	 * The x/o checked state stored in post_content is not changed
	 *
	 * @param int $task_id
	 * @param int $post_id (optional)
	 */
	function delete_item_data( $task_id, $post_id = 0 ) {
		delete_post_meta( $this->get_object_id( $post_id ), "p2_task_{$task_id}" );
	}

	/**
	 * Gets the post meta keys for each (meta) checked state for all task list items in the current/given post.
	 *
	 * @param int $post_id (optional)
	 * @return array
	 */
	function get_all_item_data( $post_id = 0 ) {
		$meta_keys = get_post_custom_keys( $this->get_object_id( $post_id ) );
		if ( !$meta_keys ) {
			return array();
		}

		$task_id_meta_keys = preg_grep( '/p2_task_\d/', $meta_keys );
		if ( !$task_id_meta_keys ) {
			return array();
		}

		return $task_id_meta_keys;
	}

	/**
	 * Deletes all (meta) checked states for the current/given post.
	 *
	 * @param int $post_id (optional)
	 */
	function delete_all_item_data( $post_id = 0 ) {
		$post_id = $this->get_object_id( $post_id );

		$task_id_meta_keys = $this->get_all_item_data( $post_id );
		foreach ( $task_id_meta_keys as $task_id_meta_key ) {
			delete_post_meta( $post_id, $task_id_meta_key );
		}
	}

	/**
	 * Wrapper for renormalizing task list meta into ASCII x's and o's during post edit.
	 * Copies the post meta checked state to x's and o's in post_content
	 */
	function edit_post_content( $text, $post_id ) {
		$task_id_meta_keys = $this->get_all_item_data( $post_id );
		if ( !$task_id_meta_keys ) {
			return $text;
		}

		$old_post = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		$GLOBALS['post'] = get_post( $post_id );
		$text = $this->unparse_list( $text );
		$GLOBALS['post'] = $old_post;
		return $text;
	}
}

/**
 * Parses lists for comments.
 *
 * @package P2
 */
class P2_Comment_List_Creator extends P2_List_Creator {
	var $form_action_name = 'p2-comment-task-list';

	function P2_Comment_List_Creator() {
		parent::P2_List_Creator();

		// Parse everything on display
		add_filter( 'comment_text', array( $this, 'comment_text' ), 11, 2 );

		// Renormalize task list meta into ASCII x's and o's
		add_filter( 'p2_get_comment_content', array( $this, 'unparse_comment_list' ), 10, 2 );
		add_action( 'edit_comment', array( $this, 'delete_all_item_data' ) );

		// Parse UL/OL on save
		add_filter( 'pre_comment_content', array( $this, 'parse_list' ) );
	}

	/**
	 * Parses all lists on display
	 *
	 * Fires on 'comment_text'
	 *
	 * @param string $comment_text
	 * @param object $comment Comment row object
	 * @return string
	 */
	function comment_text( $comment_text, $comment = null ) {
		$old_comment = isset( $GLOBALS['comment'] ) ? $GLOBALS['comment'] : null;
		$GLOBALS['comment'] = $comment;
		$comment_text = $this->parse_list( $comment_text );
		$GLOBALS['comment'] = $old_comment;
		return $comment_text;
	}

	/**
	 * Returns ID of current/given comment
	 *
	 * @param int $comment_id (optional) comment ID
	 * @return int comment ID
	 */
	function get_object_id( $comment_id = 0 ) {
		$comment = get_comment( $comment_id );
		return $comment->comment_ID;
	}

	/**
	 * Determines if the current user has permission to edit the current/given comment
	 *
	 * @param int $comment_id (optional) comment ID
	 * @return bool
	 */
	function current_user_can( $comment_id = 0 ) {
		return current_user_can( 'edit_comment', $this->get_object_id( $comment_id ) );
	}

	/**
	 * Whether we should look for and parse task lists
	 *
	 * @return bool
	 */
	function parse_task_lists() {
		return 'pre_comment_content' != current_filter();
	}

	/**
	 * Gets the (meta) checked state for the given task list item on the current/given comment.
	 *
	 * @param int $task_id
	 * @param int $comment_id (optional)
	 * @return array ( checked, checked_by_user_id, checked_timestamp )
	 */
	function get_item_data( $task_id, $comment_id = 0 ) {
		$meta = get_comment_meta( $this->get_object_id( $comment_id ), "p2_task_{$task_id}", true );
		if ( !$meta ) {
			return array();
		}

		return explode( ':', $meta );
	}

	/**
	 * Sets the (meta) checked state for the given task list item on the current/given comment
	 *
	 * @param int  $task_id
	 * @param bool $done
	 * @param int  $comment_id (optional)
	 */
	function put_item_data( $task_id, $done = true, $comment_id = 0 ) {
		update_comment_meta( $this->get_object_id( $comment_id ), "p2_task_{$task_id}", sprintf( '%d:%d:%s', $done, get_current_user_id(), time() ) );
	}

	/**
	 * Deletes the (meta) checked state for the given task list item on the current/given comment.
	 * The x/o checked state stored in comment_content is not changed
	 *
	 * @param int $task_id
	 * @param int $comment_id (optional)
	 */
	function delete_item_data( $task_id, $comment_id = 0 ) {
		delete_comment_meta( $this->get_object_id( $comment_id ), "p2_task_{$task_id}" );
	}

	/**
	 * Gets the comment meta keys for each (meta) checked state for all task list items in the current/given comment.
	 *
	 * @param int $comment_id (optional)
	 * @return array
	 */
	function get_all_item_data( $comment_id = 0 ) {
		$comment_id = $this->get_object_id( $comment_id );
		$meta = get_metadata( 'comment', $comment_id );
		if ( !$meta ) {
			return array();
		}

		$meta_keys = array_keys( $meta );
		if ( !$meta_keys ) {
			return array();
		}

		$task_id_meta_keys = preg_grep( '/p2_task_\d/', $meta_keys );
		if ( !$task_id_meta_keys ) {
			return array();
		}

		return $task_id_meta_keys;
	}

	/**
	 * Deletes all (meta) checked states for the current/given comment.
	 *
	 * @param int $comment_id (optional)
	 */
	function delete_all_item_data( $comment_id = 0 ) {
		$comment_id = $this->get_object_id( $comment_id );

		$task_id_meta_keys = $this->get_all_item_data( $comment_id );
		foreach ( $task_id_meta_keys as $task_id_meta_key ) {
			delete_comment_meta( $comment_id, $task_id_meta_key );
		}
	}

	/**
	 * Wrapper for renormalizing task list meta into ASCII x's and o's during comment edit.
	 * Copies the comment meta checked state to x's and o's in comment_content
	 */
	function unparse_comment_list( $text, $comment_id ) {
		if ( !$this->get_all_item_data( $comment_id ) ) {
			return $text;
		}

		$old_comment = isset( $GLOBALS['comment'] ) ? $GLOBALS['comment'] : null;
		$GLOBALS['comment'] = get_comment( $comment_id );
		$text = $this->unparse_list( $text );
		$GLOBALS['comment'] = $old_comment;
		return $text;
	}
}

/**
 * Central class for parsing lists.
 *
 * @package P2
 */
class P2_List_Creator {
	/**
	 * @var string name for action parameter of HTML form (not the action attribute, which is always the admin-ajax.php URL)
	 */
	var $form_action_name = '';
	/**
	 * @var bool Are we currently in a nested list?
	 */
	var $doing_recursion = false;

	var $preserved_texts = array();

	function P2_List_Creator() {
		// Have we done the CSS/JS already?
		static $did_header = false;

		if ( $this->form_action_name ) {
			// Add form submission handler
			add_action( "wp_ajax_{$this->form_action_name}", array( $this, 'submit' ) );
		}

		if ( $did_header ) {
			return;
		}

		$did_header = true;

		add_action( 'wp_head', array( $this, 'css' ) );
		add_action( 'wp_head', array( $this, 'js' ) );
		if ( !is_admin() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_js' ) );
		}
	}

	function enqueue_js() {
		wp_enqueue_script( 'jquery-color' );
	}

	function css() {
?>
<style type="text/css">
.is-js .hide-if-js {
	display: none;
}
.p2-task-list ul {
	margin-left: 0 !important;
}
.p2-task-list ul ul {
	margin-left: 20px !important;
}
.p2-task-list li {
	list-style: none;
}
</style>
<?php
	}

	function js() {
?>
<script type="text/javascript">
jQuery( function( $ ) {
	$( 'body' )
		.addClass( 'is-js' )
		.delegate( '.p2-task-list :checkbox', 'click', function() {
			var $this = $( this ),
			    $li = $this.parents( 'li:first' ),
			    $form = $this.parents( 'form:first' ),
			    data = $li.find( ':input' ).serialize(),
			    colorEl = $li, origColor = $li.css( 'background-color' ), color;

			while ( colorEl.get(0).tagName && colorEl.css( 'background-color' ).match( /^\s*(rgba\s*\(\s*0+\s*,\s*0+\s*,\s*0+\s*,\s*0+\s*\)|transparent)\s*$/ ) ) {
				colorEl = colorEl.parent();
			}

			color = colorEl.get(0).tagName ? colorEl.css( 'background-color' ) : '#ffffff';

			data += '&ajax=1&' + $form.find( '.submit :input' ).serialize();

			$.post( $form.attr( 'action' ), data, function( response ) {
				if ( '1' === response )
					$li.css( 'background-color', '#F6F3D1' ).animate( { backgroundColor: color }, 'slow', function() { $li.css( 'background-color', origColor ); } );
			} );
	} );
} );
</script>
<?php
	}

	function regex_to_parse() {
		if ( $this->parse_task_lists() ) {
			// Parse UL/OL/Task lists
			return '!              # $0 = whole list
				^
				([ ]{0,3})     # $1 = nested list space padding
				(              # $2 = list item marker
					([xo]) # $3 = task list item marker
				|
					[#*-]  # UL/OL item marker
				)
				\s+            # Mandatory whitespace after the list item marker
				.*             # List item
				$              # EOL
				(?:            # Multiple list items of the same type
					\n     # New line
					^      # BOL
					(?:
						\1             # Same amount of padding
						(?(3)(?3)|\2)  # Same list item marker
					|                      # OR ...
						\1[ ]{1,}      # Increased padding (start of nested list)
						(?2)           # Any list item marker
					)
					\s+    # Mandatory whitespace after the list item marker
					.*     # List item
					$      # EOL
				)*
			!mx';
		}

		return '!               # $0 = whole list
			^
			([ ]{0,3})      # $1 = number of spaces
			([#*-])         # $2 = UL/OL item marker
			\s+             # Mandatory whitespace after the list item marker
			.*              # List item
			$               # EOL
			(?:
				\n      # New line
				^       # BOL
				\1[ ]*  # Same or increased amount of padding
				[xo#*-] # Any list item marker
				\s+     # Mandatory whitespace after the list item marker
				.*      # List item
				$       # EOL
			)*
		!mx';
	}

	function task_list_regex_to_unparse() {
		return '!               # $0 = whole list
			^
			([ ]{0,3})      # $1 = number of spaces
			([xo])          # $2 = tasklist item marker
			\s+             # Mandatory whitespace after the list item marker
			.*              # List item
			$               # EOL
			(?:
				\n      # New line
				^       # BOL
				\1[ ]*  # Same or increased amount of padding
				[xo#*-] # Any list item marker
				\s+     # Mandatory whitespace after the list item marker
				.*      # List item
				$       # EOL
			)*
		!mx';
	}

	function preserve_text( $text ) {
		global $SyntaxHighlighter;

		if ( false !== strpos( $text, '[' ) && is_a( $SyntaxHighlighter, 'SyntaxHighlighter' ) && $SyntaxHighlighter->shortcodes ) {
			$shortcodes_regex = '#\[(' . join( '|', array_map( 'preg_quote', $SyntaxHighlighter->shortcodes ) ) . ')(?:\s|\]).*\[/\\1\]#s';
			$text = preg_replace_callback( $shortcodes_regex, array( $this, 'preserve_text_callback' ), $text );
		}

		if ( false !== strpos( $text, '<pre' ) ) {
			$text = preg_replace_callback( '#<pre(?:\s|>).*</pre>#s', array( $this, 'preserve_text_callback' ), $text );
		}

		return $text;
	}

	function preserve_text_callback( $matches ) {
		$hash = md5( $matches[0] );
		$this->preserved_text[$hash] = $matches[0];
		return "[preserved_text $hash /]";
	}

	function restore_text( $text ) {
		if ( false === strpos( $text, '[preserved_text ' ) ) {
			return $text;
		}

		return preg_replace_callback( '#\[preserved_text (\S+) /\]#', array( $this, 'restore_text_callback' ), $text );
	}

	function restore_text_callback( $matches ) {
		if ( isset( $this->preserved_text[$matches[1]] ) ) {
			return $this->preserved_text[$matches[1]];
		}

		return $matches[0];
	}

	/**
	 * Converts * and - into ULs, # into OLs, and x and o into task lists.
	 *
	 * @param string $text Plaintext to parse for lists
	 * @param bool $doing_recursion Are we in a nested list?
	 * @return string HTML
	 */
	function parse_list( $text, $doing_recursion = false ) {
		$text = $this->preserve_text( $text );

		$text = preg_replace( '/(\r\n|\r|\n)/', "\n", $text );

		// Run our regex through the callback, get the eventual text a few levels down and return it back to P2 here.

		$old_doing_recursion = $this->doing_recursion;
		$this->doing_recursion = $doing_recursion;
		$r = preg_replace_callback( $this->regex_to_parse(), array( $this, '_do_list_callback' ), $text );
		$this->doing_recursion = $old_doing_recursion;

		return $this->restore_text( $r );
	}

	/**
	 * Adds UL/OL markup, adds FORM markup for task lists.  Calls internal functions for adding LI markup.
	 *
	 * @param array $matches Regex matches from ::parse_list()
	 * @return string HTML
	 */
	function _do_list_callback( $matches ) {
		static $id = 0;

		$doing_recursion = $this->doing_recursion;

		$indent = strlen( $matches[1] );
		switch ( $matches[2] ) {
		case '*' : // UL
		case '-' : // UL
		case '#' : // OL
			if ( '#' == $matches[2] ) {
				$tag = 'ol';
			} else {
				$tag = 'ul';
			}

			// Easy peasy, lemon squeezy.
			return "<$tag>\n" . $this->process_list_items( $matches[0], $indent, $matches[2] ) . "\n</$tag>\n\n";
			break;
		case 'x' : // Task List
		case 'o' : // Task List
			$return = "<ul>\n" . $this->process_task_list_items( $matches[0], $indent ) . "\n</ul>\n\n";

			if ( !$this->current_user_can( $this->get_object_id() ) ) {
				// User is not allowed to edit the post/comment.  No form required.
				return $return;
			}

			// Don't nest form elements
			if ( $doing_recursion ) {
				return $return;
			}

			$id++;

			// Add form
			$ajax_url = remove_query_arg( 'p2ajax', P2_JS::ajax_url() );
			$return  = sprintf( '<form class="p2-task-list" id="p2-task-list-%d" action="%s" method="post">', $id, esc_url( $ajax_url ) ) . $return;
			$return .= "<p class='hide-if-js submit'>\n";
			$return .= "<input type='hidden' name='id' value='$id' />\n";
			$return .= sprintf( "<input type='hidden' name='action' value='%s' />\n", $this->form_action_name );
			$return .= "<input type='submit' value='Save' />\n";
			$return .= wp_nonce_field( "p2-task-list_$id", "_p2_task_list_nonce_$id", true, false );
			$return .= "\n</p>\n</form>";

			return $return;
		}
	}

	/**
	 * Adds LI markup.  Recursively calls ::parse_list() to handle nested lists.
	 *
	 * @param string $text   Plaintext list items
	 * @param int    $indent Number of padding spacess (nesting level)
	 * @param string $marker Which list item marker is being processed (#, *, -)
	 * @return string HTML
	 */
	function process_list_items( $text, $indent, $marker ) {
		// Break list into list items with the same nesting level and item marker
		$items = array_map( 'trim', preg_split( '/^[ ]{' . $indent . '}[' . $marker . ']/m', $text, -1, PREG_SPLIT_NO_EMPTY ) );
		$out = array();
		foreach ( $items as $item ) {
			if ( false !== strpos( $item, "\n" ) ) {
				// Has a nested list.  Newlines for parseability in recursion
				$out[] = "<li>\n$item\n</li>";
			} else {
				$out[] = "<li>$item</li>";
			}
		}
		$text = join( "\n", $out );
		return $this->parse_list( $text, true );
	}

	/**
	 * Adds LI markup, adds INPUT markup.  Recursively calls ::parse_list() to handle nested lists.
	 *
	 * @param string $text    Plaintext list items
	 * @param int    $indent  Number of padding spacess (nesting level)
	 * @param string $context 'display' or 'edit'
	 * @return string HTML
	 */
	function process_task_list_items( $text, $indent, $context = 'display' ) {
		global $post, $comment;

		static $item_ids = array();

		$object_id = $this->get_object_id();
		$current_user_can = $this->current_user_can();

		if ( !isset( $item_ids[$object_id] ) ) {
			$item_ids[$object_id] = 0;
		}

		$item_id =& $item_ids[$object_id];

		// Break list into list items with the same nesting level and note item marker (x, o)
		$items = array_map( 'trim', preg_split( '/^[ ]{' . $indent . '}([xo])/m', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE ) );

		// Heinous sprintf
		if ( 'edit' == $context ) {
			$format = '%9$s%7$s %5$s%10$s';
		} else {
			$format = '<li id="p2-task-%1$d-%2$d"><label>%8$s<input type="checkbox" name="p2_task[%1$d][%2$d]"%3$s%4$s value="1" /><input type="hidden" name="p2_task_ids[%1$d][%2$d]" value="%6$s" /> %5$s%10$s%8$s</label></li>';
		}

		$out = array();
		foreach ( $items as $i => $item ) {
			if ( !( $i % 2 ) ) {
				// Item marker: x, o
				$checked_in_post_state = $item;
				$checked_in_post = 'x' == $checked_in_post_state;
				continue;
			}

			$item_id++;

			$checked_in_meta = $this->get_item_data( $item_id );
			if ( $checked_in_meta ) {
				list( $checked, $checker, $check_timestamp ) = $checked_in_meta;
				if ( $checked ) {
					$user = get_user_by( 'id', $checker );
					$task_meta = " (@{$user->user_login})";
					$check_time = ' datetime="' . esc_attr( gmdate( 'Y-m-d\TH:i:s+0000', $check_timestamp ) ) . '"';
				} else {
					$task_meta = '';
					$check_time = '';
				}
			} else {
				$checked = $checked_in_post;
				$task_meta = '';
				$check_time = '';
			}

			$disabled = $current_user_can ? '' : ' disabled="disabled"';
			if ( 'edit' != $context ) {
				if ( $checked ) {
					$item = "<del{$check_time}>" . preg_replace( '/\n|\z/', '</del>$0', $item, 1 );
				}
			}

			// Heinous sprintf
			$out[] = sprintf(
				$format,
				$object_id,
				$item_id,
				checked( $checked, true, false ),
				$disabled,
				$item,
				$checked_in_post_state,
				$checked ? 'X' : 'O', // uppercase to not cause infinite loop in recursion @see ::unparse_list()
				false === strpos( $item, "\n" ) ? '' : "\n",
				str_repeat( ' ', $indent ),
				$task_meta
			);
		}

		$text = join( "\n", $out ) . "\n";

		if ( 'edit' == $context ) {
			return $this->unparse_list( $text );
		}

		return $this->parse_list( $text, true );
	}

	/**
	 * Handles form submission (AJAX or traditional)
	 */
	function submit() {
		$id = (int) $_POST['id'];

		$is_ajax = isset( $_POST['ajax'] ) && $_POST['ajax'];

		if ( $is_ajax ) {
			check_ajax_referer( "p2-task-list_$id", "_p2_task_list_nonce_$id" );
		} else {
			check_admin_referer( "p2-task-list_$id", "_p2_task_list_nonce_$id" );
		}

		foreach ( $_POST['p2_task_ids'] as $object_id => $tasks ) {
			foreach ( $tasks as $task_id => $checked_in_post_state ) {
				$checked_now = isset( $_POST['p2_task'][$object_id][$task_id] ) && $_POST['p2_task'][$object_id][$task_id];
				$checked_in_post = 'x' == $checked_in_post_state;
				$checked_in_meta = $this->get_item_data( $task_id, $object_id );

				if ( $checked_in_meta ) {
					list( $checked ) = $checked_in_meta;
				} else {
					$checked = $checked_in_post;
				}

				if ( $checked_now == $checked ) {
					continue;
				}

				if ( $checked_now ) {
					$this->put_item_data( $task_id, true, $object_id );
				} else {
					if ( $checked_in_post ) {
						$this->put_item_data( $task_id, false, $object_id );
					} else {
						$this->delete_item_data( $task_id, $object_id );
					}
				}
			}
		}

		// @todo send back new list item content with DEL tag, @mention, etc.
		if ( $is_ajax ) {
			die( '1' );
		}

		wp_safe_redirect( wp_get_referer() );
		exit;
	}

	/**
	 * Renormalizes (meta) checked state to ASCII x's and o's
	 *
	 * @param string $text
	 * @return string
	 */
	function unparse_list( $text ) {
		$text = preg_replace( '/(\r\n|\r|\n)/', "\n", $text );
		$text = preg_replace_callback( $this->task_list_regex_to_unparse(), array( $this, '_fix_task_list_callback' ), $text );
		return preg_replace_callback( '/^[ ]*[XO]/m', array( $this, '_strtolower' ), $text );
	}

	function _strtolower( $matches ) {
		return strtolower( $matches[0] );
	}

	function _fix_task_list_callback( $matches ) {
		$indent = strlen( $matches[1] );
		return $this->process_task_list_items( $matches[0], $indent, 'edit' );
	}
}
