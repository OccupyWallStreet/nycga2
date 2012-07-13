/* ---------------------------------
Simple:Press - Version 4.0
Private Messaging Extension Javascript

$LastChangedDate: 2010-02-11 15:30:54 +0000 (Thu, 11 Feb 2010) $
$Rev: 3471 $
------------------------------------ */

/* ----------------------------------
# Toggle a thread open and closed
-------------------------------------*/
function sfjtoggleThread(cCell, targetDiv, rowIndex)
{
	jQuery('#'+targetDiv).toggle('slow');
	var n1 = cCell.parentNode;
	var n2 = n1.parentNode;
	var n3 = n2.parentNode;
	var n4 = n3.parentNode;
	var n5 = n4.parentNode;
	var cRow = n5.parentNode;

	if(cRow.className == "sfpmshow")
	{
		cRow.className = "sfpmread";
	} else {
		cRow.className = "sfpmshow";
	}
}

/* ----------------------------------
Load PM message text in inbox
-------------------------------------*/
curId = 0;
function sfjgetPMText(imageFile, url, pmId, box, status)
{
	if (curId != pmId)
	{
    	var messageTarget = 'sfpmmsg';
    	var infoTarget = 'sfpminfo';
    	var buttonsTarget = 'sfpmbuttons';

    	var content = document.getElementById(messageTarget);
    	var info = document.getElementById(infoTarget);
    	var buttons = document.getElementById(buttonsTarget);

        if (curId != 0)
        {
        	var oldTarget = 'message-' + curId;
            var oldrow = document.getElementById(oldTarget);
            oldrow.className = 'sfpmread';
        }

    	var newTarget = 'message-' + pmId;
        var newrow = document.getElementById(newTarget);
        newrow.className = 'sfpmselected';

        curId = pmId;

		content.style.display = 'block';
		info.style.display = 'block';
		buttons.style.display = 'block';
        buttons.style.paddingTop = '5px';

		content.innerHTML = '<br /><br /><img src="' + imageFile + '" /><br />';

		buttonUrl = url + '&pmbuttons=' + pmId + '&pmaction=' + box;
		jQuery('#' + buttonsTarget).load(buttonUrl);

		infoUrl = url + '&pminfo=' + pmId + '&pmaction=' + box;
		jQuery('#' + infoTarget).load(infoUrl);

		messageUrl = url + '&pmshow=' + pmId + '&pmaction=' + box;
		jQuery('#' + messageTarget).load(messageUrl);

		/* can we reduce counter */
		if(status == '0')
		{
			var counter = document.getElementById('sfunreadpm');
			var pcount = parseInt(counter.innerHTML);
			if(isNaN(pcount) || pcount == 0)
			{
				pcount = 0;
			} else {
				pcount--;
			}
			counter.style.color = '#ffffff';
			counter.innerHTML = pcount;
		}
	}
}

/* ----------------------------------
Send PM to selected user
-------------------------------------*/
function sfjsendPMTo(recipient, name, title, reply, slug, editor)
{
	/* init some key fields */
	document.getElementById('pmtonamelist').innerHTML = '';
	document.getElementById('pmcount').value = 0;

	jQuery('#sfpostform').show(function() {
    	document.getElementById('sfpostform').scrollIntoView();

    	if (editor == 2 || editor == 3 || editor == 4)
    	{
    		document.addpm.postitem.value = '';
    		document.addpm.postitem.focus();
    	}
    	if (editor == 1)
    	{
    		if (jQuery.browser.mozilla || jQuery.browser.opera)
    		{
    			tinyMCE.activeEditor.selection.setContent('');
    			tinyMCE.activeEditor.focus();
    		}
    		if (jQuery.browser.safari || jQuery.browser.webkit)
    		{
    			tinyMCE.activeEditor.execCommand("InsertHTML", false, '');
    		}

    		if (jQuery.browser.msie)
    		{
    			tinyMCE.activeEditor.execCommand("mceInsertRawHTML", false, '');
    			tinyMCE.activeEditor.focus();
    		}
    	}
	});

    if (recipient)
	{
    	/* split names and ids */
    	namelist = name.split(",");
    	recipientlist = recipient.split(",");

    	/* add each recipient to the recipients */
	   for(x=recipientlist.length-1;x>=0;x--)
	   {
		  sfjpmaddrecipient(namelist[x], recipientlist[x]);
	   }
    }

	/* if provided, set the title, reply flag and slug */
	document.getElementById('pmtitle').value = title;
	document.getElementById('pmoldtitle').value = title;
	document.getElementById('pmreply').value = reply;
	document.getElementById('pmslug').value = slug;

	/* display the compose form */
	if(document.getElementById('sfpostform').style.display != "block")
	{
		sfjtoggleLayer('sfpostform');
	}
	return false;
}

