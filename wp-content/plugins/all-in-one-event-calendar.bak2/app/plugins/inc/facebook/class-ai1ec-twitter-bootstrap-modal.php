<?php
/**
 * This class is responsible of handling bootstrap modals
 *
 * @author time.ly
 *
 */
class Ai1ec_Twitter_Bootstrap_Modal {
	/**
	 * @var string
	 */
	private $delete_button_text;
	/**
	 * @var string
	 */
	private $keep_button_text;
	/**
	 * @var string
	 */
	private $body_text;
	/**
	 * @var string
	 */
	private $header_text;
	/**
	 * @var string
	 */
	private $id;
	/**
	 * @param string $id
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}
	/**
	 * @param string $modal_text
	 */
	public function __construct( $modal_text ) {
		$this->body_text = $modal_text;
	}

	/**
	 * @param string $delete_button_text
	 */
	public function set_delete_button_text( $delete_button_text ) {
		$this->delete_button_text = $delete_button_text;
	}

	/**
	 * @param string $keep_button_text
	 */
	public function set_keep_button_text( $keep_button_text ) {
		$this->keep_button_text = $keep_button_text;
	}

	/**
	 * @param string $body_text
	 */
	public function set_body_text( $body_text ) {
		$this->body_text = $body_text;
	}

	/**
	 * @param string $header_text
	 */
	public function set_header_text( $header_text ) {
		$this->header_text = $header_text;
	}

	/**
	 * @return string
	 */
	private function render_id_if_present() {
		return isset( $this->id ) ? "id='{$this->id}'" : '';
	}
	/**
	 * @return string
	 */
	private function render_header_if_present() {
		return isset( $this->header_text ) ? "<h1><small>{$this->header_text}</small></h1>" : '';
	}
	/**
	 * @return string
	 */
	private function render_keep_button_if_present() {
		return isset( $this->keep_button_text ) ? "<a href='#' class='btn keep btn-primary'>{$this->keep_button_text}</a>" : '';
	}
	/**
	 * @return string
	 */
	private function render_remove_button_if_present() {
		return isset( $this->delete_button_text ) ? "<a href='#' class='btn remove btn-danger'>{$this->delete_button_text}</a>" : '';
	}
	/**
	 * @return string
	 */
	public function render_modal_and_return_html() {
		$header              = $this->render_header_if_present();
		$id                  = $this->render_id_if_present();
		$remove_event_button = $this->render_remove_button_if_present();
		$keep_event_button   = $this->render_keep_button_if_present();
		$body                = $this->body_text;
		$html = <<<HTML
<div class="modal hide" $id>
	<div class="modal-header">
		<button class="close" data-dismiss="modal">×</button>
		$header
	</div>
	<div class="modal-body">
		$body
	</div>
	<div class="modal-footer">
		$remove_event_button
		$keep_event_button
	</div>
</div>
HTML;
		return $html;
	}
}
?>
