<?php 
// don't load directly 
if ( !defined('ABSPATH') ) 
	die('-1');

if ( class_exists( 'WPeMatico_Campaign_edit_functions' ) ) return;

class WPeMatico_Campaign_edit_functions {
	function create_meta_boxes() {
		global $post,$campaign_data; 
		//$campaign_data = WPeMatico_Campaign_edit :: check_campaigndata( WPeMatico :: get_campaign ($post->ID) );
		$campaign_data = WPeMatico :: get_campaign ($post->ID);
		$cfg = get_option(WPeMatico :: OPTION_KEY);
		
	//	add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
		add_meta_box( 'cron-box', __('Campaign Schedule', WPeMatico :: TEXTDOMAIN ), array( 'WPeMatico_Campaign_edit' ,'cron_box' ),'wpematico','side', 'default' );
		add_meta_box( 'cat-box',__('Campaign Categories',WPeMatico::TEXTDOMAIN),array( 'WPeMatico_Campaign_edit' ,'cat_box'),'wpematico','side', 'default' );
		add_meta_box( 'log-box', __('Send log', WPeMatico :: TEXTDOMAIN ), array(  'WPeMatico_Campaign_edit'  ,'log_box' ),'wpematico','side', 'default' );
		add_meta_box( 'feeds-box', __('Feeds for this Campaign', WPeMatico :: TEXTDOMAIN ), array(  'WPeMatico_Campaign_edit'  ,'feeds_box' ),'wpematico','normal', 'default' );
		add_meta_box( 'options-box', __('Options for this campaign', WPeMatico :: TEXTDOMAIN ), array(  'WPeMatico_Campaign_edit'  ,'options_box' ),'wpematico','normal', 'default' );
		add_meta_box( 'images-box', __('Options for images', WPeMatico :: TEXTDOMAIN ), array(  'WPeMatico_Campaign_edit'  ,'images_box' ),'wpematico','normal', 'default' );
		add_meta_box( 'template-box', __('Post Template', WPeMatico :: TEXTDOMAIN ), array(  'WPeMatico_Campaign_edit'  ,'template_box' ),'wpematico','normal', 'default' );
		if ($cfg['enableword2cats'])   // Si está habilitado en settings, lo muestra 
			add_meta_box( 'word2cats-box', __('Word to Category options', WPeMatico :: TEXTDOMAIN ), array(  'WPeMatico_Campaign_edit'  ,'word2cats_box' ),'wpematico','normal', 'default' );
		if ($cfg['enablerewrite'])   // Si está habilitado en settings, lo muestra 
			add_meta_box( 'rewrite-box', __('Rewrite options', WPeMatico :: TEXTDOMAIN ), array(  'WPeMatico_Campaign_edit'  ,'rewrite_box' ),'wpematico','normal', 'default' );
		//***** Call nonstatic
		if( $cfg['nonstatic'] ) { NoNStatic :: meta_boxes($campaign_data, $cfg); }
		// Publish Meta_box edited
		add_action('post_submitbox_start', array( __CLASS__ ,'post_submitbox_start')); 
	}
	
	
		//*************************************************************************************
	function rewrite_box( $post ) { 
		global $post, $campaign_data;
		$campaign_rewrites = $campaign_data['campaign_rewrites'];
		if(!($campaign_rewrites)) $campaign_rewrites = array();
		?>
		<div class="LightPink inmetabox">
		<p class="he20">
		<span class="left"><?php _e('Replaces words or phrases by other that you want or turns into link.', WPeMatico :: TEXTDOMAIN ) ?></span> 
		<label title="<?php _e('A little Help', WPeMatico :: TEXTDOMAIN ); ?>" onclick="jQuery('#hlprwg').fadeToggle();" class="m4 ui-icon QIco left"></label></p>		
		<p class="mphlp"><span class="srchbdr0 hide" id="hlprwg">
			<b><?php _e('Basics:', WPeMatico :: TEXTDOMAIN ); ?></b> <?php _e('The rewriting settings allow you to replace parts of the content with the text you specify.', WPeMatico :: TEXTDOMAIN ); ?><br />
			<b><?php _e('Basic rewriting:', WPeMatico :: TEXTDOMAIN ); ?></b><br />
			<?php _e('To replace all occurrences the word ass with butt, simply type ass in the "origin field", and butt in "rewrite to".', WPeMatico :: TEXTDOMAIN ); ?><br />
			<b><?php _e('Relinking:', WPeMatico :: TEXTDOMAIN ); ?></b><br />
			<?php _e('If you want to find all occurrences of google and make them link to Google, just type google in the "origin field" and http://google.com in the "relink to" field.', WPeMatico :: TEXTDOMAIN ); ?><br />
			<b><?php _e('Regular expressions', WPeMatico :: TEXTDOMAIN ); ?></b><br />
			<?php _e('For advanced users, regular expressions are supported. Using this will allow you to make more powerful replacements. Take multiple word replacements for example. Instead of using many rewriting boxes to replace ass and arse with butt, you can use the | operator: (ass|arse).', WPeMatico :: TEXTDOMAIN ); ?>
		</span></p>
		<div id="rewrites_edit" class="inlinetext">		
			<?php for ($i = 0; $i <= count(@$campaign_rewrites['origin']); $i++) : ?>			
			<div class="<?php if(($i % 2) == 0) echo 'bw'; else echo 'lightblue'; ?> <?php if($i==count($campaign_rewrites['origin'])) echo 'hide'; ?>">
				<div class="pDiv jobtype-select p7" id="nuevorew">
					<div id="rw1" class="wi30 left p4">
						<?php _e('Origin:','wpematico') ?>&nbsp;&nbsp;&nbsp;&nbsp;
						<input name="campaign_word_option_regex[<?php echo $i; ?>]" id="campaign_word_option_regex" class="checkbox" value="1" type="checkbox"<?php checked(@$campaign_rewrites['regex'][$i],true) ?> /> <?php _e('RegEx','wpematico') ?>
						<textarea class="large-text he35" id="campaign_word_origin" name="campaign_word_origin[<?php echo $i; ?>]" /><?php echo stripslashes(@$campaign_rewrites['origin'][$i]) ?></textarea>
					</div>
					<div class="wi30 left p4">
						 <?php _e('Rewrite to:','wpematico') ?>
						<textarea class="large-text he35" id="campaign_word_rewrite" name="campaign_word_rewrite[<?php echo $i; ?>]" /><?php echo stripslashes(@$campaign_rewrites['rewrite'][$i]) ?></textarea>
					</div>
					<div class="wi30 left p4">
						 <?php _e('ReLink to:','wpematico') ?>
						<textarea class="large-text he35" id="campaign_word_relink" name="campaign_word_relink[<?php echo $i; ?>]" /><?php echo stripslashes(@$campaign_rewrites['relink'][$i]) ?></textarea>
					</div>
					<div class="m7">
						<span class="" id="w2cactions">
							<label title="<?php _e('Delete this item', WPeMatico :: TEXTDOMAIN ); ?>" onclick=" jQuery(this).parent().parent().parent().children('#rw1').children('#campaign_word_origin').text(''); jQuery(this).parent().parent().parent().fadeOut();" class="right ui-icon redx_circle"></label>
						</span>
					</div>
				</div>
			</div>
			<?php endfor ?>
			<input id="rew_max" value="<?php echo $i; ?>" type="hidden" name="rew_max">
			
		  </div>
		  <div id="paging-box">		  
				<a href="JavaScript:void(0);" class="button-primary left m4" id="addmorerew" style="font-weight: bold; text-decoration: none;"><?php _e('Add more', WPeMatico :: TEXTDOMAIN ); ?>.</a>
		  </div>
		</div>

		<?php 
	}
	
