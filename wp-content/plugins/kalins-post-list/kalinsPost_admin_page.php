<?php

	if ( !function_exists( 'add_action' ) ) {
		echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
		exit;
	}
	
	$catList = get_categories('hide_empty=0');
	$tagList = get_tags('hide_empty=0');
	
	$save_preset_nonce = wp_create_nonce( 'kalinsPost_save_preset' );
	$delete_preset_nonce = wp_create_nonce('kalinsPost_delete_preset');
	$restore_preset_nonce = wp_create_nonce('kalinsPost_restore_preset');
	
	$adminOptions = kalinsPost_get_admin_options();
	
?>

<script language="javascript" type='text/javascript'>

	jQuery(document).ready(function($){
		
		var savePresetNonce = '<?php echo $save_preset_nonce; ?>';
		var deletePresetNonce = '<?php echo $delete_preset_nonce; ?>';
		var restorePresetNonce = '<?php echo $restore_preset_nonce; ?>';
		
		var catList = <?php echo json_encode($catList); ?>;
		var tagList = <?php echo json_encode($tagList); ?>;
	
		var presetArr = <?php echo $adminOptions["preset_arr"]; ?>;
		
		function getCatString(){
			var catString = '';
			var pageCount = 0;
			var l = catList.length;
			for(var i=0; i<l; i++){
				if($('#chkCat' + catList[i]['term_id']).is(':checked')){
					catString += catList[i]['term_id'] + ",";
					pageCount++;
				}
			}
			return catString;
		}
		
		function getTagString(){
			var tagString = '';
			var pageCount = 0;
			var l = tagList.length;
			for(var i=0; i<l; i++){
				if($('#chkTag' + tagList[i]['slug']).is(':checked')){
					tagString += tagList[i]['slug'] + ",";
					pageCount++;
				}
			}
			return tagString;
		}
		
		function setCatValues(str){
					
			var l = catList.length;
			for(var i=0; i<l; i++){
				$('#chkCat' + catList[i]['term_id']).attr('checked', false);
			}

			var arrCats = str.split(",");
			var l = arrCats.length;
			for(var i=0; i<l; i++){
				$('#chkCat' + arrCats[i]).attr('checked', true);
			}
		}
		
		function setTagValues(str){
			var l = tagList.length;		   
			for(var i=0; i<l; i++){
				$('#chkTag' + tagList[i]['slug']).attr('checked', false);
			}
			
			var arrCats = str.split(",");
			var l = arrCats.length;
			for(var i=0; i<l; i++){
				$('#chkTag' + arrCats[i]).attr('checked', true);
			}
		}
		
		function setPHPOutput(preset_name){
			//$('#presetPHP').html('PHP code: <code>if(function_exists("kalinsPost_show"){kalinsPost_show("' + data.preset_name + '");}</code>');
			
			$('#presetPHP').html('PHP code: <code>if(function_exists("kalinsPost_show")){kalinsPost_show("' + preset_name + '");}</code>');
		}
		
		function deletePreset(id){
			//alert("deleting: " + id);
			
			var data = { action: 'kalinsPost_delete_preset',
				_ajax_nonce : deletePresetNonce
			}
			
			data.preset_name = id;
			
			$('#createStatus').html("Deleting preset...");
	
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				//alert(response);
												
				var startPosition = response.indexOf("{");
				var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);
				
				var newFileData = JSON.parse(responseObjString);
				
				/*if(newFileData.status == "success"){
					$('#createStatus').html("Preset deleted successfully.");
				}else{
					$('#createStatus').html(response);
				}*/
				
				presetArr = newFileData;//.preset_arr;
				
				buildPresetTable();
				
				$('#createStatus').html("Preset deleted successfully.");
				
			});
		}
		
		function loadPreset(id){
			
			var newValues = presetArr[id];
			
			setCatValues(newValues["categories"]);
			setTagValues(newValues["tags"]);
			
			$('#txtNumberposts').val(newValues["numberposts"]);
			$('#txtBeforeList').val(newValues["before"]);
			$('#txtContent').val(newValues["content"]);
			$('#txtAfterList').val(newValues["after"]);
				
			$('#cboPost_type option[value=' + newValues["post_type"] + ']').attr('selected','selected'); 
			$('#cboOrderby option[value=' + newValues["orderby"] + ']').attr('selected','selected');
			$('#cboOrder option[value=' + newValues["order"] + ']').attr('selected','selected');
			
			$('#cboParent option[value=' + newValues["post_parent"] + ']').attr('selected','selected');
			
			if(newValues["excludeCurrent"] == 'true'){//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
				$('#chkExcludeCurrent').attr('checked', true);
			}else{
				$('#chkExcludeCurrent').attr('checked', false);
			}
			
			if(newValues["includeCats"] == 'true'){//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
				$('#chkIncludeCats').attr('checked', true);
			}else{
				$('#chkIncludeCats').attr('checked', false);
			}
			
			if(newValues["includeTags"] == 'true'){//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
				$('#chkIncludeTags').attr('checked', true);
			}else{
				$('#chkIncludeTags').attr('checked', false);
			}
			
			if(newValues["requireAllCats"] == 'true'){//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
				$('#chkRequireAllCats').attr('checked', true);
			}else{
				$('#chkRequireAllCats').attr('checked', false);
			}
			
			if(newValues["requireAllTags"] == 'true'){//hmmm, maybe there's a way to get an actual boolean to be passed through instead of the string
				$('#chkRequireAllTags').attr('checked', true);
			}else{
				$('#chkRequireAllTags').attr('checked', false);
			}
			
			$('#txtPresetName').val(id);
			
			$('#previewDiv').html("");
			
			//$('#presetPHP').html('PHP code: <code>kalinsPost_show("' + id + '");</code>');
			
			setPHPOutput(id);
			
			setNoneHide();
		}
		
		function buildPresetTable(){//build the file table - we build it all in javascript so we can simply rebuild it whenever an entry is added through ajax

			function tc(str){
				return "<td style='border:solid 1px' align='center'>" + str + "</td>";
			}
			
			var tableHTML = "<table style='border:solid 1px' width='725' border='1' cellspacing='1' cellpadding='3'><tr><th scope='col'>#</th><th scope='col'>Preset Name</th><th scope='col'>Load</th><th scope='col'>Delete</th><th scope='col'>Shortcode</th></tr>";

			var count = 0;
			for(i in presetArr){
				var shortcode = '[post_list preset="' + i + '"]';
				tableHTML += "<tr>" + tc(count) + tc(i) + tc("<button name='btnLoad_" + count + "' id='btnLoad_" + count + "'>Load</button>") + tc("<button name='btnDelete_" + count + "' id='btnDelete_" + count + "'>Delete</button>") + tc(shortcode) + "</tr>";
				count++;
			}
			
			tableHTML += "</table>";
			
			$('#presetListDiv').html(tableHTML);
			
			count = 0;
			for(j in presetArr){
				
				$('#btnDelete_' + count).attr('presetname', j);
				
				$('#btnDelete_' + count).click(function(){
					if(confirm("Are you sure you want to delete " + $(this).attr('presetname') + "?")){							
						deletePreset($(this).attr('presetname'));
					}
				});
				
				$('#btnLoad_' + count).attr('presetname', j);
				
				$('#btnLoad_' + count).click(function(){				
					loadPreset($(this).attr('presetname'));
				});
				
				count++;
			}	
		}
		
		$('#btnSavePreset').click(function(){
			var data = { action: 'kalinsPost_save_preset',
				_ajax_nonce : savePresetNonce
			}
			
			data.preset_name = $("#txtPresetName").val();
			
			if(data.preset_name == ""){
				$('#createStatus').html("Error: Please type a name for your preset, or press 'load' on any of the presets below to edit.");
				return;
			}
							   
			if(presetArr[data.preset_name]){				   
				if(!confirm("Are you sure you want to overwrite the preset " + data.preset_name)){
					$('#createStatus').html("<br/>");
					return;
				}
			}
			
			data.categories = getCatString();
			data.tags = getTagString();
			
			data.requireAllCats = $("#chkRequireAllCats").is(':checked');
			data.requireAllTags = $("#chkRequireAllTags").is(':checked');
			
			data.includeCats = $("#chkIncludeCats").is(':checked');
			data.includeTags = $("#chkIncludeTags").is(':checked');
			
			if(data.requireAllCats && data.categories.split(",").length < 3 && !data.includeCats){//check if there's only one or zeor cats selected (plus the empty one at the end)
				$('#createStatus').html("Error: If you select 'Require all selected categories' you must also select at least two categories.");
				alert("Error: If you select 'Require all selected categories' you must also select at least two categories.");
				return;
			}
			
			if(data.requireAllTags && data.tags.split(",").length < 3 && !data.includeTags){//check if there's only one or zeor cats selected (plus the empty one at the end)
				$('#createStatus').html("Error: If you select 'Require all selected tags' you must also select at least two tags.");
				alert("Error: If you select 'Require all selected tags' you must also select at least two tags.");
				return;
			}
			
			data.post_type = $("#cboPost_type").val();
			
			data.numberposts = $("#txtNumberposts").val();
			data.before = $("#txtBeforeList").val();
			data.content = $("#txtContent").val();
			data.after = $("#txtAfterList").val();
			
			data.orderby = $("#cboOrderby").val();
			data.order = $("#cboOrder").val();
			
			data.excludeCurrent = $("#chkExcludeCurrent").is(':checked');
			
			
			
			data.doCleanup = $("#chkDoCleanup").is(':checked');
			
			if($('#parentSelector').css('display')=='none'){
				data.post_parent = "None";
			}else{
				data.post_parent = $("#cboParent").val();
			}
			
			//$('#presetPHP').html('PHP code: <code>kalinsPost_show("' + data.preset_name + '");</code>');
			
			setPHPOutput(data.preset_name);
			
			$('#createStatus').html("Saving to preset...");
	
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response) {
				
				//alert(response);
												
				var startPosition = response.indexOf("{");
				var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);
				
				//alert(responseObjString);
				
				var newFileData = JSON.parse(responseObjString);
				
				presetArr = newFileData.preset_arr;
				buildPresetTable();
				$('#createStatus').html("Preset successfully saved.");
				
				if($("#chkShowPreview").is(':checked')){
					$('#previewDiv').html(newFileData.previewOutput);
				}else{
					$('#previewDiv').html("");
				}
				
				//$('#previewDiv').html("Hello floopy doop");
				
			});
		});
		
		$('#btnRestorePreset').click(function(){
			//alert(data.post_type);
			var data = { action: 'kalinsPost_restore_preset',
				_ajax_nonce : restorePresetNonce
			}
			
			if(confirm("Are you sure you want to restore all default presets? This will remove any changes you've made to the default presets, but will not delete your custom presets.")){
				
				$('#createStatus').html("Restoring presets...");
				$('#previewDiv').html("");
		
				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(ajaxurl, data, function(response) {
													
					var startPosition = response.indexOf("{");
					var responseObjString = response.substr(startPosition, response.lastIndexOf("}") - startPosition + 1);
					
					//alert(responseObjString);
					var newFileData = JSON.parse(responseObjString);
					
					presetArr = newFileData;//.preset_arr;
					buildPresetTable();
					//$('#createStatus').html("Preset successfully added.");
					
					$('#createStatus').html("Presets successfully reset.");
					
				});
			}
		});
		
		$('#cboPost_type').change(function() {
			setNoneHide();
		});
		
		function setNoneHide(){
			
			var postTypeVal = $("#cboPost_type").val() ;
			
			if(postTypeVal == "none"){
				$('.noneHide').hide();
				$('.noneShow').show();
				$('#createStatus').html("In 'None' mode, the content field will be displayed only once and all shortcodes will refer to the current page.");
			}else{
				$('.noneHide').show();
				$('.noneShow').hide();
				$('#createStatus').html("&nbsp;");
			}
			
			if(postTypeVal != "none" && postTypeVal != "post" && postTypeVal != "attachment"){//if it's a page or custom type, show parent selector
				$('#parentSelector').show();
			}else{
				$('#parentSelector').hide();
			}
		}
		
		buildPresetTable();
		
		//$('#outputSpan').hide();
		$('#parentSelector').hide();
		
	});
	
