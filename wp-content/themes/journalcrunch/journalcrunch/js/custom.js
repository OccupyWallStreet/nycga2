//DROPDOWN MENU INIT
ddsmoothmenu.init({
mainmenuid: "topMenu", //menu DIV id
orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
classname: 'ddsmoothmenu', //class added to menu's outer DIV
//customtheme: ["#1c5a80", "#18374a"],
contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
})



/* Twitter ToolTip */

function twitter_tooltip(target_items, name){
 $(target_items).each(function(){
		//$("body").append("<div class='"+name+"' id='"+name+i+"'><p>"+$(this).attr('title')+"</p></div>");
		var my_tooltip = $("."+name);
		$(this).removeAttr("title").mouseover(function(){
				my_tooltip.css({opacity:0.9, display:"none"}).fadeIn(400);
		}).mousemove(function(kmouse){
				my_tooltip.css({left:kmouse.pageX-290, top:kmouse.pageY-45});
		}).mouseout(function(){
				my_tooltip.fadeOut(400);
		});
	});
}
$(document).ready(function(){
						   
twitter_tooltip("a.tip","tooltip");
						 
// PRETTY PHOTO INIT
$("a[rel^='prettyPhoto']").prettyPhoto();						 
							
	 
// POST BOXES HOVER BEHAVIOUR

$('#content .postBoxInner').hover(function(){
	$(this).toggleClass('postBoxInnerHover');
	}); 
$('.postBox a.readMore').hover(function(){
	$(this).prev('.postBoxInner').toggleClass('postBoxInnerHover');
	}); 

// AJAX CONTACT FORM INIT

 $('#contact').ajaxForm(function(data) {
		 if (data==1){
			 $('#success').fadeIn("slow");
			 $('#bademail').fadeOut("slow");
			 $('#badserver').fadeOut("slow");
			 $('#contact').resetForm();
			 }
		 else if (data==2){
				 $('#badserver').fadeIn("slow");
			  }
		 else if (data==3)
			{
			 $('#bademail').fadeIn("slow");
			}
			});

});