	//*************************************************************************************
	function word2cats_box( $post ) { 
		global $post, $campaign_data;
		
		$campaign_wrd2cat = $campaign_data['campaign_wrd2cat'];
		if(!($campaign_wrd2cat)) $campaign_wrd2cat = array();
		?>
		<div class="Papaya inmetabox">
		<p class="he20">
		<span class="left"><?php _e('Assigning categories based on content words.', WPeMatico :: TEXTDOMAIN ) ?></span> 
		<label title="<?php _e('A little Help', WPeMatico :: TEXTDOMAIN ); ?>" onclick="jQuery('#hlpwtoc').fadeToggle();" class="m4 ui-icon QIco left"></label></p>	
		<p class="mphlp"><span class="srchbdr0 hide" id="hlpwtoc">
			<b><?php _e('Basics:', WPeMatico :: TEXTDOMAIN ); ?></b> <?php _e('The Word to Category option allow you to assign singular category to the post.', WPeMatico :: TEXTDOMAIN ); ?><br />
			<b><?php _e('Example:', WPeMatico :: TEXTDOMAIN ); ?></b><br />
			<?php _e('If the post content contain the word "motor" and then you want assign the post to category "Engines", simply type "motor" in the "Word" field, and select "Engine" in Categories combo.', WPeMatico :: TEXTDOMAIN ); ?><br />
			<b><?php _e('Regular Expressions', WPeMatico :: TEXTDOMAIN ); ?></b><br />
			<?php _e('For advanced users, regular expressions are supported. Using this will allow you to make more powerful replacements. Take multiple word replacements for example. Instead of using many Word2Cat boxes to assign motor and car to Engines, you can use the | operator: (motor|car). If you want Case insensitive on RegEx, add "/i" at the end of RegEx.', WPeMatico :: TEXTDOMAIN ); ?>
		<br /></span></p>
		<div id="wrd2cat_edit" class="inlinetext">		
			<?php for ($i = 0; $i <= count(@$campaign_wrd2cat['word']); $i++) : ?>			
			<div class="<?php if(($i % 2) == 0) echo 'bw'; else echo 'lightblue'; ?> <?php if($i==count($campaign_wrd2cat['word'])) echo 'hide'; ?>">
				<div class="pDiv jobtype-select p7" id="nuevow2c">
					<div id="w1" style="float:left;">
						<?php _e('Word:', WPeMatico :: TEXTDOMAIN ) ?> <input type="text" size="25" class="regular-text" id="campaign_wrd2cat" name="campaign_wrd2cat[<?php echo $i; ?>]" value="<?php echo stripslashes(@$campaign_wrd2cat['word'][$i]); ?>" /><br />
						<input name="campaign_wrd2cat_regex[<?php echo $i; ?>]" id="campaign_wrd2cat_regex" class="checkbox w2cregex" value="1" type="checkbox"<?php checked($campaign_wrd2cat['regex'][$i],true) ?> /> <?php _e('RegEx', WPeMatico :: TEXTDOMAIN ) ?>
						<input <?php echo ($campaign_wrd2cat['regex'][$i]) ? 'disabled' : '';?> name="campaign_wrd2cat_cases[<?php echo $i; ?>]" id="campaign_wrd2cat_cases" class="checkbox w2ccases" value="1" type="checkbox"<?php checked($campaign_wrd2cat['cases'][$i],true) ?> /> <?php _e('Case sensitive', WPeMatico :: TEXTDOMAIN ) ?>
					</div>
					<div id="c1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php _e('To Category:', WPeMatico :: TEXTDOMAIN ) ?>
						<?php 
						$catselected='selected='.$campaign_wrd2cat['w2ccateg'][$i];
						$catname="name=campaign_wrd2cat_category[".$i."]";
						$catid="id=campaign_wrd2cat_category_".$i;
						wp_dropdown_categories('hide_empty=0&hierarchical=1&show_option_none='.__('Select category', WPeMatico :: TEXTDOMAIN ).'&'.$catselected.'&'.$catname.'&'.$catid);
						?>
						<span class="wi10" id="w2cactions">
							<label title="<?php _e('Delete this item', WPeMatico :: TEXTDOMAIN ); ?>" onclick=" jQuery(this).parent().parent().parent().children('#w1').children('#campaign_wrd2cat').attr('value',''); jQuery(this).parent().parent().parent().fadeOut();" class="right ui-icon redx_circle"></label>
					</span>
					</div>
				</div>
			</div>
			<?php endfor ?>
			<input id="wrd2cat_max" value="<?php echo $i; ?>" type="hidden" name="wrd2cat_max">
			
		  </div>
		  <div id="paging-box">
				<a href="JavaScript:void(0);" class="button-primary left m4" id="addmorew2c" style="font-weight: bold; text-decoration: none;"><?php _e('Add more', WPeMatico :: TEXTDOMAIN ); ?>.</a>
		  </div>
		</div>

		<?php 
	}
	
	
	//*************************************************************************************
	function template_box( $post ) { 
		global $post, $campaign_data;
		$campaign_enable_template = @$campaign_data['campaign_enable_template'];
		$campaign_template = @$campaign_data['campaign_template'];
		$cfg = get_option(WPeMatico :: OPTION_KEY);
		?>
		<div class="lightblue inmetabox">
		<p class="he20">
		<span class="left"><?php _e('Modify, manage or add extra content to every post fetched.', WPeMatico :: TEXTDOMAIN ) ?></span> 
		<label title="<?php _e('A little Help', WPeMatico :: TEXTDOMAIN ); ?>" onclick="  jQuery('#hlptpl').fadeToggle();" class="m4 ui-icon QIco left"></label></p>		
		<p class="mphlp"><span class="srchbdr0 hide" id="hlptpl"><?php _e('The post template takes the full text of each feed item it encounters, and then uses it as the post content.<br />A post template, if used, allows you to alter that content, by adding extra information, such as text, images, campaign data, etc.', WPeMatico :: TEXTDOMAIN ); ?></span></p>
		
		<div id="wpe_post_template_edit" class="inlinetext">
			<input name="campaign_enable_template" id="campaign_enable_template" class="checkbox" value="1" type="checkbox"<?php checked($campaign_enable_template,true) ?> />
			<label for="campaign_enable_template"> <?php _e('Enable Post Template', WPeMatico :: TEXTDOMAIN ) ?></label>
			<textarea class="large-text" id="campaign_template" name="campaign_template" /><?php echo stripslashes($campaign_template) ?></textarea>
			<p class="he20"><span id="tags_note" class="note left"> Valid tags: </span>
			<label title="<?php _e('A little Help', WPeMatico :: TEXTDOMAIN ); ?>" onclick="jQuery('#tags_list').fadeToggle(); jQuery('#tags_list_det').fadeToggle();" class="m4 ui-icon QIco left"></label></p>
			<p id="tags_list" style="border-left: 3px solid #EEEEEE; color: #999999; font-size: 11px; padding-left: 6px;">
				<span class="tag">{content}</span>, <span class="tag">{title}</span>, <span class="tag">{author}</span>, <span class="tag">{authorlink}</span>, <span class="tag">{permalink}</span>, <span class="tag">{feedurl}</span>, <span class="tag">{feedtitle}</span>, <span class="tag">{feeddescription}</span>, <span class="tag">{feedlogo}</span>, <span class="tag">{campaigntitle}</span>, <span class="tag">{campaignid}</span>
			</p>
			<div id="tags_list_det" style="display: none;">
				<h4><?php _e('Supported tags', WPeMatico :: TEXTDOMAIN ); ?></h4>
				<p><?php _e('A tag is a piece of text that gets replaced dynamically when the post is created. Currently, these tags are supported:', WPeMatico :: TEXTDOMAIN ); ?></p>
				<ul style='list-style-type: square;margin:0 0 5px 20px;font:0.92em "Lucida Grande","Verdana";'>
				  <li><strong class="tag">{content}</strong> <?php _e('The feed item content.', WPeMatico :: TEXTDOMAIN ); ?> </li>
				  <li><strong class="tag">{title}</strong> <?php _e('The feed item title.', WPeMatico :: TEXTDOMAIN ); ?> </li>
				  <li><strong class="tag">{author}</strong> <?php _e('The feed item author.', WPeMatico :: TEXTDOMAIN ); ?> </li>
				  <li><strong class="tag">{authorlink}</strong> <?php _e('The feed item author link (If exist).', WPeMatico :: TEXTDOMAIN ); ?> </li>
				  <li><strong class="tag">{permalink}</strong> <?php _e('The feed item permalink.', WPeMatico :: TEXTDOMAIN ); ?> </li>
				  <li><strong class="tag">{feedurl}</strong> <?php _e('The feed URL.', WPeMatico :: TEXTDOMAIN ); ?> </li>
				  <li><strong class="tag">{feedtitle}</strong> <?php _e('The feed title.', WPeMatico :: TEXTDOMAIN ); ?> </li>
				  <li><strong class="tag">{feeddescription}</strong> <?php _e('The description of the feed.', WPeMatico :: TEXTDOMAIN ); ?> </li>
				  <li><strong class="tag">{feedlogo}</strong> <?php _e('The feed\'s logo image URL.', WPeMatico :: TEXTDOMAIN ); ?> </li>
				  <li><strong class="tag">{campaigntitle}</strong> <?php _e('This campaign title', WPeMatico :: TEXTDOMAIN ); ?> </li>
				  <li><strong class="tag">{campaignid}</strong> <?php _e('This campaign ID.', WPeMatico :: TEXTDOMAIN ); ?> </li>
				</ul>
				<b><?php _e('Example:', WPeMatico :: TEXTDOMAIN ); ?></b> <p><?php _e('If you want to add a link to the source at the bottom of every post and the author, the post template would look like this:', WPeMatico :: TEXTDOMAIN ); ?></p>
				<div class="code">{content}<br>&lt;a href="{permalink}"&gt;<?php _e('Go to Source', WPeMatico :: TEXTDOMAIN ); ?>&lt;/a&gt;&lt;br /&gt;<br>Author: {author}</div>
				<p><em>{content}</em> <?php _e('will be replaced with the feed item content', WPeMatico :: TEXTDOMAIN ); ?>, <em>{permalink}</em> <?php _e('by the source feed item URL, which makes it a working link and', WPeMatico :: TEXTDOMAIN ); ?> <em>{author}</em> <?php _e('with the original author of the feed item.', WPeMatico :: TEXTDOMAIN ); ?></p>
			</div>

		</div>
		<?php if( $cfg['nonstatic'] ) { NoNStatic :: last_html_tag($post, $cfg); } ?>
		</div>

		<?php
	}
	//*************************************************************************************
	function images_box( $post ) { 
		global $post, $campaign_data;
		
		$campaign_imgcache = $campaign_data['campaign_imgcache'];
		$campaign_cancel_imgcache = $campaign_data['campaign_cancel_imgcache'];
		$campaign_nolinkimg = $campaign_data['campaign_nolinkimg'];
		$cfg = get_option(WPeMatico :: OPTION_KEY);
		if (!$cfg['imgcache']) : ?>
			<p><input name="campaign_imgcache" id="campaign_imgcache" class="checkbox left" value="1" type="checkbox" <?php checked($campaign_imgcache,true); ?> style="width: 19px;" />
			<b class="left he20"><?php echo '<label for="campaign_imgcache">' . __('Enable Cache Images for this campaign?', WPeMatico :: TEXTDOMAIN ) . '</label>'; ?></b>
			&nbsp;&nbsp; <label title="<?php _e('A little Help', WPeMatico :: TEXTDOMAIN ); ?>" onclick="  jQuery('#hlpimg').fadeToggle();" class="m4 ui-icon QIco left"></label>
			</p>
			<p class="mphlp"><span class="srchbdr0 hide" id="hlpimg"><b><?php _e("Image Caching",  WPeMatico :: TEXTDOMAIN ) ?>:</b> <?php _e("When image caching is on, a copy of every image found (only in &lt;img&gt; tags) is downloaded to the specified directory, by default in Wordpress UPLOADS Dir (Highly recommended).",  WPeMatico :: TEXTDOMAIN ) ?><br />
			<?php _e("If not enabled all images will linked to the image owner's server, but also make your website faster for your visitors.",  WPeMatico :: TEXTDOMAIN ) ?><br />
			<b><?php _e("Note",  WPeMatico :: TEXTDOMAIN ) ?>:</b> <?php _e("If this featured is disabled the general Settings options for images caching is taken. Enabling this feature here will be overridden only for this campaign the general Settings options for images caching.",  WPeMatico :: TEXTDOMAIN ) ?></span></p>
		<?php else : ?>
			<p><input name="campaign_cancel_imgcache" id="campaign_cancel_imgcache" class="checkbox" value="1" type="checkbox" <?php checked($campaign_cancel_imgcache,true); ?> />
			<b><?php echo '<label for="campaign_cancel_imgcache">' . __('Cancel Cache Images for this campaign?', WPeMatico :: TEXTDOMAIN ) . '</label>'; ?></b></p>
		<?php endif ?>
		<div id="nolinkimg" <?php if (!$campaign_imgcache) echo 'style="display:none;"';?>>
			<p><input name="campaign_nolinkimg" id="campaign_nolinkimg" class="checkbox" value="1" type="checkbox" <?php checked($campaign_nolinkimg,true); ?> />
			<b><?php echo '<label for="campaign_nolinkimg">' . __('No link to source images', WPeMatico :: TEXTDOMAIN ) . '</label>'; ?></b></p>
			<p class="mphlp"><b><?php _e("Note",  WPeMatico :: TEXTDOMAIN ) ?>:</b> <?php _e('If selected and image upload get error, then delete the "src" attribute of the &lt;img&gt;. <br />If disable cache images, enabling this for delete "src" attribute of all &lt;img&gt; in the post.', WPeMatico :: TEXTDOMAIN ) ?></p>
		</div>
	<?php
	}
	//*************************************************************************************
	function options_box( $post ) { 
		global $post, $campaign_data;
		$campaign_max = $campaign_data['campaign_max'];
		$campaign_author = $campaign_data['campaign_author'];
		$campaign_linktosource = $campaign_data['campaign_linktosource'];
		$campaign_commentstatus = $campaign_data['campaign_commentstatus'];
		$campaign_allowpings = $campaign_data['campaign_allowpings'];
		?>
		<p><b><?php echo '<label for="campaign_max">' . __('Max items to create on each fetch:', WPeMatico :: TEXTDOMAIN ) . '</label>'; ?></b>
		<input name="campaign_max" type="text" size="3" value="<?php echo $campaign_max;?>" class="small-text" id="campaign_max"/><br />
		<?php _e("Set it to 0 for unlimited. If set to a X value, only the last X items will be selected, ignoring the older ones.",  WPeMatico :: TEXTDOMAIN ) ?></p>

		<p><b><?php echo '<label for="campaign_author">' . __('Author:', WPeMatico :: TEXTDOMAIN ) . '</label>'; ?></b>
		<?php wp_dropdown_users(array('name' => 'campaign_author','selected' => $campaign_author )); ?>
		<span class="note"><?php _e("The created posts will be assigned to this author.",  WPeMatico :: TEXTDOMAIN ) ?></span></p>

		<p><b><?php echo '<label for="campaign_linktosource">' . __('Post title links to source?', WPeMatico :: TEXTDOMAIN ) . '</label>'; ?></b>
		<input class="checkbox" type="checkbox"<?php checked($campaign_linktosource ,true);?> name="campaign_linktosource" value="1" id="campaign_linktosource"/> <br />
		<?php _e("This option make the title permalink to original URL.", WPeMatico :: TEXTDOMAIN ) ?></p>

		<p><b><?php echo '<label for="campaign_commentstatus">' . __('Discussion options:', WPeMatico :: TEXTDOMAIN ) . '</label>'; ?></b>
		<?php //echo 'campaign_commentstatus = '.$campaign_commentstatus;  ?>
		<select id="campaign_commentstatus" name="campaign_commentstatus">
		<option value="open"<?php echo ($campaign_commentstatus =="open" || $campaign_commentstatus =="") ? 'SELECTED' : ''; ?> >Open</option>
		<option value="closed" <?php echo ($campaign_commentstatus =="closed") ? 'SELECTED' : ''; ?> >Closed</option>
		<option value="registered_only" <?php echo ($campaign_commentstatus =="registered_only") ? 'SELECTED' : ''; ?> >Registered only</option>
		</select>
		<input class="checkbox" type="checkbox"<?php checked($campaign_allowpings ,true);?> name="campaign_allowpings" value="1" id="campaign_allowpings"/> <?php echo '<label for="campaign_allowpings">' . __('Allow pings?', WPeMatico :: TEXTDOMAIN ) . '</label>'; ?><br />
		</p>
		<?php
	}