</script>

<style type="text/css">
	.txtHeader{
		width:610px;
		position:absolute;
		left:290px;
	}
</style>


<h2>Kalin's Post List - settings</h2>

<h3>by Kalin Ringkvist - <a href="http://kalinbooks.com/">KalinBooks.com</a></h3>

<p><h3><a href="http://kalinbooks.com/post-list-wordpress-plugin/">Plugin Page</a></h3></p>

<p><h3><a href="http://kalinbooks.com/post-list-wordpress-plugin/kalins-post-list-help/">Help and instructions</a></h3></p>

<br/><hr/><br/>

<p>Post type:
<select id="cboPost_type" name="cboPost_type" style="width:100px;">


<?php
	$post_types = get_post_types('','names');
	foreach ($post_types as $post_type ) {//loop to create each option value. this will grab post, page, attachment and any custom post types
		if($post_type != "revision" && $post_type != "nav_menu_item"){
			echo "<option value='$post_type'>" .$post_type ."</option>";
		}
	}
?>

<option value="none">none</option>
<option value="any">all</option>
</select>

<span class="noneHide">
&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;

Show count: <input type="text" size='5' name='txtNumberposts' id='txtNumberposts' value='' ></input>

&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;

Order by: <select id="cboOrderby" style="width:110px;">
<option value="post_date">post date</option>
<option value="author">author ID</option>
<option value="ID">post ID</option>
<option value="menu_order">menu_order</option>
<option value="modified">modified date</option>
<option value="parent">parent</option>
<option value="rand">random</option>
<option value="title">title</option>
</select>

