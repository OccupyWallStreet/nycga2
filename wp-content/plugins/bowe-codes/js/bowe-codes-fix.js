jQuery(document).ready(function($){
	$('.user-infos').each(function(){
		bc_fix_height($(this));
	});
	$('.group-infos').each(function(){
		bc_fix_height($(this));
	});
	$('.blog-infos').each(function(){
		bc_fix_height($(this));
	});
	$('.post-infos').each(function(){
		bc_fix_height($(this));
	});
	$('.forum-infos').each(function(){
		bc_fix_height($(this));
	});
	$('.message-infos').each(function(){
		bc_fix_height($(this));
	});
	$('.notification-infos').each(function(){
		bc_fix_height($(this));
	});
});

function bc_fix_height(theclass){
	fixedHeight = Number(parseInt(theclass.parent().find('.bc_avatar').css('height'))+10);
	fixedWidth = Number(parseInt(theclass.parent().find('.bc_avatar').css('width'))+20)
	if(parseInt(theclass.css('height')) < parseInt(fixedHeight)){
		theclass.css('height', fixedHeight+'px');
	}
	theclass.css('margin-left', fixedWidth+'px');
}