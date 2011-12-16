<?php
if ( !current_user_can( 'view_projects' ) ) : 
     echo '<div class="error"><p style="text-align: center;">'.__("You do not have sufficient permissions to access this page.").'</p></div>';
else :
?>
<div class="wrap">
	<h2 id="top"><?php _e( 'Projectmanager Documentation', 'projectmanager' ) ?></h2>
	
	<h3><?php _e( 'Content', 'projectmanager') ?></h3>
	<ul>
	 <li><a href="#shortcodes"><?php _e( 'Shortcodes', 'projectmanager' ) ?></a></li>
	 <li><a href="#templates"><?php _e( 'Templates', 'projectmanager' ) ?></a></li>
	 <li><a href="#template_tags"><?php _e( 'Template Tags', 'projectmanager' ) ?></a></li>
	 <li><a href="#access"><?php _e( 'Access Control', 'projectmanager' ) ?></a></li>
	 <li><a href="#customization"><?php _e( 'Customization', 'projectmanager' ) ?></a></li>
	 <li><a href="#howto_intro"><?php _e( 'Howto', 'projectmanager' ) ?></a></li>
	 <li><a href="#donations"><?php _e( 'Donations', 'projectmanager' ) ?></a></li>
	</ul>
	
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
	<h3 id="shortcodes"><?php _e( 'Shortcodes', 'projectmanager' ) ?></h3>

	<!-- Shortcode to display all datasets from one project -->
 	<p><?php _e( 'The main shortcode is to display datasets of a project. The following shows a minimal example. A complete list of arguments is described in the table below.', 'projectmanager' ) ?></p>
	<blockquote><p>[project id=ID template=X]</p></blockquote>

	<table class="widefat">
	<thead>
		<tr>
			<th scope="col"><?php _e( 'Parameter', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Description', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Possible Values', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Default', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Optional', 'projectmanager' ) ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="" valign="top">
			<td>id</td>
			<td><?php _e( 'ID of Project', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'No', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>template</td>
			<td><?php _e( 'Template file used to display', 'projectmanager' ) ?></td>
			<td><?php _e( '<em>table</em>, <em>gallery</em> or template file without extension', 'projectmanager' ) ?></td>
			<td>table</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="" valign="top">
			<td>cat_id</td>
			<td><?php _e( 'Set this attribute to only display datasets of given category ID', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>orderby</td>
			<td><?php _e( 'order datasets by given field', 'projectmanager' ) ?></td>
			<td><?php _e( '<em>name</em>, <em>id</em>, <em>formfields-ID</em> (replace ID with respective ID or <em>rand</em>. <em>rand</em> must be used together with the results attribute to limit the number of datastes.', 'projectmanager' ) ?></td>
			<td>name</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="" valign="top">
			<td>order</td>
			<td><?php _e( 'Ordering of datasets', 'projectmanager' ) ?></td>
			<td><em>ASC</em>, <em>DESC</em></td>
			<td>ASC</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>single</td>
			<td><?php _e( 'Toggle link to single dataset', 'projectmanager' ) ?></td>
			<td><?php _e( '<em>true</em>, <em>false</em>', 'projectmanager' ) ?></td>
			<td>true</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="" valign="top">
			<td>selections</td>
			<td><?php _e( 'Toggle display of selection forms for categories and dataset ordering', 'projectmanager' ) ?></td>
			<td><em>true</em>, <em>false</em></td>
			<td>true</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>results</td>
			<td><?php _e( 'Limit number of datasets', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="" valign="top">
			<td>field_id</td>
			<td><?php _e( 'Filter datasets for formfield ID. Must be used together with <em>field_value</em>.', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>field_value</td>
			<td><?php _e( 'Filter datasets for formfield value. Must be used together with <em>field_id</em>.', 'projectmanager' ) ?></td>
			<td><em>string</em></td>
			<td>&#160;</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
	</tbody>
	</table>

	<!-- Shortcode to display individual dataset -->
	<p><?php _e( 'A single dataset can be displayed with the following code', 'projectmanager' ) ?></p>
	<blockquote><p>[dataset id=X]</p></blockquote>
	<table class="widefat">
	<thead>
		<tr>
			<th scope="col"><?php _e( 'Parameter', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Description', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Possible Values', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Default', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Optional', 'projectmanager' ) ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="" valign="top">
			<td>id</td>
			<td><?php _e( 'ID of Project', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'No', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>template</td>
			<td><?php _e( 'Template to use', 'projectmanager' ) ?></td>
			<td>file name without extension</td>
			<td>&#160;</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
	</tbody>
	</table>

	<!-- Shortcode to show search form -->
	<p><?php _e( 'A simple search form can be displayed with the following code', 'projectmanager' ) ?></p>
	<blockquote><p>[project_search project_id=ID template=X]</p></blockquote>
	<table class="widefat">
	<thead>
		<tr>
			<th scope="col"><?php _e( 'Parameter', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Description', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Possible Values', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Default', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Optional', 'projectmanager' ) ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="" valign="top">
			<td>project_id</td>
			<td><?php _e( 'ID of Project', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'No', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>template</td>
			<td><?php _e( 'Template to use', 'projectmanager' ) ?></td>
			<td><em>compact</em>, <em>extend</em></td>
			<td>extend</td>
			<td><?php _e( 'Yes', 'projectmanager' ) ?></td>
		</tr>
	</tbody>
	</table>

	<!-- Shortcode to include dataset input form -->
	<p><?php _e( 'It is possible to include the dataset input form with the following code', 'projectmanager' ) ?></p>
	<blockquote><p>[dataset_form project_id=ID]</p></blockquote>
	<table class="widefat">
	<thead>
		<tr>
			<th scope="col"><?php _e( 'Parameter', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Description', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Possible Values', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Default', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Optional', 'projectmanager' ) ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="" valign="top">
			<td>project_id</td>
			<td><?php _e( 'ID of Project', 'projectmanager' ) ?></td>
			<td><em>integer</em></td>
			<td>&#160;</td>
			<td><?php _e( 'No', 'projectmanager' ) ?></td>
		</tr>
	</tbody>
	</table>

	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
  	<h3 id="templates"><?php _e( 'Templates', 'projectmanager' ) ?></h3>
    <p><?php _e( 'Templates are special files that are used to display plugin data in the website frontend. They reside in the following directory', 'projectmanager' ) ?></p>
	<blockquote><p>WP_PLUGIN_DIR/projectmanager/templates/</p></blockquote>
	<p><?php _e( 'The following table lists all available default templates', 'projectmanager' ) ?></p>
	<table class="widefat">
	<thead>
		<tr>
			<th scope="col"><?php _e( 'Template', 'projectmanager' ) ?></th>
			<th scope="col"><?php _e( 'Description', 'projectmanager' ) ?></th>
		</tr>
	</thead>
		<tbody>
		<tr class="" valign="top">
			<td>table.php</td>
			<td><?php _e( 'Tabular display of datasets', 'projectmanager' ) ?></td>

		</tr>
		<tr class="alternate" valign="top">
			<td>gallery.php</td>
			<td><?php _e( 'Show datasets as photo gallery', 'projectmanager' ) ?></td>

		</tr>
		<tr class="" valign="top">
			<td>table-image.php</td>
			<td><?php _e( 'Tabular display of datasets with small image', 'projectmanager' ) ?></td>

		</tr>
		<tr class="alternate" valign="top">
			<td>selections.php</td>
			<td><?php _e( 'Display dropdown selections for categories and dataset sorting', 'projectmanager' ) ?></td>

		</tr>
		<tr class="" valign="top">
			<td>dataset-form.php</td>
			<td><?php _e( 'Dataset input formular', 'projectmanager' ) ?></td>
		</tr>
		<tr class="alternate" valign="top">
			<td>search-extend.php</td>
			<td><?php _e( 'Full Search formular', 'projectmanager' ) ?></td>

		</tr>
		<tr class="" valign="top">
			<td>search-compact.php</td>
			<td><?php _e( 'Compact Search formular', 'projectmanager' ) ?></td>
		</tr>
	</tbody>
	</table>
	<p><?php _e( 'If you want to modify existing templates copy it to', 'projectmanager' ) ?></p>
	<blockquote><p>your_theme_dir/projectmanager/</p></blockquote>
	<p><?php _e( 'The plugin will then first look in your theme directory. Further it is possible to design own templates. Assume you created a file <strong>sample1.php</strong>, to display datasets of a project. To use the template use the following tag.', 'projectmanager' ) ?></p>
	<blockquote><p>[project id=ID template=<strong>sample1</strong>]</p></blockquote>
	<p><?php _e( 'For single datasets templates must be named <strong>dataset-X.php</strong> and searchform <strong>search-X.php</strong>. The files are then loaded with the following tags.', 'projectmanager' ) ?></p>
	<blockquote><p>[dataset id=ID template=<strong>X</strong>]</p><p>[project_search project_id=ID template=<strong>X</strong>]</p></blockquote>
	
	
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
 	<h3 id="template_tags"><?php _e( 'Template Tags', 'projectmanager' ) ?></h3>
	<p><?php _e( 'Template Tags are functions that can be used in your Wordpress Theme to display the plugin data. Here is a brief listing of available tags. For details see file functions.php', 'projectmanager' ) ?><p>
	<dl class="projectmanager">
		<dt><pre>&lt;?php project( $id, $args ) ?&gt;</pre></dt><dd><?php _e( 'display all datasets from a single project', 'projectmanager' ) ?></dd>
		<dt><pre>&lt;?php dataset( $id, $args ) ?&gt;</pre></dt><dd><?php _e( 'display a single dataset', 'projectmanager' ) ?></dd>
		<dt><pre>&lt;?php projectmanager_display_widget( $number, $instance ) ?&gt;</pre></dt><dd><?php _e( 'Display widget. <em>$number</em> is the widget number and <em>$instance</em> is an assoziative array of widget settings. See lib/widget.php function widget for details.', 'projectmanager' ) ?></dd>
		<dt><pre>&lt;?php projectmanager_searchform( $project_id, $args ) ?&gt;</pre></dt><dd><?php _e( 'display the searchform. <em>$project_id</em> is the ID of the project.', 'projectmanager' ) ?></dd>
		<dt><pre>&lt;?php projectmanager_selections( $project_id ) ?&gt;</pre></dt><dd><?php _e( 'display the selection bar. <em>$project_id</em> is the ID of the project.', 'projectmanager' ) ?></dd>
		<dt><pre>&lt;?php projectmanager_datasetform( $project_id ) ?&gt;</pre></dt><dd><?php _e( 'display the dataset form. <em>$project_id</em> is the ID of the project.', 'projectmanager' ) ?></dd>
	</dl>
	<p><?php _e( 'The variable <em>$args</em> is always an assoziative array of additional arguments with keys being the same as the shortcode attributes.', 'projectmanager' ) ?></p>
	
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
 	<h3 id="access"><?php _e( 'Access Control', 'projectmanager' ) ?></h3>
	<p><?php printf( __( 'ProjectManager has fine grained capabilities to control access to different areas of the administration panel. You could use <a href="%s" target="_blank">Capability Manager</a> to manage roles and capabilities. <em>Note</em>: Capabilities are not inherent.', 'projectmanager' ), 'http://wordpress.org/extend/plugins/capsman/'); ?></p>
	<dl class="projectmanager">
		<dt>edit_projects</dt><dd>add and edit projects</dd>
		<dt>delete_projects</dt><dd>delete existing projects</dd>
		<dt>projectmanager_settings</dt><dd>allow access to global settings of ProjectManager</dd>
		<dt>edit_formfields</dt><dd>allow access to FormField Panel</dd>
		<dt>edit_projects_settings</dt><dd>allow access to individual projects settinigs</dd>
		<dt>import_datasets</dt><dd>access import/export panel</dd>
		<dt>edit_datasets</dt><dd>add datasets and edit own datasets</dd>
		<dt>edit_other_datasets</dt><dd>add datasets and edit all datasets and add WP User as dataset</dd>
		<dt>delete_datasets</dt><dd>delete own datasets</dd>
		<dt>delete_other_datasets</dt><dd>delete any dataset</dd>
		<dt>view_projects</dt><dd>browse projects in administration panel</dd>
		<dt>projectmanager_user</dt><dd>allow usage of profile hook</dd>
	</dl>
	
	
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
	<h3 id="extended_profile"><?php _e( 'Hook dataset into profile page', 'projectmanager' ) ?></h3>
	<p><?php _e( 'Each dataset is assigned an owner who added it. This owner can be also changed by anybody with the capability <em>edit_other_datasets</em>. This makes it possible to use ProjectManager as extended WP User Profile by activating the profile hook option in the projects settings. When this option is activated the first dataset of the current user is loaded into the profile page and can be edited through their profile. Administrators can also add datasets for other WP Users by using the button next to the dataset name when adding a new dataset. For new users, a dataset is automatically generated upon registration if the default user group has the capability <em>projectmanager_user</em>.', 'projectmanager' ) ?></p>
	
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
  	<h3 id="customization"><?php _e( 'Customization', 'projectmanager' ) ?></h3>
	<h4>Icons in admin menu</h4>
	<p><?php _e( 'If you want to use custom icons for the admin menu put them into the following folder in your theme directory', 'projectmanager' ) ?></p>
	<blockquote><p>projectmanager/icons</p></blockquote>
	
	<h4>Add Formfield types</h4>
	<p><?php _e( 'You can also add custom Formfields via the filter projectmanager_formfields. First let us add the field.', 'projectmanager' ) ?></h4>
	<code><pre>
	&lt;?php
	add_filter( 'projectmanager_formfields', 'my_formfields');

	function my_formfields( $formfields ) {
		$formfields['myfield'] = array( 'name' => 'My Field', 'callback' => 'get_myfield_data', 'args' => array());
		return $formfields;
	}
	?&gt;
	</pre></code>
	<p><?php _e( 'The <em>callback</em> option is a function which gets the data for this field as it is not stored in the ProjectManager Database. <em>args</em> can be an optional assoziative array of arguments that are passed to the callback function. Finally we just need to get the data from somewhere.', 'projectmanager' ) ?></p>
	<code><pre>
	&lt;?php
	function get_myfield_data( $dataset, $args ) {
		// $dataset is an assoziative array with keys 'id' and 'name' that hold the dataset ID and name respectively
		// do some stuff
	}
	?&gt;
	</pre></code>
	
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
	<h3 id="howto_intro"><?php _e( 'HowTo', 'projectmanager' ) ?></h3>
	<p><?php _e( 'This section should give a short introduction to setting up and using ProjectManager. In addition <a href="http://randyhoyt.com/" target="_blank">Randy Hoyt</a> created a <a href="http://randyhoyt.com/wordpress/snippets/" target="_blank">Screencast</a> on the usage of ProjectManager. Thanks a lot.', 'projectmanager' ) ?></p>
	<p><?php printf( __( 'After activation, there will be a new item in the admin menu. The first step is to <a href="%s" class="thickbox">add a new project</a>. When you click on a specific project you will get to an <a href="%s" class="thickbox">overview page displaying all datasets</a>. The screenshot already shows some example datasets. At first you should go to the <a href="%s" class="thickbox">settings page</a>. The option <em>Navi Link</em> will create an individual object in the admin menu. Activate the <em>Hook into profile option</em> to use ProjectManager as extended WP User Profile. Before adding datasets you need to setup the <a href="%s" class="thickbox">formfields</a>. This is a very powerful feature of ProjectManager as it allows the design of custom input formular to have datasets with a variety of data. You can choose to display the field value on the startpage and also control display in the user profile. Further it is possible to order datasets by certain formfield values. After that you are set to <a href="%s" class="thickbox">insert datasets</a>. It is possible to group datasets into different <a href="%s" class="thickbox">categories</a> using the WP category system.', 'projectmanager'), PROJECTMANAGER_URL .'/admin/doc/index.png', PROJECTMANAGER_URL .'/admin/doc/overview.png', PROJECTMANAGER_URL .'/admin/doc/settings.png', PROJECTMANAGER_URL .'/admin/doc/formfields.png', PROJECTMANAGER_URL .'/admin/doc/dataset-form.png', PROJECTMANAGER_URL .'/admin/doc/categories.png') ?></p>
	<p><?php printf( __( 'After activation, there will be a new item in the admin menu. The first step is to <a href="%s" class="thickbox">add a new project</a>. When you click on a specific project you will get to an <a href="%s" class="thickbox">overview page displaying all datasets</a>. The screenshot already shows some example datasets. At first you should go to the <a href="%s" class="thickbox">settings page</a>. The option <em>Navi Link</em> will create an individual object in the admin menu. Activate the <em>Hook into profile option</em> to use ProjectManager as extended WP User Profile. Before adding datasets you need to setup the <a href="%s" class="thickbox">formfields</a>. This is a very powerful feature of ProjectManager as it allows the design of custom input formular to have datasets with a variety of data. You can choose to display the field value on the startpage and also control display in the user profile. Further it is possible to order datasets by certain formfield values. After that you are set to <a href="%s" class="thickbox">insert datasets</a>. It is possible to group datasets into different <a href="%s" class="thickbox">categories</a> using the WP category system.', 'projectmanager'), PROJECTMANAGER_URL .'/admin/doc/index.png', PROJECTMANAGER_URL .'/admin/doc/overview.png', PROJECTMANAGER_URL .'/admin/doc/settings.png', PROJECTMANAGER_URL .'/admin/doc/formfields.png', PROJECTMANAGER_URL .'/admin/doc/dataset-form.png', PROJECTMANAGER_URL .'/admin/doc/categories.png') ?></p>
	<p><?php printf( __( 'The datasets of a project can be inserted into a page or post either by directly using the shortcodes which are described above or by using the <a href="%s" class="thickbox">TinyMCE Button</a>. The button, however, only covers a few default parameter. One default template is <a href="%s" class="thickbox">gallery</a> which displays the datasets in a multicolumn photogallery with a link to <a href="%s" class="thickbox">individual datasets</a>. This template is especially useful for athlete profiles. ', 'projectmanager' ), PROJECTMANAGER_URL .'/admin/doc/page.png', PROJECTMANAGER_URL .'/admin/doc/gallery.png', PROJECTMANAGER_URL .'/admin/doc/dataset.png') ?></p>
	<p><?php printf( __( 'ProjectManager also supplies a <a href="%s" class="thickbox">widget</a> and supports any number of widgets. It can be used to display a list of datasets in the sidebar, but also show a slideshow of datasets, e.g. athlete portraits.', 'projectmanager' ), PROJECTMANAGER_URL .'/admin/doc/widget.png') ?></p>
	<p><?php printf( __( 'ProjectManager also supports <a href="%s" class="thickbox">AJAX editing</a> of dataset values by clicking on the pencil symbol next to the field value on the overview page. <a href="%s" class="thickbox">E-Mail and URL</a> fields have a special syntax to add the values with a certain text.', 'projectmanager' ), PROJECTMANAGER_URL .'/admin/doc/ajax_group.png', PROJECTMANAGER_URL .'/admin/doc/ajax_email.png') ?></p>
	
	
	<a href="#top" class="alignright"><?php _e( 'Top', 'projectmanager' ) ?></a>
	<h3 id="donations"><?php _e( 'Donations', 'projectmanager' ) ?></h3>
	<p><?php _e( 'If you like my plugin and want to support me, I am grateful for any donation.', 'projectmanager' ) ?></p>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float: left; margin-right: 1em;">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="2329191">
		<input type="image" src="<?php echo PROJECTMANAGER_URL ?>/admin/doc/donate_eur.gif" border="0" name="submit" alt="Donate in Euro">
	</form>
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="3408441">
		<input type="image" src="<?php echo PROJECTMANAGER_URL ?>/admin/doc/donate_usd.gif" border="0" name="submit" alt="Donate in USD">
	</form>
</div>
<?php endif; ?>