<select id="cboOrder" style="width:110px;">
<option value="DESC">descending</option>
<option value="ASC">ascending</option>
</select>

</p></span>

<div id="parentSelector"><p>
Parent: <select id="cboParent" style="width:110px;">

<option value="">-any or none-</option>
<option value="current">-current page-</option>

<?php
	$posts = get_posts('numberposts=-1&post_type=any&orderby=title');
	foreach ($posts as $page) {
		if(get_children($page->ID)){//if this returns some children, show the option
			echo "<option value='" .$page->ID ."'>" .$page->post_title ."</option>";
		}
	}
?>

</select>


</p></div>

<div style="overflow:scroll; overflow-x:hidden; height:150px; width:239px; float:left; border:ridge; margin-right:20px; padding-left:10px;" class="noneHide">
<h3 align="center">Categories</h3>

<input type=checkbox id="chkIncludeCats" name="chkIncludeCats" ></ input> Include current post categories <br />
<input type=checkbox id="chkRequireAllCats" name="chkRequireAllCats" ></ input> Require all selected categories
<hr />

<?php
	$l = count($catList);
	for($i=0; $i<$l; $i++){//build our list of cats
		$pageID = $catList[$i]->term_id;
		echo('<input type=checkbox id="chkCat' .$pageID .'" name="chkCat' .$pageID .'" ></ input> ' .$catList[$i]->name .'<br />');
	}
