/* ----------------------------------*/
/* general redirect					 */
/* ----------------------------------*/

function sfjreDirect(url)
{
	window.location = url;
}

/* ----------------------------------*/
/* Basically changing re-direct      */
/* ----------------------------------*/

function sfjsetCredentials(baseURL, forumURL)
{
	var lForm = document.forms[0];
	switch(lForm.name)
	{
		case 'loginform':
			lForm.action = baseURL + "wp-login.php?action=login&view=forum";
			break;

		case 'registerform':
			lForm.action = baseURL + "wp-login.php?action=register&view=forum";
			break;

		case 'lostpasswordform':
			lForm.action = baseURL + "wp-login.php?action=lostpassword&view=forum";
			break;
	}

	/* redirect on login form after a registration */
	var reDirect = document.getElementsByName("redirect_to");
	if(reDirect != null)
	{
		reDirect[0].value = forumURL;
	}
}