	//*************************************************************************************
	function feeds_box( $post ) {  
		global $post, $campaign_data;
		$cfg = get_option(WPeMatico :: OPTION_KEY);
		$campaign_feeds = $campaign_data['campaign_feeds'];
		if(!($campaign_feeds)) $campaign_feeds = array();
		?>  <div class="submenu_dropdown">
 		<div id="domainsPlaceHolder">
		  <div class="filter_bar">
			<span class="srchbdr0">
				<?php _e('Please fill in at least one feed.', WPeMatico :: TEXTDOMAIN ) ?>
				<label title="<?php _e('A little Help', WPeMatico :: TEXTDOMAIN ); ?>" onclick="  jQuery('#feedhlp').fadeToggle();" class="m4 ui-icon QIco left"></label>
			</span>
			<span class="srchbdr0 hide" id="feedhlp"><?php _e('If you\'re not sure about the exact feed url, just type in the domain name, and the feed will be autodetected.', WPeMatico :: TEXTDOMAIN ) ?></span>
			<div class="right srchFilterOuter">
				<div style="float:left;margin-left:2px;">
					<input id="psearchtext" name="psearchtext" class="srchbdr0" type="text" value=''>
				</div>
			  <div class="srchSpacer"></div>
			  <div id="productsearch" class="left mya4_sprite searchIco" style="margin-top:4px;"></div>
			</div>
		  </div>
		  <div id="domainsBlock">
			<div id="feeds_edit" class="maxhe290">      
			
			  <?php 
				foreach($campaign_feeds as $id => $feed): ?>
				<div class="<?php if(($id % 2) == 0) echo 'bw'; else echo 'lightblue'; ?>">
					<div class="pDiv jobtype-select">
					<?php echo '<span class="left mp04 b">' . __('Feed URL:', WPeMatico :: TEXTDOMAIN ) . '</span>
					<input class="feedinput" type="text" value="' . $feed . '" id="feed_' . $id . '" name="campaign_feeds[]">';  ?>
					<?php if( $cfg['nonstatic'] ) { NoNStatic :: feedat($feed, $cfg); }  ?>
					<span class="wi10" id="feedactions">
						<label title="<?php _e('Delete this item', WPeMatico :: TEXTDOMAIN ); ?>" onclick="if(confirm('Are you sure ?')){ jQuery(this).parents('div').children('input').attr('value',''); jQuery(this).parent().parent().fadeOut();}" class="m4 right ui-icon redx_circle"></label>
						<label title="<?php _e('Check if this item work', WPeMatico :: TEXTDOMAIN ); ?>" id="checkfeed_<?php echo $id; ?>"  class="check1feed m4 right ui-icon yellowalert_small"></label><img src="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif" id="ruedita1" class="hide" Title="<?php _e('Checking', WPeMatico :: TEXTDOMAIN ); ?>">
					</span>
					</div>
				</div>
			  <?php endforeach ?>
				<span id="newfeed">
				<div class="pDiv jobtype-select">
					<?php echo '<span class="left mp04 b">' . __('New Feed:', WPeMatico :: TEXTDOMAIN ) . '</span>
					<input class="feedinput" type="text" value="" id="feed_new" name="campaign_feeds[]">'; ?>
					<?php if( $cfg['nonstatic'] ) { NoNStatic :: feedat('', $cfg); }  ?>
					<span class="wi10" id="feedactions">
						<label title="<?php _e('Delete this item', WPeMatico :: TEXTDOMAIN ); ?>" onclick="if(confirm('Are you sure ?')){ jQuery(this).parents('div').children('input').attr('value',''); jQuery(this).parent().parent().fadeOut();}" class="m4 right ui-icon redx_circle"></label>
						<label title="<?php _e('Check if this item work', WPeMatico :: TEXTDOMAIN ); ?>" id="checkfeed"  class="check1feed m4 right ui-icon yellowalert_small"></label><img src="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif" id="ruedita1" class="hide" Title="<?php _e('Checking', WPeMatico :: TEXTDOMAIN ); ?>">
					</span>
				</div>
				</span>

			</div>
			<?php if($cfg['nonstatic']){NoNStatic::feedlist();} ?>
		  </div>
		  
		  <div class="left he20">
			<p class="m7"><a href="JavaScript:void(0);" class="button-primary" id="addmore" onclick="s=jQuery('#newfeed');s.children('div').show();jQuery('#feeds_edit').append( s.html() ); jQuery('#feeds_edit input:last').focus()" style="font-weight: bold; text-decoration: none;" ><?php _e('Add more', WPeMatico :: TEXTDOMAIN ); ?>.</a>
			<span class="button-primary" id="checkfeeds" style="font-weight: bold; text-decoration: none;" ><?php _e('Check all feeds', WPeMatico :: TEXTDOMAIN ); ?>. <img src="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif" id="ruedita" style="height:13px; display:none;" Title="<?php _e('Checking', WPeMatico :: TEXTDOMAIN ); ?>"></span>
			<?php if($cfg['nonstatic']){NoNStatic::bimport();} ?>
			</p>
		  </div>
		  
		  <div id="paging-params" class="hide" data-totalrecords="<?php echo $id+1; ?>" data-totalpages="3" data-currentpage="1" data-pagesize="5"></div>
			
		  <div id="paging-box">
			  <div class="p7">
				<div class="leftText">Displaying <span id="pb-totalrecords" class="b"><?php echo $id+1; ?></span>&nbsp;<span id="pb-ptext">feeds </span></div>
				<div class="right"><label class="right ui-icon select_down" onclick="jQuery('#feeds_edit').toggleClass('maxhe290');jQuery(this).toggleClass('select_up');" title="<?php _e('Display all feeds', WPeMatico :: TEXTDOMAIN ); ?>"></label></div>
				
			</div>
			<div class="p7 hide">
				<div class="leftText">Displaying <span id="pb-currentrecordblock" class="b">1-5</span>&nbsp;of&nbsp;<span id="pb-totalrecords" class="b"><?php echo $id+1; ?></span>&nbsp;<span id="pb-ptext">feeds </span></div>
				<div class="middleText">Results per page:&nbsp;&nbsp;</div>
				<div class="dd"><select name="pb-resultsperpage" id="pb-resultsperpage" class="t12"><option selected="selected" value="5">5</option><option value="10">10</option><option value="20">20</option></select></div>
				<div class="rightText">
				  <div id="page-first" class="mya4_sprite begin_active_arrow icoPad arrow_opacity"></div>
				  <div id="page-prev" class="mya4_sprite prev_active_arrow icoPad arrow_opacity"></div>
				  <div class="icoPad" style="padding-top:2px;"><span id="pb-currentpage" class="b">1</span> of <span id="pb-totalpages" class="b">3</span></div>
				  <div id="page-next" class="mya4_sprite next_active_arrow icoPad"></div>
				  <div id="page-last" class="mya4_sprite end_active_arrow icoPad"></div>
				</div>
			  </div>
		  </div>
			
		</div>
		</div>
	<?php
	}
		