?>

</div>

<div style="overflow:scroll; overflow-x:hidden; height:150px; width:239px; border:ridge; padding-left:10px; float:left; margin-right:20px"  class="noneHide">
<h3 align='center'>Tags</h3>
<input type=checkbox id="chkIncludeTags" name="chkIncludeTags" ></ input> Include current post tags <br />
<input type=checkbox id="chkRequireAllTags" name="chkRequireAllTags" ></ input> Require all selected tags
<hr />
<?php
	$l = count($tagList);
	for($i=0; $i<$l; $i++){//build our list of cats
		$pageID = $tagList[$i]->slug;//so retarded that categories run off IDs and tags run off slugs
		echo('<input type=checkbox id="chkTag' .$pageID .'" name="chkTag' .$pageID .'" ></ input> ' .$tagList[$i]->name .'<br />');
	}
?>
</div>

<div style="overflow:auto; height:150px; width:160px; border:ridge; padding-left:10px;" class="noneHide">

<p>Nothing selected means show everything, including tags or categories not yet created. Checking all will include everything, but will exclude all future categories or tags.</p>

</div>
<span  class="noneHide">
<br/><br/>


<p>Before list HTML: <textarea rows='4' cols='200' name='txtBeforeList' id='txtBeforeList' value=''  class="txtHeader"></textarea></p><br/><br/><br/></span>

