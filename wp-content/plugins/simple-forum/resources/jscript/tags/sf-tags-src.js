jQuery(document).ready(function() {
	/* Yahoo API */
	jQuery("a.yahoo_api").click(function() {
		jQuery('#sftagsloading').show();
		jQuery("#suggestedtags .container_clicktags").load( sfSettings.url + '/index.php?sf_ahah=tag-suggest&sfaction=tags_from_yahoo', {content:getContentFromEditor(),title:jQuery("#topictitle").val(),tags:jQuery("#tags-input").val()}, function(){
			registerClickTags();
		});
		return false;
	});

	/* Tag The Net API */
	jQuery("a.ttn_api").click(function() {
		jQuery('#sftagsloading').show();
		jQuery("#suggestedtags .container_clicktags").load( sfSettings.url + '/index.php?sf_ahah=tag-suggest&sfaction=tags_from_tagthenet', {content:getContentFromEditor(),title:jQuery("#topictitle").val()}, function(){
			registerClickTags();
		});
		return false;
	});

	/* Local Tags Database */
	jQuery("a.local_db").click(function() {
		jQuery('#sftagsloading').show();
		jQuery("#suggestedtags .container_clicktags").load( sfSettings.url + '/index.php?sf_ahah=tag-suggest&sfaction=tags_from_local_db', {content:getContentFromEditor(),title:jQuery("#topictitle").val()}, function(){
			registerClickTags();
		});
		return false;
	});
});

function getContentFromEditor()
{
	var data = '';
	if ((typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()) /* Tiny MCE editor */
	{
		var ed = tinyMCE.activeEditor;
		if ('mce_fullscreen' == ed.id)
		{
			tinyMCE.get('postitem').setContent(ed.getContent({format : 'raw'}), {format : 'raw'});
		}
		tinyMCE.get('postitem').save();
		data = jQuery("#postitem").val();
	} else {   /* bbcode, html and textarea editors */
		data = jQuery("#postitem").val();
	}

	/* Trim data */
	data = data.replace(/^\s+/, '' ).replace( /\s+$/, '');
	if (data != '')
	{
		data = strip_tags(data);
	}

	return data;
}

function registerClickTags() {
	jQuery("#suggestedtags .container_clicktags span").click(function() {
		addTag(this.innerHTML);
	});

	jQuery('#sftagsloading').hide();
	if (jQuery('#suggestedtags .inside').css('display') != 'block')
	{
		jQuery('#suggestedtags').toggleClass('closed');
	}
}

function strip_tags(str) {
   return str.replace(/&lt;\/?[^&gt;]+&gt;/gi, "");
}

function addTag(tag) {
	/* Trim tag */
	tag = tag.replace(/^\s+/, '' ).replace( /\s+$/, '');

	var newtags = jQuery('#tags-input').val();
	var tagexp = new RegExp('\\b'+tag+'\\b','i');
	if (!tagexp.test(newtags))
	{
		newtags += ',' + tag;
	}

	/* massage */
	newtags = newtags.replace(/\s+,+\s*/g, ',').replace(/,+/g, ',').replace(/,+\s+,+/g, ',').replace(/,+\s*$/g, '').replace(/^\s*,+/g, '');
	jQuery('#tags-input').val( newtags );
}
