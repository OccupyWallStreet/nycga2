This folder contains specific forms used throughout EM.

CAREFUL!! If you do override these files, make sure you keep form IDs, actions, filters and other php code of that nature. 
Doing this could prevent the form from working properly.

Any of these files in this folder can be individually overriden in your theme folder, making it upgrade safe without having to hack core plugin files directly.

To override a file, copy (not move) the file to wp-content/themes/yourtheme/plugins/events-manager/forms/ and edit as needed. 

If the file is within a subfolder of this directory, create that subdirectory in your theme path above and copy the file there.

Whilst we intend to keep changes to a minimum, it may be inevitable that we need to update these files to add new features or fix a 
reported bug, please keep this in mind when updating.