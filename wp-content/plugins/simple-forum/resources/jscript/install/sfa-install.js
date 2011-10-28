/* Simple:Press Version 4.4 Install/Upgrade */

var messageStrings;
var installProgress;

/* ======================================== */
/*											*/
/*				SPF INSTALLER				*/
/*			   Simple:Press 4.4				*/
/*											*/
/* ======================================== */

function sfjPerformInstall(phpUrl, phaseCount, currentPhase, subPhaseCount, currentSubPhase, image, messages)
{
	try {
		var phaseTotal = (parseInt(phaseCount) + parseInt(subPhaseCount));

		/* If first time in - load up message strings and initialise progress */
		if(currentPhase == 0)
		{
			var installtext = new String(messages);
			messageStrings = installtext.split("@");

			/* display installing message and set up progress bar */
			jQuery('#imagezone').html('<p><br /><img src="' + image + '" /><br />' + messageStrings[1] + '<br /></p>');
			jQuery('#imagezone').fadeIn('slow');
			jQuery("#progressbar").progressbar({ value: 0 });
			installProgress = 0;
		} else {
			installProgress++;
		}

		/* update progress bar */
		var currentProgress  = ((installProgress / phaseTotal) * 100);
		jQuery("#progressbar").progressbar('option', 'value', currentProgress);

		/* do next phase/build section */
		var thisUrl = phpUrl + '&phase=' + currentPhase;
		var target = "#zone" + currentPhase;
		if(currentPhase == 8 && currentSubPhase < (subPhaseCount+1))
		{
			thisUrl = thisUrl + '&subphase=' + currentSubPhase;
		}

		jQuery(target).load(thisUrl, function(a, b) {
			/* check for errors first */
			var retVal = a.substr(0,13);

			jQuery(target).fadeIn('slow');

			if(retVal == 'Install Error')
			{
				jQuery('#imagezone').html('<p>' + messageStrings[3] + '</p>');
				return;
			}

			if(currentPhase == 8)
			{
				currentSubPhase++;
				if(currentSubPhase > subPhaseCount)  { currentPhase++; }
			} else {
				currentPhase++;
			}

			/* are we finished yet */
			if(currentPhase > phaseCount)
			{
				jQuery("#progressbar").progressbar('option', 'value', 100);
				jQuery('#finishzone').html('<p>' + sfjEndInstall(messageStrings[0]) + '</p>');
				jQuery('#imagezone').html('<p>' + messageStrings[2] + '</p>');
				return;
			} else {
				sfjPerformInstall(phpUrl, phaseCount, currentPhase, subPhaseCount, currentSubPhase, image, messages)
			}
		});
	}

	catch(e) {
//		jQuery("#imagezone").html('<p>PROBLEM - The Install can not be completed</p>');
//		var abortMsg = "<p>There is a problem with the JavaScript being loaded on this page which is stopping the install from being completed.<br />";
//		abortMsg += "The error being reported is: " + e.message + '</p>';
//		jQuery("#errorzone").html(abortMsg);
//		jQuery("#errorzone").show();

		var iZone = document.getElementFromId('imagezone');
		var eZone = document.getElementFromId('errorzone');
		iZone.innerHTML = '<p>PROBLEM - The Install can not be completed</p>';
		var abortMsg = "<p>There is a problem with the JavaScript being loaded on this page which is stopping the upgrade from being completed.<br />";
		abortMsg += "The error being reported is: " + e.message + '</p>';
		eZone.innerHTML = abortMsg;
		iZone.style.display="block";
		eZone.style.display="block";
	}
}

/* ======================================== */
/*											*/
/*				SPF UPGRADER				*/
/*			   Simple:Press 4.4				*/
/*											*/
/* ======================================== */

function sfjPerformUpgrade(phpUrl, startBuild, endBuild, currentBuild, image, messages)
{
	try {
		var currentProgress = 0;
		var buildSpan = (endBuild - startBuild);

		/* If first time in - load up message strings and initialise progress */
		if(messageStrings == null)
		{
			var installtext = new String(messages);
			messageStrings = installtext.split("@");

			/* display upgrading message and progressbar */
			jQuery('#imagezone').html('<p><br /><img src="' + image + '" /><br />' + messageStrings[1] + '<br /></p>');
			jQuery('#imagezone').fadeIn('slow');
			jQuery("#progressbar").progressbar({ value: 0 });
		} else {
			/* calculate progress so far */
			cValue = (buildSpan - (endBuild - currentBuild));
			var currentProgress  = ((cValue / buildSpan) * 100);
		}

		/* update progress bar */
		jQuery("#progressbar").progressbar('option', 'value', currentProgress);

		/* do next phase/build section */
		var thisUrl = phpUrl + '&start=' + currentBuild;
		jQuery('#errorzone').load(thisUrl, function(a, b) {

			/*  print debug info in case (hidden) */
			var h = jQuery('#debug').html();
			jQuery('#debug').html(h + '<p>' + a + ' - ' + b + '</p>');

			/* are we finished yet */
			if(a == endBuild)
			{
				jQuery('#finishzone').html('<p>' + sfjEndUpgrade(messageStrings[0]) + '</p>');
				jQuery('#imagezone').html('<p>' + messageStrings[2] + '</p>');
				jQuery("#progressbar").progressbar('option', 'value', 100);
				return;
			} else {
				/* did it error out */
				if(a.length > 6)
				{
					jQuery('#errorzone').html('<p>' + messageStrings[3] + '<br />' + currentBuild + '<br />' + a + '<br />' + b + '</p>');
					jQuery('#errorzone').fadeIn('slow');
					jQuery('#debug').fadeIn('slow');
					return;
				} else {
					/* once more around the loop for next build section */
					sfjPerformUpgrade(phpUrl, startBuild, endBuild, a, image, messages);
				}
			}
		});
	}

	catch(e) {
//		jQuery("#imagezone").html('<p>PROBLEM - The Upgrade can not be completed</p>');
//		var abortMsg = "<p>There is a problem with the JavaScript being loaded on this page which is stopping the upgrade from being completed.<br />";
//		abortMsg += "The error being reported is: " + e.message + '</p>';
//		jQuery("#errorzone").html(abortMsg);
//		jQuery("#errorzone").show();

		var iZone = document.getElementFromId('imagezone');
		var eZone = document.getElementFromId('errorzone');
		iZone.innerHTML = '<p>PROBLEM - The Upgrade can not be completed</p>';
		var abortMsg = "<p>There is a problem with the JavaScript being loaded on this page which is stopping the upgrade from being completed.<br />";
		abortMsg += "The error being reported is: " + e.message + '</p>';
		eZone.innerHTML = abortMsg;
		iZone.style.display="block";
		eZone.style.display="block";
	}
}

function sfjEndInstall(messagetext)
{
	return '<form name="sfinstalldone" method="post" action="admin.php?page=simple-forum/admin/panel-config/sfa-config.php"><br /><input type="submit" class="button-secondary" name="goforuminstall" value="' + messagetext + '" /></form>';
}

function sfjEndUpgrade(messagetext)
{
	return '<form name="sfinstalldone" method="post" action="admin.php?page=simple-forum/admin/panel-config/sfa-config.php"><br /><input type="submit" class="button-secondary" name="goforumupgrade" value="' + messagetext + '" /></form>';
}