<p>List item content: 

<textarea name='txtContent' id='txtContent' rows='4' cols="200" class="txtHeader"></textarea>

</p>

<span  class="noneHide">
<br/><br/><br/>

<p>After list HTML: <textarea rows='4' cols='200' name='txtAfterList' id='txtAfterList'  class="txtHeader noneHide"></textarea></p>

<br/><br/><br/>

<input type=checkbox id="chkExcludeCurrent" name="chkExcludeCurrent" checked="yes"></ input> Exclude current post from results

</span><br/><br/>

<p>
<button id="btnSavePreset">Save to Preset</button>&nbsp;&nbsp;:&nbsp;&nbsp;<input type="text" size='30' name='txtPresetName' id='txtPresetName' value='<?php echo $adminOptions["default_preset"]; ?>' ></input>&nbsp;&nbsp;&nbsp;&nbsp;<input type=checkbox id="chkShowPreview" name="chkShowPreview" checked="yes"></ input> show preview
</p>

<p><span id="createStatus">&nbsp;</span></p>

<p>
	<div style="width:700px; padding:10px">
		<div id="previewDiv">
        	Preview will appear here when saved</span>
		</div>  
	</div>
</p>

<p>
	<div id="presetListDiv">
	</div>
</p>

<p><button id="btnRestorePreset">Restore Preset Defaults</button></p><br/>

<p>
	<div id="presetPHP">
	PHP code - click load on any preset to generate PHP code for use in your theme
	</div>
</p>

