function slickr_flickr_validate_form(frm){
    var firstname = frm.elements["firstname"];
	if ((firstname.value==null)||(firstname.value=="")){
		alert("Please enter your First Name")
		firstname.focus();
		return false;
	}
    var email = frm.elements["email"];
	if ((email.value==null)||(email.value==""))
		alert("Please enter your Email Address")
    else {
        if (slickr_flickr_validate_email(email.value))
           return true;
	    else
	  	   alert('Please provide a valid email address');
        }
	email.focus();
	return false;
 }

function slickr_flickr_validate_email(emailaddress) {
    var filter = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
    return filter.test(emailaddress);
}