	//*************************************************************************************	
	function cron_box( $post ) {  
		global $post, $campaign_data;
		$cfg = get_option(WPeMatico :: OPTION_KEY);
		$activated = $campaign_data['activated'];
		$cron = $campaign_data['cron'];
		?>

		<input class="checkbox" value="1" type="checkbox" <?php checked($activated,true); ?> name="activated" /> <?php _e('Activate scheduling', 'wpematico'); ?><br />
		<?php if( $cfg['nonstatic'] ) { NoNStatic :: min1e($post, $cfg); }  ?>
		<div id="cronboxes">
			<?php list($cronstr['minutes'],$cronstr['hours'],$cronstr['mday'],$cronstr['mon'],$cronstr['wday'])=explode(' ',$cron,5);    ?>
			<div style="width:85px; float: left;">
				<b><?php _e('Minutes: ','wpematico'); ?></b><br />
				<?php 
				if (strstr($cronstr['minutes'],'*/'))
					$minutes=explode('/',$cronstr['minutes']);
				else
					$minutes=explode(',',$cronstr['minutes']);
				?>
				<select name="cronminutes[]" id="cronminutes" style="height:65px;" multiple="multiple">
				<option value="*"<?php selected(in_array('*',$minutes,true),true,true); ?>><?php _e('Any (*)','wpematico'); ?></option>
				<?php
				for ($i=0;$i<60;$i=$i+5) {
					echo "<option value=\"".$i."\"".selected(in_array("$i",$minutes,true),true,false).">".$i."</option>";
				}
				?>
				</select>
			</div>
			<div style="width:85px; float: left;">
				<b><?php _e('Hours:','wpematico'); ?></b><br />
				<?php 
				if (strstr($cronstr['hours'],'*/'))
					$hours=explode('/',$cronstr['hours']);
				else
					$hours=explode(',',$cronstr['hours']);
				?>
				<select name="cronhours[]" id="cronhours" style="height:65px;" multiple="multiple">
				<option value="*"<?php selected(in_array('*',$hours,true),true,true); ?>><?php _e('Any (*)','wpematico'); ?></option>
				<?php
				for ($i=0;$i<24;$i++) {
					echo "<option value=\"".$i."\"".selected(in_array("$i",$hours,true),true,false).">".$i."</option>";
				}
				?>
				</select>
			</div>
			<div style="width:85px; float: right;">
				<b><?php _e('Days:','wpematico'); ?></b><br />
				<?php 
				if (strstr($cronstr['mday'],'*/'))
					$mday=explode('/',$cronstr['mday']);
				else
					$mday=explode(',',$cronstr['mday']);
				?>
				<select name="cronmday[]" id="cronmday" style="height:65px;" multiple="multiple">
				<option value="*"<?php selected(in_array('*',$mday,true),true,true); ?>><?php _e('Any (*)','wpematico'); ?></option>
				<?php
				for ($i=1;$i<=31;$i++) {
					echo "<option value=\"".$i."\"".selected(in_array("$i",$mday,true),true,false).">".$i."</option>";
				}
				?>
				</select>
			</div>
			<br class="clear" />
			<div style="width:42%; float: left;">
				<b><?php _e('Months:','wpematico'); ?></b><br />
				<?php 
				if (strstr($cronstr['mon'],'*/'))
					$mon=explode('/',$cronstr['mon']);
				else
					$mon=explode(',',$cronstr['mon']);
				?>
				<select name="cronmon[]" id="cronmon" style="height:65px;" multiple="multiple">
				<option value="*"<?php selected(in_array('*',$mon,true),true,true); ?>><?php _e('Any (*)','wpematico'); ?></option>
				<option value="1"<?php selected(in_array('1',$mon,true),true,true); ?>><?php _e('January'); ?></option>
				<option value="2"<?php selected(in_array('2',$mon,true),true,true); ?>><?php _e('February'); ?></option>
				<option value="3"<?php selected(in_array('3',$mon,true),true,true); ?>><?php _e('March'); ?></option>
				<option value="4"<?php selected(in_array('4',$mon,true),true,true); ?>><?php _e('April'); ?></option>
				<option value="5"<?php selected(in_array('5',$mon,true),true,true); ?>><?php _e('May'); ?></option>
				<option value="6"<?php selected(in_array('6',$mon,true),true,true); ?>><?php _e('June'); ?></option>
				<option value="7"<?php selected(in_array('7',$mon,true),true,true); ?>><?php _e('July'); ?></option>
				<option value="8"<?php selected(in_array('8',$mon,true),true,true); ?>><?php _e('Augest'); ?></option>
				<option value="9"<?php selected(in_array('9',$mon,true),true,true); ?>><?php _e('September'); ?></option>
				<option value="10"<?php selected(in_array('10',$mon,true),true,true); ?>><?php _e('October'); ?></option>
				<option value="11"<?php selected(in_array('11',$mon,true),true,true); ?>><?php _e('November'); ?></option>
				<option value="12"<?php selected(in_array('12',$mon,true),true,true); ?>><?php _e('December'); ?></option>
				</select>
			</div>
			<div style="width:42%; float: right;">
				<b><?php _e('Weekday:','wpematico'); ?></b><br />
				<select name="cronwday[]" id="cronwday" style="height:65px;" multiple="multiple">
				<?php 
				if (strstr($cronstr['wday'],'*/'))
					$wday=explode('/',$cronstr['wday']);
				else
					$wday=explode(',',$cronstr['wday']);
				?>
				<option value="*"<?php selected(in_array('*',$wday,true),true,true); ?>><?php _e('Any (*)','wpematico'); ?></option>
				<option value="0"<?php selected(in_array('0',$wday,true),true,true); ?>><?php _e('Sunday'); ?></option>
				<option value="1"<?php selected(in_array('1',$wday,true),true,true); ?>><?php _e('Monday'); ?></option>
				<option value="2"<?php selected(in_array('2',$wday,true),true,true); ?>><?php _e('Tuesday'); ?></option>
				<option value="3"<?php selected(in_array('3',$wday,true),true,true); ?>><?php _e('Wednesday'); ?></option>
				<option value="4"<?php selected(in_array('4',$wday,true),true,true); ?>><?php _e('Thursday'); ?></option>
				<option value="5"<?php selected(in_array('5',$wday,true),true,true); ?>><?php _e('Friday'); ?></option>
				<option value="6"<?php selected(in_array('6',$wday,true),true,true); ?>><?php _e('Saturday'); ?></option>
				</select>
			</div>
			<br class="clear" />
		</div>
		<?php 
		_e('Working as <a href="http://wikipedia.org/wiki/Cron" target="_blank">Cron</a> job schedule:', WPeMatico :: TEXTDOMAIN ); echo ' <i>'.$cron.'</i><br />'; 
		_e('Next runtime:', WPeMatico :: TEXTDOMAIN ); echo ' '.date('D, j M Y H:i',WPeMatico :: time_cron_next($cron));
	}
	
