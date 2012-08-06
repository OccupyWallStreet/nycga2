=== CSS & JavaScript Toolbox ===
Contributors: wipeoutmedia
Author URL: http://wipeoutmedia.com/wordpress-plugins/css-javascript-toolbox/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VMXTA3838F6A8
Tags: html, css, javascript, code, custom, page, pages, post, posts, category, categories, url
Requires at least: 3.2
Tested up to: 3.3.1
Stable tag: 0.8

Easily add custom CSS and JavaScript code to individual Pages, Posts, Categories, and URLs.

== Description ==

Add custom reusable CSS & JavaScript to pages, posts, categories & URLs.
Full code template management system.
Insert, create, save, edit and delete code templates.
Add titles to code template and blocks.
Reuse code via code templates drop-down list.
Add code to header or footer.
Add extra code blocks as needed.
Default CSS/JS declarations.
Simple and easy UI.
Page/post nav icons for page preview.
Multilingual support.
Reorder, minimise & maximise blocks.
Ability to embedded WordPress/CJToolbox Javascript files by just checking them.
jQuery Cycle and jQuery Easing included.
Ability to Backup and Restore blocks data.

[Click for full instructions with screenshots, usage hints & tips, code examples, and user feedback](http://wipeoutmedia.com/wordpress-plugins/css-javascript-toolbox)

= After Install & Activation =
After you have installed and activated the CSS & JavaScript Toolbox plugin, you will notice a new menu item appear under Settings in your Dashboard. Click the 'CSS & JavaScript Toolbox' link in the main navigation (left side of your Dashboard).

= Default code example =
First time installers will see a large editor window with a JavaScript example, that simply adds an alert box to a page of your choice. You can try it out by clicking on a page or post checkbox. Clicking the navigation icon (rectangle with arrow in corner) will open the page in a new window.  When you are done exploring, simply delete the entire text content from the editor.

= Create a CSS & JavaScript Block title =
Block names are useful to keep your code blocks managed and organised. Please type a relevant name that describes what your block does, then click Save.

= Write the code, then choose what the code affects =
The large editor window is where you write your CSS or JavaScript code.  To allow your code to run on your website, you need to specify the pages, posts, categories, or page URLs you want the code to work with.  You can select multiple checkboxes and even make selections from the other tabs, just make sure you click the Save All Changes button when you are done. 

= Insert, create, save, edit, delete your code templates =
We have created a management system so you can perform these functions with your code templates.
- down arrow icon: insert code from the drop-down list into the editor window (at cursor)
- plus sign icon: popup appears so you can add a new code template, which will be added to the drop-down list
- pen icon: popup appears allowing you edit any existing code templates
- cross icon: confirmation popup appears before deleting the code template

= Adding more CSS & JavaScript Blocks = 
Look for a button labelled Add New CSS/JS Block.  Click this and a popup will appear asking you to name the code block.  Once the Save button is clicked, an entirely new CSS & JavaScript code block with the new name is now added.  Note: CSS & JavaScript Blocks with EMPTY code will not be saved!

= Shuffle, drag, open and close CSS & JavaScript Blocks =
If you hover your mouse cursor over the title bar of the code block, the cursor should change into a four-sided arrow.  This allows you to shuffle and drag the blocks around.  If you click on the title bar, the code block will open and close, which may come in handy if you have many code blocks.  

= Header and Footer switch =
This switch can be used to automatically add your code block to a header location or a footer location.

= Saving your work =
Although this plugin uses Ajax for the function calls (in other words does not refresh the webpage), please remember to save your code on a regular basis by clicking the Save All Changes button. 

= Thank You = 
We would like to take this opportunity to thank you for installing our plugin. :)
We hope you enjoy using our new WordPress plugin with your development work.  We do plan on developing it further with some really cool updates in the near future. If you enjoy this plugin and not only find it useful, but appreciate the huge amount of work that has gone into creating it, you can certainly support us in a number of ways, including: 
- a small donation via [our PayPal account](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VMXTA3838F6A8)
- provide a rating on Wordpress.org
- visit our [Wipeout Media](http://wipeoutmedia.com/wordpress-plugins/css-javascript-toolbox/) website and provide some user feedback or feature requests
- Like our [Facebook page](http://www.facebook.com/pages/Wipeout-Media/209420279099268) - we really need Likes :) 
- Follow us on [Twitter](http://twitter.com/wipeoutmedia) - we really need followers too :D

Thank you very much for your support and we hope you enjoy using it as much as we enjoyed creating it.

Regards,
Wipeout Media Team

== Installation ==

1. Upload the 'css-js-toolbox' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click 'CSS & JavaScript Toolbox' link in the main navigation (left side of your Dashboard).

== Frequently Asked Questions ==

= Can I move the blocks around? =
Yes by hovering your mouse cursor over the code block title bar until it turns into a four-sided arrow, this allow you to move the blocks.  Clicking the block title bar allows you to open and close the blocks.

= I'm using the URL List and my code is not working? =
Make sure you have copied and pasted the page, post, or category URL exactly as it appears in the address bar.  For example, you may have inadvertently included an extra forward slash at the end of your URL.

= Where did my CSS & JavaScript Block I created go? =
If you have added a new CSS/JS block, created a title and clicked the Save All Changes button, and you refreshed the page when your block did not contain any code, then when the page reloads, your new 'empty' block will disappear. You must have code inside the block for it to permanently save.

= Why use the Footer switch in Location/Hook? =
Hook location feature gives you control over the location of outputting the CSS/JS code. This is useful in case overriding another Plugin CSS is required. Also sometimes its better to put your JS code in the footer to avoid slowing down your page load.

= I received a weird error, what do I do now? =
Sometimes a bug decides to rear its ugly head and when this happens, this is when we need your help.  If you receive an error, if it be a PHP error, or some functionality that isn't working for whatever reason, please visit our: [Wipeout Media website and let us know](http://wipeoutmedia.com/wordpress-plugins/css-javascript-toolbox/)

== Screenshots ==

1. Main CSS & JavaScript Toolbox window showing the JS/CSS Block.
2. Three tab sections showing Pages (& Posts), Categories, and URL List respectively.
3. Popup window allowing you to edit the block name.
4. Confirmation message after the Save Changes button is pressed.
5. Popup window for writing and saving custom template CSS, JavaScript and jQuery code.
6. Custom template code is now added and saved to the drop-down list for future use.
7. With a press of a button, code is added to the block editor, and a page is assigned to use that code.
8. CSS code is now styling the assigned page. Can be used for Posts, Categories and URLs too.
9. Blocks can be ordered by click and dragging the block title bar. Single click minimises the block.
10. Embedded WordPress or scripts that shipped out with CJToolbox Plugin by just checking them.
11. Backup and Restore functions allow for backing up and restoring entire code blocks

== Changelog ==

= 0.8 =
* Modifying template code.
* Header and Footer hooks support: Select in which hook to output CSS/JS code.
* Blocks can be reordered.
* Blocks can be named.
* New icons and improved UI.
* Multilingual support: Only English translation is shipped with this version.
* Style overriding: Blocks order allow later blocks styles to override former blocks.
* Embedded Scripts: Embedded WordPress or Scripts that shipped out with CJToolbox Plugin by just checking them.
* Backup and Restore blocks data.
* Bug Fix: New blocks not toggling unless the page is refreshed.
* Bug Fix: CSS/JS template extra slashes problem.
* Bug Fix: Block deletion issues.
* Bug Fix: Code is not applied to the URL list except the last URL.
* Bug Fix: Cannot use string offset as array error.
* Bug Fix: Invalid argument supplied foreach() error.

= 0.3 =
* This is the first and latest release.

== Upgrade Notice ==

New features has been added and a few bugs fixed.

== Credits ==
	
Copyright (c) 2012, Wipeout Media.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