/* ----------------------------------
Send PM to all users
-------------------------------------*/
function sfjpmallusers()
{
	var uid = document.getElementById('uid').value;

	/* remove any current recipients */
	var rList = '';
	for(i=uid-1;i>0;i--)
	{
		var user = document.getElementById('userid' + i);
		if (user != null)
		{
			sfjpmremoveuser(user);
		}
	}

	/* init some key fields */
	document.getElementById('pmtonamelist').innerHTML = '';
	document.getElementById('pmcount').value = 1;

    /* add bogus label for all users */
	sfjpmaddrecipient('All Users', -1);
	document.getElementById('pmall').value = 1;

    /* remove add buddy button */
	var addbuddy = document.getElementById('addbuddy');
	addbuddy.style.display="none";

	return false;
}

/* ----------------------------------
Send PM to selected user - quoted
-------------------------------------*/
function sfjquotePM(recipient, pmid, intro, editor, name, title, reply, slug)
{
	var postcontent = document.getElementById('sfpmmsg').innerHTML;

	jQuery('#sfpostform').show(function() {
		/* all browsers */
		document.getElementById('sfpostform').scrollIntoView();

    	if (editor == 2 || editor == 4)
    	{
    		document.addpm.postitem.value = '<strong>'+intro+'</strong>\r\r'+'<blockquote>'+postcontent+'</blockquote><br />\r\r';
    		document.addpm.postitem.focus();
    	}
    	if (editor == 3)
    	{
    		document.addpm.postitem.value = '[b]'+intro+'[/b]\r\r[quote]'+postcontent+'[/quote]\r\r';
    		document.addpm.postitem.focus();
    	}
    	if (editor == 1)
    	{
    		if (jQuery.browser.mozilla || jQuery.browser.opera)
    		{
    			tinyMCE.activeEditor.selection.setContent('<strong>'+intro+'</strong><blockquote>'+postcontent+'</blockquote><br />');
    			tinyMCE.activeEditor.focus();
    		}
    		if (jQuery.browser.safari || jQuery.browser.webkit)
    		{
    			tinyMCE.activeEditor.execCommand("InsertHTML", false, '<strong>'+intro+'</strong><blockquote>'+postcontent+'</blockquote><br />');
    		}

    		if (jQuery.browser.msie)
    		{
    			tinyMCE.activeEditor.execCommand("mceInsertRawHTML", false, '<strong>'+intro+'</strong><blockquote>'+postcontent+'</blockquote><br />');
    			tinyMCE.activeEditor.focus();
    		}
    	}
	});

    /* address the email */
    sfjsendPMTo(recipient, name, title, reply, slug);
}

/* ----------------------------------
Delete a complete PM thread
-------------------------------------*/
function sfjdeleteThread(cCell, url, rowIndex, targetDiv)
{
	var pmTable = document.getElementById('sfmainpmtable');
	var target = pmTable.rows[rowIndex];

	var n1 = cCell.parentNode;
	var cRow = n1.parentNode;

	if(cRow.className == "sfpmshow")
	{
		sfjtoggleLayer(targetDiv);
	}

	if (navigator.appName == "Microsoft Internet Explorer")
	{
		sfjopacity(target.style,9,0,10,function(){sfjremoveIt(target);});
	} else {
		sfjopacity(target.style,199,0,10,function(){sfjhideIt(target);});
	}

	jQuery('#sfdummy').load(url);
}