	//********************************
	function log_box( $post ) {
		global $post, $campaign_data;
		
		$mailaddresslog = $campaign_data['mailaddresslog'];
		$mailerroronly = $campaign_data['mailerroronly'];
	?>
		<?php _e('E-Mail-Adress:', WPeMatico :: TEXTDOMAIN ); ?>
		<input name="mailaddresslog" id="mailaddresslog" type="text" value="<?php echo $mailaddresslog; ?>" class="large-text" /><br />
		<input class="checkbox" value="1" type="checkbox" <?php checked($mailerroronly,true); ?> name="mailerroronly" /> <?php _e('Send only E-Mail on errors.', WPeMatico :: TEXTDOMAIN ); ?>
	<?php
	}
	
	//********************************
	function cat_box( $post ) {
		global $post, $campaign_data;
		
		$campaign_categories = $campaign_data['campaign_categories'];
		if(!($campaign_categories)) $campaign_categories = array();
		?>
		<div class="inside" style="overflow-y: scroll; overflow-x: hidden; max-height: 250px;">
		<ul id="categories" style="font-size: 11px;">
		<?php self :: Categories_box($campaign_categories) ?>
		</ul> 
		</div>
		<div id="major-publishing-actions">
		<a href="JavaScript:void(0);" id="quick_add" onclick="arand=Math.floor(Math.random()*101);jQuery('#categories').append('&lt;li&gt;&lt;input type=&quot;checkbox&quot; name=&quot;campaign_newcat[]&quot; checked=&quot;checked&quot;&gt; &lt;input type=&quot;text&quot; id=&quot;campaign_newcatname'+arand+'&quot; class=&quot;input_text&quot; name=&quot;campaign_newcatname[]&quot;&gt;&lt;/li&gt;');jQuery('#campaign_newcatname'+arand).focus();" style="font-weight: bold; text-decoration: none;" ><?php _e('Quick add',  WPeMatico :: TEXTDOMAIN ); ?>.</a>
		</div>
	<?php
	}

