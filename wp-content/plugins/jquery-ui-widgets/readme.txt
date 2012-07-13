=== jQuery UI Widgets ===
Contributors: dgwyer
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MTVAN3NBV3HCA
Tags: jquery, tabs, accordion, dialog, ui, admin, enqueue, themeroller, styles, themes
Requires at least: 2.7
Tested up to: 3.4
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Simple, clean, and flexible way to add jQuery UI widgets to your site pages. Works right out of the box!

== Description ==

Easily add jQuery UI widgets to any post, page, or sidebar! All standard pre-defined jQuery themes are supported, or you can upload your own custom theme built with the <a href="http://jqueryui.com/themeroller/" target="_blank">jQuery ThemeRoller</a>. See the FAQ page for detailed instructions on uploading your own custom theme.

No need to mess about with cryptic shortcodes! Just enter clean, valid, HTML markup and the Plugin does the rest, adding all the necessary jQuery scripts and styles for you!

For example, once activated, the Plugin can show tabs in any post, page, or sidebar with just the following code:

    <div class="tabs">
      <ul>
        <li><a href="#tabs-1">Tab 1</a></li>
        <li><a href="#tabs-2">Tab 2</a></li>
        <li><a href="#tabs-3">Tab 3</a></li>
      </ul>
      <div id="tabs-1">
        <p>This is tab one.</p>
      </div>
      <div id="tabs-2">
        <p>This is tab two.</p>
      </div>
      <div id="tabs-3">
        <p>This is tab one.</p>
      </div>
    </div>

The jQuery UI widgets currently supported are: Tabs, Accordion, Dialog, Button, Datepicker, and Slider.

ALL 25 standard jQuery UI themes are supported. Simply select the theme you want from the drop down box in Plugin settings, or upload your own custom theme to blend in with your existing WordPress theme.

Please rate this Plugin if you find it useful. Thanks. :)

See our <a href="http://www.presscoders.com" target="_blank">WordPress development site</a> for more Plugins and themes.

== Frequently Asked Questions ==

**I am not really sure what HTML code I need to add for each jQuery UI widget. Can you give me some examples?**

There are plenty of examples for each jQuery widget on the official <a href="http://jqueryui.com/demos/" target="_blank">demo and documentation pages</a> which include example code you can analyse and use on your own pages.

**How do you upload a custom theme using the official jQuery ThemeRoller?**

The Plugin supports themes created with the <a href="http://jqueryui.com/themeroller/" target="_blank">jQuery interactive ThemeRoller</a> which means you can very easily create a custom jQuery theme to match your WordPress theme perfectly.

1. Create your custom theme using the ThemeRoller online tool. When you have finished your masterpiece, click the 'Download theme' button.
2. On the 'Build Your Download' page make sure ALL check boxes are seletected (this is important).
3. Click the 'Advanced Theme Settings' button on the right hand side of the page and a couple of text boxes will come into view. Add a name for your custom theme in the 'Theme Folder Name' text box. Make sure that any words are separated by a dashes NOT spaces.
4. Finally, before downloading your custom theme, make sure the version radio button selected is 1.8.xx rather than 1.7.3.
5. Now, you can click 'Download' to download your custom theme to your computer. It will be saved as a zip file.
6. Locate your downloaded custom theme and extract the zip file.
7. Open up the extracted custom theme folder, and you'll see three folders: 'css', 'development-bundle', and 'js'. The one we are interested in is 'css'.
8. Open the 'css' folder and inside will be a single folder containing your theme, which has the name you specified above. Inside this folder will be an images folder and a single stylesheet. Make a note of the stylesheet name, you will need it later on.
9. You need to now upload this custom theme folder to your site, so the Plugin can read the custom styles.
10. If you take a look at the Plugin settings page you will see the folder name that you should upload to. Usually this will be something like http://www.mysite.com/wp-content/uploads/. If you are running a WordPress multisite this will be something different. Using FTP upload your custom jQuery theme to this folder.
11. Finally, you just need to paste in the name of your custom theme stylsheet in Plugin settings, in the textbox provided.
12. So, if your custom theme folder is named 'mytheme' and your custom stylesheet is called 'jquery-ui-1.8.20.custom.css' then you would need to paste in something like 'mytheme/jquery-ui-1.8.20.custom.css' into the textbox.
13. If you wanted to try out multiple custom themes I would recommend adding a 'jquery-ui-themes' folder (or whatever name you wanted) and uploading all of your custom themes to this folder.
14. The path to your stylesheet would then become 'jquery-ui-themes/mytheme/jquery-ui-1.8.20.custom.css'.
15. If for whatever the reason the Plugin cannot find your stylesheet file it will display a warning message on the Plugin settings page.

**I have an issue with how the jQuery UI widgets are rendering with my WordPress theme. Can you help fix it?**

This isn't a Plugin issue. Any problems with how the jQuery CSS interacts with your current WordPress theme will need to be fixed by using tools such as Firebug, or by contacting the theme author to help tweak the CSS. I'm afraid I don't have the resources to help with CSS queries for specific themes.

**The custom jQuery UI init code I added doesn't seem to be working. What could the problem be?**

Again, this isn't a Plugin issue. If you are using custom init code for some jQuery widgets and it isn't working then please use Firebug to detect the issue. I would also recommend posting on the jQuery forums if you are stuck.

== Installation ==

Instructions for installing:

1. In your WordPress admin go to Plugins -> Add New.
2. Enter jQuery UI Widgets in the text box and click Search Plugins.
3. In the list of Plugins click Install Now next to the jQuery UI Widgets Plugin.
4. Once installed click to activate.
5. Visit the Plugin options page via Settings -> jQuery UI Widgets.

Usage:

1. Go to Plugin settings and select the theme that you want to use to render the jQuery UI elements.
2. Select the check boxes for the widgets you want to be available on your site pages.
3. Default initialization code is automatically added to your site pages for each active widget. So, for example the init code added for the tabs widget is: $( ".tabs" ).tabs(). This is fine in most cases but if you want to override this, or add support for different tab selectors, then there is a text box for each widget to add custom init code.
4. Quite often, depending on the WordPress theme you are using (and the specific jQuery selectors), you may run in minor issues with the CSS rendering. To help with this, there is a text box that you can enter custom style rules to tweak the jQuery theme styles, so they match your theme perfectly. By default, there is a selection of custom styles already added to help make any jQuery theme more compatible with your WordPress theme right out of the box! Edit these custom style rules as necessary to match your WordPress theme.
5. To upload a custom jQuery theme created with the ThemeRoller, see the FAQ for specific details.

== Screenshots ==

1. Plugin settings.
2. jQuery UI theme example.
3. jQuery UI theme example.
4. jQuery UI theme example.
5. jQuery UI theme example.

== Changelog ==

*0.1*

* Initial release.

== Upgrade Notice ==

= 0.1 =
Initial release.