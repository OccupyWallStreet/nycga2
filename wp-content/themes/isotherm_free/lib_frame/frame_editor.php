<?php

//------ CUSTOM FILE EDITOR ------//
// Some parts of code used from BizzThemes 1.6 class 'thesis_custom_editor' @Chris Pearson

class bizz_custom_editor {
	function get_custom_files() {
		$files = array();
		$directory = opendir(BIZZ_LIB_CUSTOM); // Open the directory
		$exts = array('.php', '.css', '.js', '.txt', '.inc', '.htaccess', '.html', '.htm'); // What type of files do we want?

		while ($file = readdir($directory)) { // Read the files
			if ($file != '.' && $file != '..') { // Only list files within the _current_ directory
				$extension = substr($file, strrpos($file, '.')); // Get the extension of the file

				if ($extension && in_array($extension, $exts)) // Verify extension of the file; we can't edit images!
					$files[] = $file; // Add the file to the array
			}
		}

		closedir($directory); // Close the directory
		return $files; // Return the array of editable files
	}

	function is_custom_writable($file, $files) {
		if (!in_array($file, $files))
			$error = "<p><strong>" . __('Attention!', 'bizzthemes') . '</strong> ' . __('For security reasons, the file you are attempting to edit cannot be modified via this screen.', 'bizzthemes') . '</p>';
		elseif (!file_exists(BIZZ_LIB_CUSTOM)) // The custom/ directory does not exist
			$error = "<p><strong>" . __('Attention!', 'bizzthemes') . '</strong> ' . __('Your <code>custom/</code> directory does not appear to exist. Have you remembered to rename <code>/custom-sample</code>?', 'bizzthemes') . '</p>';
		elseif (!is_file(BIZZ_LIB_CUSTOM . '/' . $file)) // The selected file does not exist
			$error = "<p><strong>" . __('Attention!', 'bizzthemes') . '</strong> ' . __('The file you are attempting does not appear to exist.', 'bizzthemes') . '</p>';
		elseif (!is_writable(BIZZ_LIB_CUSTOM . '/custom.css')) // The selected file is not writable
			$error = "<p><strong>" . __('Attention!', 'bizzthemes') . '</strong> ' . sprintf(__('Your <code>/custom/%s</code> file is not writable by the server, and in order to modify the file via the admin panel, BizzThemes needs to be able to write to this file. All you have to do is set this file&#8217;s permissions to 666, and you&#8217;ll be good to go.', 'bizzthemes'), $file) . '</p>';

		if ( isset($error) ) { // Return the error + markup, if required
			$error = "<div class=\"warning\">\n\t$error\n</div>\n";
			return $error;
		}

		return false;
	}

	function bizzthemes_editor() {
		$custom_editor = new bizz_custom_editor;
?>
<div class="clear"><!----></div>
<div id="bizz_options" class="wrap<?php if (get_bloginfo('text_direction') == 'rtl') { echo ' rtl'; } ?>">
<?php
	if ( isset($_GET['save']) && $_GET['save'] == 'true' ) {
		if (!current_user_can('edit_themes'))
			wp_die(__('Easy there, homey. You don&#8217;t have admin privileges to access theme options.', 'bizzthemes'));

		$custom_editor = new bizz_custom_editor;

		if (isset($_POST['custom_file_submit'])) {
			$contents = stripslashes($_POST['newcontent']); // Get new custom content
			$file = $_POST['file']; // Which file?
			$allowed_files = $custom_editor->get_custom_files(); // Get list of allowed files

			if (!in_array($file, $allowed_files)) // Is the file allowed? If not, get outta here!
				wp_die(__('You have attempted to modify an ineligible file. Only files within the BizzThemes <code>/custom</code> folder may be modified via this interface. Thank you.', 'bizzthemes'));

			$file_open = fopen(BIZZ_LIB_CUSTOM . '/' . $file, 'w+'); // Open the file

			if ($file_open !== false) // If possible, write new custom file
				fwrite($file_open, $contents);

			fclose($file_open); // Close the file
			$updated = '&updated=true'; // Display updated message
		}

		if (isset($_POST['custom_file_jump'])) {
			$file = $_POST['custom_files'];
			$updated = '';
		}
		
	}
	
	if (file_exists(BIZZ_LIB_CUSTOM)) {
		// Determine which file we're editing. Default to something harmless, like custom.css.
		$file = ($_GET['file']) ? $_GET['file'] : 'custom.css';
		$files = $custom_editor->get_custom_files();
		$extension = substr($file, strrpos($file, '.'));

		// Determine if the custom file exists and is writable. Otherwise, this page is useless.
		$error = $custom_editor->is_custom_writable($file, $files);

		if ($error)
			echo $error;
		else {
			// Get contents of custom.css
			if (filesize(BIZZ_LIB_CUSTOM . '/' . $file) > 0) {
				$content = fopen(BIZZ_LIB_CUSTOM . '/' . $file, 'r');
				$content = fread($content, filesize(BIZZ_LIB_CUSTOM . '/' . $file));
				$content = htmlspecialchars($content);
			}
			else
				$content = '';
		}
?>
<div class="one_col">
	<h3><?php printf(__('Currently editing: <code>%s</code>', 'bizzthemes'), "custom/$file"); ?></h3>
	<p>
<?php
	if ($extension == '.php')
		echo "\t\t\t<div class=\"updated bizzalert\"><p>" . __('<strong>Note:</strong> Make sure you have <acronym title="File Transfer Protocol">FTP</acronym> server access, before you start modifying <acronym title="PHP: Hypertext Preprocessor">PHP</acronym> files. Bad code will make your site temporarily unusable.', 'bizzthemes') . "</p></div>\n";
		
	if ( isset($_GET['save']) )
		echo "\t\t\t<div class=\"updated bizzalert\"><p>" . __('File successfully updated.', 'bizzthemes') . "</p></div>\n";
?>
	<ul class="file-select">
		<li><a href="<?php echo admin_url("admin.php?page=bizz-editor&file=$file"); ?>" title="Edit <?php echo $file; ?>"><?php echo $file; ?></a></li>
<?php
		foreach ($files as $f) { // An option for each available file
			if ($f != $file) {
?>
			    <li><a href="<?php echo admin_url("admin.php?page=bizz-editor&file=$f"); ?>" title="Edit <?php echo $f; ?>"><?php echo $f; ?></a></li>
<?php
			}
		}
?>
	</ul>
	</p>
		
	    <form class="file_editor" method="post" id="template" name="template" action="<?php echo admin_url("admin.php?page=bizz-editor&file=$file&save=true"); ?>">
			<input type="hidden" id="file" name="file" value="<?php echo $file; ?>" />
			<p><textarea id="newcontent" name="newcontent" rows="25" cols="50" class="large-text editor-area"><?php echo $content; ?></textarea></p>
			<p>
				<input type="submit" class="save_button" id="custom_file_submit" name="custom_file_submit" value="Save" />
			</p>
		</form>
		
</div>
<?php
	}
?>
</div>
<?php
	}
}