	// ** Muestro Categorías seleccionables 
	private function _wpe_edit_cat_row($category, $level, &$data) {  
		$category = get_category( $category );
		$name = $category->cat_name;
		echo '
		<li style="margin-left:'.$level.'5px" class="jobtype-select checkbox">
		<input type="checkbox" value="' . $category->cat_ID . '" id="category_' . $category->cat_ID . '" name="campaign_categories[]" ';
		echo (in_array($category->cat_ID, $data )) ? 'checked="checked"' : '' ;
		echo '>
		<label for="category_' . $category->cat_ID . '">' . $name . '</label></li>';
	}

	private function Categories_box(&$data, $parent = 0, $level = 0, $categories = 0)  {    
		if ( !$categories )
			$categories = get_categories(array('hide_empty' => 0));

		if(function_exists('_get_category_hierarchy'))
		  $children = _get_category_hierarchy();
		elseif(function_exists('_get_term_hierarchy'))
		  $children = _get_term_hierarchy('category');
		else
		  $children = array();

		if ( $categories ) {
			ob_start();
			foreach ( $categories as $category ) {
				if ( $category->parent == $parent) {
					echo "\t" . self :: _wpe_edit_cat_row($category, $level, $data);
					if ( isset($children[$category->term_id]) )
						self :: Categories_box($data, $category->term_id, $level + 1, $categories );
				}
			}
			$output = ob_get_contents();
			ob_end_clean();

			echo $output;
		} else {
			return false;
		}
	}

