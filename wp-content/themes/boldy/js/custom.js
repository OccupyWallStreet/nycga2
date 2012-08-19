$(document).ready(function(){

//DROPDOWN MENU INIT
ddsmoothmenu.init({
mainmenuid: "mainMenu", //menu DIV id
orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
classname: 'ddsmoothmenu', //class added to menu's outer DIV
//customtheme: ["#1c5a80", "#18374a"],
contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
})

// PRETTY PHOTO INIT
$("a[rel^='prettyPhoto']").prettyPhoto();

// SHOW/HIDE FOOTER ACTIONS
$('#showHide').click(function(){	
	if ($("#footerActions").is(":hidden")) {
		$(this).css('background-position','0 0');
		$("#footerActions").slideDown("slow");
		
		} else {
		$(this).css('background-position','0 -16px') 
		$("#footerActions").hide();
		$("#footerActions").slideUp("slow");
		}
	return false;			   
});		

// TOP SEARCH 
$('#s').focus(function() {
		$(this).animate({width: "215"}, 300 );	
		$(this).val('')
});

$('#s').blur(function() {
		$(this).animate({width: "100"}, 300 );
});

// QUICK CONTACT

$('#quickName').val('your name');
$('#quickEmail').val('your email');
$('#quickComment').val('your message');

$('#quickName').focus(function() {
		$(this).val('');	
});

$('#quickEmail').focus(function() {
		$(this).val('');	
});

$('#quickComment').focus(function() {
		$(this).val('');	
});

//SHARE LINKS
$('#shareLinks a.share').click(function() {
		if ($("#shareLinks #icons").is(":hidden")) {
		$('#shareLinks').animate({width: "625"}, 500 );
		$('#shareLinks #icons').fadeIn();
		$(this).text('[-] Share & Bookmark');
		return false;
		}else {
		$('#shareLinks').animate({width: "130"}, 500 );
		$('#shareLinks #icons').fadeOut();
		$(this).text('[+] Share & Bookmark');
		return false;	
		}
});

});
<!-- end document ready -->

<!-- PrettyPhoto init -->

