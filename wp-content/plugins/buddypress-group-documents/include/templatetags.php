<?php

class BP_Group_Documents_Template {

	//category filtering
	public $category;
	public $parent_id;

	//Sorting
	public $order;
	private $sql_sort;
	private $sql_order;

	//Paging
	private $total_records;
	private $total_pages;
	private $page = 1;
	private $start_record = 1;
	private $end_record;
	private $items_per_page;

	//Misc
	public $action_link;

	//Top display - "list view"
	public $document_list;

	//bottom display - "detail view"
	public $show_detail = 0;
	public $name = '';
	public $description = '';
	public $group_categories = array();
	public $doc_categories = array();
	public $operation = 'add';
	public $featured;
	public $id = '';
	public $header;

	public function __construct() {
		global $bp;

		//the parent category id is used sometimes used in post_logic, but always in category_logic so we get it first
		$this->parent_id = self::get_parent_category_id();

		$this->do_post_logic();

		$this->do_url_logic();

		$this->do_category_logic();

		$this->do_sorting_logic();

		$this->do_paging_logic();
		
		$this->document_list = BP_Group_Documents::get_list_by_group( $bp->groups->current_group->id, $this->category, $this->sql_sort, $this->sql_order, $this->start_record, $this->items_per_page );
	}

	public static function get_parent_category_id() {
		global $bp;

		//in order to shoehorn the group-less wordpress taxonomy to behave like groups,
		//all groups have a unique parent taxonomy item.  eg: g3
		$parent_info = term_exists( "g" . $bp->groups->current_group->id, 'group-documents-category');
		if( !$parent_info ){
			$parent_info = wp_insert_term( "g" . $bp->groups->current_group->id, 'group-documents-category');
		}

		return $parent_info['term_id'];
	}
	
	/**
	 * do_post_logic()
	 *
	 * checks the POST array to see if user has submitted either a new document
	 * or has updated a current document.  Creates objects, and used database methods to process
	 */
	private function do_post_logic() {
		global $bp;

		do_action('bp_group_documents_template_do_post_action');

		if( isset( $_POST['bp_group_documents_operation'] ) ) {
			if ( get_magic_quotes_gpc() ) {
				$_POST = array_map( 'stripslashes_deep', $_POST );
			}

			switch( $_POST['bp_group_documents_operation'] ) {
				case 'add':
					$document = new BP_Group_Documents();
					$document->user_id = get_current_user_id();
					$document->group_id = $bp->groups->current_group->id;
					$document->name = $_POST['bp_group_documents_name'];
					$document->description = $_POST['bp_group_documents_description'];
					if ( isset( $_POST['bp_group_documents_featured'] ) )
                        $document->featured = apply_filters('bp_group_documents_featured_in',$_POST['bp_group_documents_featured']);
					if( $document->save() ) {
						self::update_categories($document);
						do_action('bp_group_documents_add_success',$document);
						bp_core_add_message( __('Document successfully uploaded','bp-group-documents') );
					}
				break;
				case 'edit':
					$document = new BP_Group_Documents($_POST['bp_group_documents_id']);
					$document->name = $_POST['bp_group_documents_name'];
					$document->description = $_POST['bp_group_documents_description'];
                    if ( isset( $_POST['bp_group_documents_featured'] ) )
					    $document->featured = apply_filters('bp_group_documents_featured_in',$_POST['bp_group_documents_featured']);
					self::update_categories($document);
					if( $document->save() ) {
						do_action('bp_group_documents_edit_success',$document);
						bp_core_add_message( __('Document successfully edited', 'bp-group-documents') );
					}
				break;
			} //end switch
		} //end if operation
	}
	
	private function update_categories($document) {
		global $bp;

		//update categories from checkbox list
        if ( isset( $_POST['bp_group_documents_categories'] ) )
            $category_ids = apply_filters('bp_group_documents_category_ids_in',$_POST['bp_group_documents_categories']);

        if ( isset( $category_ids ) )
		    wp_set_object_terms($document->id,$category_ids,'group-documents-category');

		//check if new category was added, if so, append to current list
		if( isset( $_POST['bp_group_documents_new_category'] ) && $_POST['bp_group_documents_new_category'] ) {

			if( !term_exists( $_POST['bp_group_documents_new_category'], 'group-documents-category',$this->parent_id ) ) {
				$term_info = wp_insert_term( $_POST['bp_group_documents_new_category'],'group-documents-category',array('parent'=>$this->parent_id));
				wp_set_object_terms($document->id, $term_info['term_id'], 'group-documents-category', true);
			}
		}

	}

