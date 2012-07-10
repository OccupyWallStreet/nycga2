=== bbPress Attachments ===
Tags:  attachments, attachment, attach, uploads, upload, files, aws, s3, _ck_
Contributors: _ck_
Requires at least: 0.9
Tested up to: 0.9
Stable tag: trunk
Donate link: http://bbshowcase.org/donate/

Gives members the ability to upload attachments on their posts. This is an early beta release for review. Feedback, bug reports, feature ideas, improvements are encouraged. Please note there are important security considerations when allowing uploads of any kind to your server.

== Description ==

Gives members the ability to upload attachments on their posts. 
This is an early beta release for review. 
Feedback, bug reports, feature ideas, improvements are encouraged. 
Please note there are important security considerations when allowing uploads of any kind to your server.

== Installation ==

* make a directory `/bb-attachments/`  ABOVE your webroot ie.`/home/username/bb-attachments/` 
* `chmod 777` the above `/bb-attachments/` directory
* (skip this step if your bbPress is 0.9.0.2 or newer) edit `edit-post.php` and below `<?php edit_form(); ?>` put `<?php if (function_exists('bb_attachments')) {bb_attachments();} ?>`
* install plugin in it's own bb-attachments directory in `my-plugins` then activate plugin 
* there are some optional settings you can adjust in `bb-attachments.php`

== Frequently Asked Questions ==

= General =
* demo: http://bbshowcase.org/forums/topic/new-bbpress-plugin-bbpress-attachments
* members's ability to upload attachments is tied to their ability to edit post - ie. if edit ends in 1 hour, so does adding attachments
* the plugin will try to create the base upload directory itself, but in most cases will fail so you need to follow the first installation step
* if available, posix is used to write files with owner's id so you can delete/move files manually via FTP
* needs PHP >= 4.3
* filesize max might be 2mb because of passthrough/readfile limit (supposedly fixed in newer PHP)
* administrators can debug settings (ie. PHP upload limit) by adding to url `?bb_attachments_diagnostic`
* if you get `error: denied mime`  on every upload, mime_content_type function or shell access must exist to verify mime types - otherwise you can force all types to be allowed by editing `bb-attachments.php` and adding `'application/octet-stream'` to each of the `$bb_attachments['allowed']['mime_types']` 

= Amazon AWS S3 Simple Storage Service =
* Starting with version 0.2.0 bb-attachments now supports Amazon S3 service 
- this feature is sponsored by weddingbee.com who donated towards it and allows me to give out the code for free so be sure to thank them
* this feature requires fsockopen and fwrite support which most hosts should allow 
   but check your phpinfo if you know your host locks out some features or runs in "safe mode"
* Register at http://amazon.com/s3/ and enter your key and secret code into the bb-attachments settings
https://aws-portal.amazon.com/gp/aws/developer/account/index.html#AccessKey
* Files are first uploaded and stored on your own server as normal for a mirrored backup (S3 goes down occasionally)
* Users then will be automagically routed to the S3 url instead of your local URL
* You can setup a CNAME to make it appear as files are actually on your own server
http://docs.amazonwebservices.com/AmazonS3/2006-03-01/VirtualHosting.html#VirtualHostingCustomURLs
* If you have been using bb-attachments without S3, you must manually copy the existing files on your server to S3 via one of the many S3 utilities available - I will eventually make some sync routines but it might be awhile
* If S3 goes down or you decide not to use them anymore, you can simply turn off the option and it automagically will return to using your local files
* If an attachment is deleted, it is not remotely deleted off S3 at this time - one GB of "deleted" files only costs you 15 cents per month

== License ==

* CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/

== Donate ==

* http://bbshowcase.org/donate/

== Changelog ==

* 0.0.5	first public beta release for review
* 0.0.6	advanced restrictions by file type & user role, upload form displays allowed file types
* 0.0.7	one more mime option for windows/no-shell-access users
* 0.0.9	no longer necessary to edit `edit-post.php` template if using bbPress 0.9.0.2 or newer
* 0.1.0	uploading now possible directly on new posts (instead of only attaching afterwards)
* 0.1.1	role check before hooks, ability to disable upload on new posts if compatibility issues
* 0.1.5 first attempt at inline image display ability
* 0.1.6 BBcode replacement for inserted inline attachment
* 0.1.7 bug squashed for IIS ?
* 0.1.8 workaround for lack of apache_request_headers, javascript insert bug
* 0.1.9 handle missing inline images a bit better with "x" icon
* 0.1.10 bug fix for bbPress activation hook on Windows servers
* 0.1.11 now can auto-insert images into post after upload (default)
* 0.1.12 filter switch to get_post_text and thumbnail bug fix (props BarnRacoon)
* 0.1.13 better detection and cleanup for enctype=multipart hack
* 0.1.14 now does a recount for topic after attachment deleted to possibly remove paperclip icon
* 0.1.15 better recount for when posts are deleted/undeleted
* 0.2.0  initial support for Amazon AWS S3 Simple Storage Service 
* 0.2.1	a few S3 bug fixes and speedups
* 0.2.2	add encoding to RSS feeds, props nathany
* 0.2.3	changed database table to UTF-8 instead of latin for proper filename support
* 0.2.4	security fix for when delete level set to participate - user can only delete attachment if they can edit post
* 0.2.5 multipart fix for bbPress 1.0a6 switch to bb_get_uri
* 0.2.6 bug fix for handling encoding during feeds (encode self, not entire post text)
* 0.2.7 allow custom table name for multiple installs in same db, 
	don't use temp area for testing - move to main area and destroy if bad, fixes open_basedir and some safe mode problems
	
== To Do ==

* map mime types to match extensions?
* check for file duplicates before saving
* thumbnails for image attachments
* pre-validate upload filenames via javascript to spare user upload time with rejection
* admin menu