<br/><hr/><br/>
		<p>
        <b>Shortcodes:</b> Use these codes inside the list item content (will throw errors if placed in before or after HTML fields)<br />
        </p>
        <p>
        <ul>
        
        <li><b>[ID]</b> - the ID number of the page/post</li>
        <li><b>[post_author]</b> - author of the page/post</li>
        <li><b>[post_permalink]</b> - the page permalink</li>
        <li><b>[post_date format="m-d-Y"]</b> - date page/post was created <b>*</b></li>
        <li><b>[post_date_gmt format="m-d-Y"]</b> - date page/post was created in gmt time <b>*</b></li>
        <li><b>[post_title]</b> - page/post title</li>
        <li><b>[post_content]</b> - page/post content</li>
        <li><b>[post_excerpt length="250"]</b> - page/post excerpt (note the optional character 'length' parameter)</li>
        <li><b>[post_name]</b> - page/post slug name</li>
        <li><b>[post_modified format="m-d-Y"]</b> - date page/post was last modified <b>*</b></li>
        <li><b>[post_modified_gmt format="m-d-Y"]</b> - date page/post was last modified in gmt time <b>*</b></li>
        <li><b>[guid]</b> - original URL of the page/post (post_permalink is probably better)</li>
        <li><b>[comment_count]</b> - number of comments posted for this post/page</li>

        <li><b>[item_number offset="1" increment="1"]</b> - the list index for each page/post. Offset parameter sets start position. Increment sets the number you want to increase on each loop.</li>
        <li><b>[final_end]</b> - on the final list item, everything after this shortcode will be excluded. This will allow you to have commas (or anything else) after each item except the last one.</li>
        <li><b>[post_pdf]</b> - URL to the page/post's PDF file. (Requires Kalin's PDF Creation Station plugin. See help menu for more info.)</li>
        
        <li><b>[post_meta name="custom_field_name"]</b> - page/post custom field value. Correct 'name' parameter required</li>
        <li><b>[post_tags delimeter=", " links="true"]</b> - post tags list. Optional 'delimiter' parameter sets separator text. Use optional 'links' parameter to turn off links to tag pages</li>
        <li><b>[post_categories delimeter=", " links="true"]</b> - post categories list. Parameters work like tag shortcode.</li>
        <li><b>[post_parent link="true"]</b> - post parent. Use optional 'link' parameter to turn off link</li>
        <li><b>[post_comments before="" after=""]</b> - post comments. Parameters represent text/HTML that will be inserted before and after comment list but will not be displayed if there are no comments. PHP coders: <a href="http://kalinbooks.com/2011/customize-comments-pdf-creation-station">learn how to customize comment display.</a></li>
        <li><b>[post_thumb]</b> - URL to the page/post's featured image (requires theme support)</li>
        <li><b>[php_function name="function_name" param=""]</b> - call a user-defined custom function. Refer to <a href="http://kalinbooks.com/2011/custom-php-functions/">this blog post</a> for instructions.</li>
        </ul></p>
        <p><b>*</b> Time shortcodes have an optional format parameter. Format your dates using these possible tokens: m=month, M=text month, F=full text month, d=day, D=short text Day Y=4 digit year, y=2 digit year, H=hour, i=minute, s=seconds. More tokens listed here: <a href="http://php.net/manual/en/function.date.php" target="_blank">http://php.net/manual/en/function.date.php.</a> </p>
        <p>Note: these shortcodes only work in the List item content box on this page.</p>

<br/><hr/><br/>
    
<p>Thank you for using Kalin's Post List. To report bugs, request help or suggest features, visit my <a href="http://kalinbooks.com/post-list-wordpress-plugin" target="_blank">plugin page</a>. If you have a cool example of Kalin's Post LIst in use, post a reply/link on my <a href="http://kalinbooks.com/post-list-wordpress-plugin/examples/">examples page.</a> If you find this plugin useful, please consider <A href="http://wordpress.org/extend/plugins/kalins-post-list/">rating this plugin on WordPress.org</A> or making a PayPal donation:</p>

<p>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="C6KPVS6HQRZJS">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="Donate to Kalin Ringkvist's WordPress plugin development.">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

</p><br/>
 
 <p><input type='checkbox' id='chkDoCleanup' name='chkDoCleanup' <?php if($adminOptions["doCleanup"] == "true"){echo "checked='yes' ";} ?>></input> Upon plugin deactivation clean up all database entries. (Save any preset to change this value)</p>

<p>You may also like <a href="http://kalinbooks.com/pdf-creation-station/">Kalin's PDF Creation Station WordPress Plugin</a> - <br /> Create highly customizable PDF documents from any combination of pages and posts, or add a link to generate a PDF on each individual page or post.</p>
<p>Or <a href="http://kalinbooks.com/easy-edit-links-wordpress-plugin/">Kalin's Easy Edit Links</a> - <br />  Adds a box to your page/post edit screen with links and edit buttons for all pages, posts, tags, categories, and links for convenient linking and editing.</p>

</html>