	private function do_url_logic() {
		global $bp;

		do_action('bp_group_documents_template_do_url_logic');

		//figure out what to display in the bottom "detail" area based on url
		//assume we are adding a new document
		$document = new BP_Group_Documents();
		if( $document->current_user_can('add') ) {
			$this->header =  __( 'Upload a New Document', 'bp-group-documents' );
			$this->show_detail = 1;
		}
		//if we're editing, grab existing data
		if( ($bp->current_action == $bp->group_documents->slug ) && ( isset( $bp->action_variables[0] ) && $bp->action_variables[0] == 'edit') ) {
			if( ctype_digit( $bp->action_variables[1] ) ){
				$document = new BP_Group_Documents( $bp->action_variables[1] );
				$this->name = apply_filters('bp_group_documents_name_out',$document->name);
				$this->description = apply_filters('bp_group_documents_description_out',$document->description);
				$this->featured = apply_filters('bp_group_documents_featured_out', $document->featured);
				$this->doc_categories = wp_get_object_terms($document->id,'group-documents-category');
				$this->operation = 'edit';
				$this->id = $bp->action_variables[1];
				$this->header =  __( 'Edit Document', 'bp-group-documents' );
			}
		//otherwise, we might be deleting
		} else if ( $bp->current_action == $bp->group_documents->slug && isset( $bp->action_variables[0] ) && $bp->action_variables[0] == 'delete' ) {
			if( bp_group_documents_delete( $bp->action_variables[1] ) ){
				bp_core_add_message( __('Document successfully deleted','bp-group-documents') );
			}
		}
	}

	public function doc_in_category($cat_id) {
		foreach( $this->doc_categories as $doc_category ) {
			if( $doc_category->term_id == $cat_id ) {
				return true;
			}
		}
		return false;
	}

	private function do_category_logic() {
		global $bp;

		do_action('bp_group_documents_template_do_category_logic');

		//1st priority, category in url
		if( isset( $_GET['category'] ) ) {
			$this->category = $_GET['category'];

		//category wasn't in url, check cookies
		} elseif ( isset( $_COOKIE['bp-group-documents-category'])) {
			$this->category = $_COOKIE['bp-group-documents-category'];

		//show all categories
		} else {
			$this->category = false;
		}

		$this->group_categories = self::get_group_categories($this->parent_id);

	}
	
	public static function get_group_categories($not_empty = true) {
		global $bp;

		$parent_id = self::get_parent_category_id();
		if( $not_empty ) {
			return get_terms( 'group-documents-category', array('parent'=>$parent_id ) );
		} else {
			return get_terms( 'group-documents-category', array('parent'=>$parent_id,'hide_empty'=>false ) );
		} 
	}


	private function do_sorting_logic(){
		global $bp;

		do_action('bp_group_documents_template_do_sorting_logic');

		//1st priority, order is in url.  Store in cookie as well
		if( isset( $_GET['order'] ) ){
			$this->order = $_GET['order'];

		//order wasn't in url, check for cookies
		} elseif( isset( $_COOKIE['bp-group-documents-order'] ) ) {
			$this->order = $_COOKIE['bp-group-documents-order'];

		//no order to be found, use default, and put in cookie
		} else {
			$this->order = 'newest';
		}

		switch( $this->order ) {
			case 'newest':
				$this->sql_sort = 'created_ts';
				$this->sql_order = 'DESC';
			break;
			case 'alpha':
				$this->sql_sort = 'name';
				$this->sql_order = 'ASC';
			break;
			case 'popular':
				$this->sql_sort = 'download_count';
				$this->sql_order = 'DESC';
			break;
			default:// default to newest
				$this->sql_sort = 'created_ts';
				$this->sql_order = 'DESC';
			break;
				
		}
	}

	private function do_paging_logic(){
		global $bp;

		do_action('bp_group_documents_template_do_paging_logic');

		$this->items_per_page = get_option('bp_group_documents_items_per_page');

		$this->total_records = BP_Group_Documents::get_total( $bp->groups->current_group->id, $this->category );

		$this->total_pages = ceil( $this->total_records / $this->items_per_page );

		if( isset($_GET['page']) && ctype_digit($_GET['page'])){
			$this->page = $_GET['page'];
			$this->start_record = (($this->page-1) * $this->items_per_page) +1;
		}
		$last_possible = $this->items_per_page * $this->page;
		$this->end_record = ($this->total_records < $last_possible)?$this->total_records:$last_possible;

		$this->action_link = get_bloginfo('url') . '/' . $bp->current_component . '/' . $bp->current_item . '/' . $bp->current_action . '/';

	}

	public function pagination_count(){

		printf( __('Viewing item %s to %s (of %s items)','bp-group-documents'), $this->start_record, $this->end_record, $this->total_records );

	}

	public function pagination_links() {

		if( $this->page != 1 ) {
			echo "<a class='page-numbers prev' href='{$this->action_link}?page=" . ($this->page - 1) . "'>&laquo;</a>";
		}
		for( $i=1; $i<= $this->total_pages; $i++ ) {
			if( $i == $this->page ) {
				echo "<span class='page-numbers current'>$i</span>";
			}else {
				echo "<a class='page-numbers' href='{$this->action_link}?page=$i'>$i</a>";	
			}
		}
		if( $this->page != $this->total_pages ) {
			echo "<a class='page-numbers next' href='{$this->action_link}?page=" . ($this->page + 1) . "'>&raquo;</a>";
		}
	}

	public function show_pagination() {

		return ($this->total_pages > 1);
	}


}
