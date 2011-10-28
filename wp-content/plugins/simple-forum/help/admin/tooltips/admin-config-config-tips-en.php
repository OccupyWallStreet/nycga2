<?php

global $tooltips;

$tooltips=array();

$tooltips['styles']=
"The <b>Styles</b> folder contains all of the forum skins and icons as well as, initially, default smileys and avatars. This folder
contains a large number of files and is a good candidate for relocation.<br />
This is especially the case if you have, or plan, to make customisation to a skin or create your own icons.
<br /><br />
For the benefits of relocating folders, please click on the help button in the top right of this form.";

$tooltips['avatars']=
"The <b>Avatars</b> folder contains both the default three avatars and all avatars uploaded by your members. If you choose to not
use the forum avatar options then you may ignore this folder.<br /><br />
If this folder failed to get created during the installation (due to permission settings) then you need to create it manually. Please
follow these instructions:
<br />
[1] Create the new folder within the WordPress '<b>wp-content</b>' folder. You may give this folder any name you choose or create a sub-folder
path. The default name is '<b>forum-avatars</b>'.<br />
[2] Move or copy the three supplied default avatars into this new folder. They are supplied in the '<b>/styles/avatars/</b>' folder but
can not be used from that location.<br />
[3] Make sure that your new folder has the correct permissions. If you are allowing your members to upload their avatars
this will need to be '777'.<br />
[4] Finally - if you changed the name of the avatars folder - enter the path into thos configuration form and update it.";

$tooltips['avatar-pool']=
"The <b>Avatar Pool</b> Folder is the location for storing a pool of images uploaded by the forum admin from which his users can select an
avatar to use. Use of the Avatar Pool will depend, of course, of the general avatar settings made in the Profile > Avatars panel.";

$tooltips['smileys']=
"The <b>Smileys</b> folder contains both the default supplied smileys and all smileys you upload and add to the forum. If you choose to not
use the forum smiley options then you may ignore this folder.<br /><br />
If this folder failed to get created during the installation (due to permission settings) then you need to create it manually. Please
follow these instructions:
<br />
[1] Create the new folder within the WordPress '<b>wp-content</b>' folder. You may give this folder any name you choose or create a sub-folder
path. The default name is '<b>forum-smileys</b>'.<br />
[2] Move or copy the supplied default smileys into this new folder. They are supplied in the '<b>/styles/smileys/</b>' folder but
can not be used from that location.<br />
[3] Make sure that your new folder has the correct permissions. If you are likely to upload additional smileys
this will need to be '777'.<br />
[4] Finally - if you changed the name of the smileys folder - enter the path into thos configuration form and update it.";

$tooltips['ranks']=
"The <b>Forum Badges</b> folder contains any custom images that you want to use for forum ranks.  If you are not using forum ranks or
do not want images (ie badges) with the forum ranks, then you do not need to worry about this configuration path.<br /><br />
This folder needs to be manually created with permissions of '777' and the path entered here.";

$tooltips['image-uploads']=
"If you are electing to allow your members to <b>upload images</b> to your server (using the TinyMCE editor) for use in the forum, it is necessary
to create a base folder for the storage. Note that the system is capable of creating individual sub-folders for your members if you so choose.
<br /><br />
If this folder did not get created during the installation then you will need to do so manually and will need to set the permissions to '777'.
The default name for this folder is '<b>forum-image-uploads</b>' but you can name it to suit. You might also condsider creating a single 'base' folder
and creating the image and other upload folders as a sub-folder of that.<br /><br />
When the folder is in place - ensure that it is correctly entered into this configuration form and updated.<br /><br />
Note: <b>Image Upload will be available to any member granted the correct permission. It is available when using the TinyMCE editor
from the 'Image' toolbar button</b>.";

