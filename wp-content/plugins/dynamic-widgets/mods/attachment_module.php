<?php
/**
 * Attachment Module
 *
 * @version $Id: attachment_module.php 523481 2012-03-25 19:49:08Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_Attachment extends DWModule {
		public static $option = array( 'attachment'	=> 'Attachments' );
		protected static $question = 'Show widget on attachment pages?';
	}
?>