	// Action handler - The 'Save' button is about to be drawn on the advanced edit screen.
	function post_submitbox_start()	{
		global $post, $campaign_data;
		if($post->post_type != 'wpematico') return $post->ID;
		
		$campaign_posttype = $campaign_data['campaign_posttype'];
		$campaign_customposttype = $campaign_data['campaign_customposttype'];
		wp_nonce_field( 'edit-campaign', 'wpematico_nonce' ); 
		?><div class="clear" style="margin: 0 0 15px 0;">
		<div class="postbox inside" style="min-width:30%;float:left; padding: 0pt 5px 16px 10px;">
			<p><b><?php _e('Status',  WPeMatico :: TEXTDOMAIN ); ?></b></p>
			<?php
				echo '<input class="radio" type="radio"'.checked('publish',$campaign_posttype,false).' name="campaign_posttype" value="publish" id="type_published" /> <label for="type_published">'.__('Published',  WPeMatico :: TEXTDOMAIN ).'</label><br />';
				echo '<input class="radio" type="radio"'.checked('private',$campaign_posttype,false).' name="campaign_posttype" value="private" id="type_private" /> <label for="type_private">'.__('Private',  WPeMatico :: TEXTDOMAIN ).'</label><br />';
				echo '<input class="radio" type="radio"'.checked('draft',$campaign_posttype,false).' name="campaign_posttype" value="draft" id="type_draft" /> <label for="type_draft">'.__('Draft',  WPeMatico :: TEXTDOMAIN ).'</label><br />';
			?>
		</div>
		<div class="postbox inside" style="float: right; min-width: 45%; padding: 0pt 5px 16px 10px;">
		<p><b><?php _e('Post type',  WPeMatico :: TEXTDOMAIN ); ?></b></p>
		<?php
			$args=array(
			  'public'   => true
			); 
			$output = 'names'; // names or objects, note names is the default
			$operator = 'and'; // 'and' or 'or'
			$post_types=get_post_types($args,$output,$operator); 
			foreach ($post_types  as $post_type ) {
				if ($post_type == 'wpematico') continue;
				echo '<input class="radio" type="radio" '.checked($post_type,$campaign_customposttype,false).' name="campaign_customposttype" value="'. $post_type. '" id="customtype_'. $post_type. '" /> <label for="customtype_'. $post_type. '">'. $post_type. '</label><br />';
			}
		?>
		</div></div>	<?php 
	}
	
}
?>