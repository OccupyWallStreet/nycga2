<?php
/** Following code copied from WordPress core */
/**
 * Create HTML list of nav menu input items.
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker_Nav_Menu
 */
class cj_Walker_Nav_Menu_Checklist extends Walker_Nav_Menu  {
	function __construct( $fields = false, $boxid = 0, $type = 'page', $selected = array() ) {
		if ( $fields ) {
			$this->db_fields = $fields;
		}
		$this->boxid = $boxid;
		$this->selected = $selected;
		$this->type = $type;
	}

	function start_lvl( &$output, $depth ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul class='children'>\n";
	}

	function end_lvl( &$output, $depth ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent</ul>";
	}

	/**
	 * @see Walker::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param object $args
	 */
	function start_el(&$output, $item, $depth, $args) {
		$possible_object_id =  $item->object_id;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$output .= $indent . '<li>';
		$output .= '<label>';
		$output .= '<input type="checkbox" ';
		if ( ! empty( $item->_add_to_top ) ) {
			$output .= ' add-to-top';
		}
		$output .= ' name="cjtoolbox['.$this->boxid.']['.$this->type.'][]" value="'. esc_attr( $item->object_id ) .'" ';
		if(is_array($this->selected)) {
			$output .= in_array($item->object_id, $this->selected) ? 'checked="checked"' : '';
		}
		$output .= '/> ';
		$output .= empty( $item->label ) ? esc_html( $item->title ) : esc_html( $item->label );
		$permalink = '';
		if($this->type == 'category') {
			$permalink = get_category_link($item->object_id);
		} else {
			$permalink = get_permalink($item->object_id);
		}
		$output .= '</label> <a class="l_ext" target="_blank" href="'. $permalink .'"></a>';

	}
}
?> 