$tooltips['media-uploads']=
"If you are electing to allow your members to <b>upload media</b> to your server (using the TinyMCE editor) for use in the forum, it is necessary
to create a base folder for the storage. Note that the system is capable of creating individual sub-folders for your members if you so choose.
<br /><br />
If this folder did not get created during the installation then you will need to do so manually and will need to set the permissions to '777'.
The default name for this folder is '<b>forum-media-uploads</b>' but you can name it to suit. You might also condsider creating a single 'base' folder
and creating the media and other upload folders as a sub-folder of that.<br /><br />
When the folder is in place - ensure that it is correctly entered into this configuration form and updated.<br /><br />
Note: <b>Media Upload will be available to any member granted the correct permission. It is available when using the TinyMCE editor
from the 'Media' toolbar button</b>.";

$tooltips['file-uploads']=
"If you are electing to allow your members to <b>upload files</b> to your server (using the TinyMCE editor) for use in the forum, it is necessary
to create a base folder for the storage. Note that the system is capable of creating individual sub-folders for your members if you so choose.
<br /><br />
If this folder did not get created during the installation then you will need to do so manually and will need to set the permissions to '777'.
The default name for this folder is '<b>forum-file-uploads</b>' but you can name it to suit. You might also condsider creating a single 'base' folder
and creating the file and other upload folders as a sub-folder of that.<br /><br />
When the folder is in place - ensure that it is correctly entered into this configuration form and updated.<br /><br />
Note: <b>File Upload will be available to any member granted the correct permission. It is available when using the TinyMCE editor
from the 'Links' toolbar button</b>.";

$tooltips['custom-icons']=
"The <b>Custom icons</b> folder is a general storage area for any custom icons used by the forum. These can be replacement icons for Groups and Forums
or the three custom locations set aside for custom icons available in the Conpoents > Custom Icons panel.";

$tooltips['policies']=
"The <b>Policy Documents Folder</b> can optionally contain <b>plain text files</b> (which can include HTML tags) describing forum policy.<br />
If used, two documents can be defined directly used by Simple:Press. These are:
<br /><br />
&nbsp;&nbsp;<b>User Registration Policy</b> document<br />
&nbsp;&nbsp;<b>Site Privacy</b> document<br />
<br />
Note that in many countries, a statement of Privacy Policy is a legal requirement.";

$tooltips['hooks']=
"The <b>Program Hooks file</b> allows a user to add their own custom code into selected points within the forum display. This, single file, can be relocated
if so desired. The location currently assigned in this form is where it will normally be looked for by the forum.<br /><br />
For more detailed help on using Program Hooks, please refer to the
<a target='_blank' href='http://wiki.simple-press.com'><b>Simple:Press Wiki</b></a>.
<br /><br />
For the benefits of relocating folders, please click on the help button in the top right of this form.";

$tooltips['pluggable']=
"The <b>Pluggable Functions file</b> allows a user to create replacement functions for most forum rendering code. This, single file, can be relocated
if so desired. The location currently assigned in this form is where it will normally be looked for by the forum.<br /><br />
For more detailed help on using Pluggable Functions, please refer to the
<a target='_blank' href='http://wiki.simple-press.com'><b>Simple:Press Wiki</b></a>.
<br /><br />
For the benefits of relocating folders, please click on the help button in the top right of this form.";

$tooltips['filters']=
"The <b>Custom Filters file</b> allows a user to create custom filters for applying to forum post display/content. This, single file, can be relocated
if so desired. The location currently assigned in this form is where it will normally be looked for by the forum.<br /><br />
For more detailed help on using Custom Filter Functions, please refer to the
<a target='_blank' href='http://wiki.simple-press.com'><b>Simple:Press Wiki</b></a>.
<br /><br />
For the benefits of relocating folders, please click on the help button in the top right of this form.";

$tooltips['help']=
"The <b>Admin Help folder</b> contains all of the help files for the forums administration pages. You are using this help system now to read this.
The popup help consists of a large nunber of files worthy to be considered for relocation. The location currently assigned in this form is where
help files will normally be looked for by the forum.
<br /><br />
For the benefits of relocating folders, please click on the help button in the top right of this form.";

?>