/* ----------------------------------
Delete a PM
-------------------------------------*/
function sfjdeletePM(cCell, messageUrl, threadUrl, rowIndex, threadRowIndex, threadDiv, threadSlug)
{
	var pmTable = document.getElementById('sfmessagetable-'+threadSlug);
	var target = pmTable.rows[rowIndex];

	msgDiv = document.getElementById('sfpm'+threadSlug);

	if (navigator.appName == "Microsoft Internet Explorer")
	{
		sfjopacity(target.style,9,0,10,function(){sfjremoveIt(target);});
	} else {
		sfjopacity(target.style,199,0,10,function(){sfjhideIt(target);});
	}
	/* try reducing the thread count */
	var threadCount = document.getElementById('pm-' + threadSlug + 'count');

	var pcount = parseInt(threadCount.innerHTML);
	if(isNaN(pcount))
	{
		return;
	} else {
		pcount--;
		threadCount.innerHTML = pcount;
	}

	if(pcount == 0)
	{
		/* delete thread */
		var threadCell = document.getElementById('pm-' + threadSlug+'delthread');
		sfjdeleteThread(threadCell, threadUrl, threadRowIndex, threadDiv);
	} else {
		/* delete message */
		jQuery('#sfdummy').load(messageUrl);
	}
}

/* ----------------------------------
Delete complete box
-------------------------------------*/
function sfjdeleteMassPM(url)
{
	var pmTable = document.getElementById('sfmainpmtable');

	if (navigator.appName == "Microsoft Internet Explorer")
	{
		sfjopacity(pmTable.style,9,0,10,function(){sfjremoveIt(pmTable);});
	} else {
		sfjopacity(pmTable.style,199,0,10,function(){sfjhideIt(pmTable);});
	}

	jQuery('#sfdummy').load(url);
}

/* ----------------------------------
mark pm unread
-------------------------------------*/
function sfjmarkUnread(url, id, text)
{
    /* make sure we havent already unmarked this guy */
    if (curId != 0)
    {
        /* flash up the marked as read icon */
    	var msg = document.getElementById('sfpmmsg-'+id);
        msg.style.visibility = "";
        msg.style.display = "block";
        msg.className = 'sfpmunread sfxcontrol';
    	msg.innerHTML = text;
    	if (navigator.appName == "Microsoft Internet Explorer")
    	{
    		sfjopacity(msg.style,9,0,10,function(){sfjremoveIt(msg);});
    	} else {
    		sfjopacity(msg.style,199,0,10,function(){sfjhideIt(msg);});
    	}

        /* change msg background to unread */
    	var oldTarget = 'message-' + id;
        var oldrow = document.getElementById(oldTarget);
        oldrow.className = 'sfpmunread';
        curId = 0;

    	jQuery('#sfdummy').load(url);

    	/* increase inbox counter */
    	var counter = document.getElementById('sfunreadpm');
    	var pcount = parseInt(counter.innerHTML);
    	if (isNaN(pcount))
    	{
    		pcount = 1;
    	} else {
    		pcount++;
    	}
    	counter.style.color = '#ffffff';
    	counter.innerHTML = pcount;
    }
}

/* ----------------------------------
Add all recipients to buddy list
-------------------------------------*/
function sfjpmaddbuddies()
{
	/* get user list and ahah routine */
	var uid = document.getElementById('uid').value;
	var url = document.getElementById('pmsite').value;

	/* create a '-' seperated list of recipients */
	var rList = '';
	for(i=uid-1;i>0;i--)
	{
		var user = document.getElementById('userid' + i);
		if (user != null)
		{
			if (rList == '')
			{
				rList = user.value;
			} else {
				rList = rList + '-' + user.value;
			}
		}
	}

	/* make sure there are really folks to add */
	if((rList == null) || (rList == '0'))
	{
		return;
	}

	/* call the ahah routine to add the new buddies */
	url += '&addbuddy='+rList;
	jQuery('#pmbuddies').load(url);
}

/* ----------------------------------
Add single recipient to buddy list
-------------------------------------*/
function sfjpmnewbuddy(uid)
{
	/* grab the user to add to buddy list */
	var url = document.getElementById('pmsite').value;
	var user = document.getElementById('userid' + uid);

	/* call the ahah routine to add the single user */
	rList = user.value;
	url += '&addbuddy='+rList;
	jQuery('#pmbuddies').load(url);
}

/* ----------------------------------
Add recipient from buddy list
-------------------------------------*/

