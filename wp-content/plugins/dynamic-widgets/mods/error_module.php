<?php
/**
 * Error Module
 *
 * @version $Id: error_module.php 523481 2012-03-25 19:49:08Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_E404 extends DWModule {
		public static $option = array( 'e404' => 'Error Page' );
		protected static $question = 'Show widget on the error page?';
	}
?>