function sfjpmaddbuddy(source)
{
	/* grab the buddy information */
	var source = document.getElementById(source);
	var uid = document.getElementById('uid').value;

	/* make sure the buddy isnt already in the recipient list */
	var found = false;
	for(i=uid-1;i>0;i--)
	{
		var user = document.getElementById('userid' + i);
		if (user != null && user.value == source.value)
		{
			found = true;
			break;
		}
	}

	/* if buddy isnt already in the list, add buddy to the recipient list */
	if(!found)
	{
		var thisOption = new Option(source.options[source.selectedIndex].text, source.value, true, true);
		sfjpmaddrecipient(thisOption.text, source.value);
	}

	return false;
}

/* ----------------------------------
Add recipient from users list
-------------------------------------*/
function sfjpmadduser(li)
{
	/* get current recipient list */
	var uid = document.getElementById('uid').value;

	/* check is user to add is already in the recipient list */
	var found = false;
	for(i=uid-1;i>0;i--)
	{
		var user = document.getElementById('userid' + i);
		if (user != null && user.value == li.extra)
		{
			found = true;
			break;
		}
	}

	/* if buddy isnt already in the list, add buddy to the recipient list */
	if(!found)
	{
		sfjpmaddrecipient(li.selectValue, li.extra);
	}

	/* clear the input box where names are typed to reset for next users */
	var tolist = document.getElementById('pmusers');
	tolist.value = '';

	return false;
}

/* ----------------------------------
Helper function to populate recipient list
-------------------------------------*/
function sfjpmaddrecipient(rid, ruid)
{
	/* get some hidden input elements from the form as data to this js routine */
	var uid = document.getElementById('uid').value;
	var img1 = document.getElementById('pmdelimage').value;
	var img2 = document.getElementById('pmaddimage').value;
	var img1msg = document.getElementById('pmdelmsg').value;
	var img2msg = document.getElementById('pmaddmsg').value;
	var cc = document.getElementById('pmcc').value;
	var bcc = document.getElementById('pmbcc').value;
	var max = document.getElementById('pmmax').value;
	var count = parseInt(document.getElementById('pmcount').value);
	var limited = document.getElementById('pmlimited').value;
	var pmall = document.getElementById('pmall').value;

	/* if max recipient count is not reached, add the user */
	if ((max == 0 || count < max) && pmall == 0)
	{
		var images = "<a href='#' onclick='sfjpmremoveuser(\"#row" + uid + "\"); return false;'> <img src='" + img1 + "' title='" + img1msg + "' /></a>";
        if (ruid != -1)
        {
            var images = images + "<a href='#' onclick='sfjpmnewbuddy(" + uid + "); return false;'>&nbsp;&nbsp;<img src='" + img2 + "' title='" + img2msg + "' /></a>";
        }
		if (limited)
		{
			images = '';
		}
		cctext = bcctext = '';
		if (cc == 1) cctext = "<option value='2'>Cc:</option>";
		if (bcc == 1) bcctext = "<option value='3'>Bcc:</option>";
		jQuery("#pmtonamelist").append("<div id='row" + uid + "' style='text-align:center'><select class='sfcontrol' size='1' name='type[]' id='type" + uid + "'><option value='1' default='default'>To:</option>" + cctext + bcctext + "</select> <input class='sfcontrol' type='text' size='30' name='user[]' id='user" + uid + "' value='" + rid +"'><input type='hidden' name='userid[]' id='userid" + uid + "' value='" + ruid +"'>" + images + "</div>");
		uid = (uid - 1) + 2;
		document.getElementById('uid').value = uid;
		document.getElementById('pmcount').value = (count + 1);
	} else {
	    if (pmall == 0)
        {
    		/* display error message if max recipients already reached */
    		var target = document.getElementById('sfpmexceed');
    		target.innerHTML = document.getElementById('pmmaxmsg').value;
    		var box = hs.htmlExpand(document.getElementById('pmmaxmsg'), {contentId: 'pm-tolist', preserveContent: true});
        }
	}
}

/* ----------------------------------
helper function to remove recipient from list
-------------------------------------*/
function sfjpmremoveuser(ruid)
{
	/* remove the desired recipient from the list */
	jQuery(ruid).remove();

	/* decrement the recipient count by one since we removed a user */
	var count = parseInt(document.getElementById('pmcount').value);
	document.getElementById('pmcount').value = (count - 1);

    /* cant be pm all any more so clear */
	document.getElementById('pmall').value = 0;
}