=== Auto ThickBox Plus ===
Contributors: attosoft
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=D2DLJNSUFBU4U
Tags: lightbox, thickbox, shadowbox, gallery, semiologic, image, images, thumbnail, thumbnails, popup, pop-up, overlay, photo, photos, picture, pictures, javascript, simple, inline, iframe, ajax, jquery, plugin, plugins, link, links, widget, widgets, nextgen, nextgen gallery
Requires at least: 2.7
Tested up to: 3.4-RC3
Stable tag: trunk

Overlays linked image, inline, iFrame and AJAX content on the page in simple & fast effects. (improved version of Auto Thickbox plugin)

== Description ==

Auto ThickBox Plus plugin is the improved version of [Auto Thickbox](http://wordpress.org/extend/plugins/auto-thickbox/) plugin, with some extra features and bug fixes.

By clicking on links, this plugin overlays linked content on the page in simple & fast effects. It's recommended if you want to pop up thumbnails easily in its original size.

= Basic Features =

* Automatically applies [ThickBox script](http://thickbox.net/) to thumbnails including WordPress Galleries
  * All you do is upload images to WordPress Gallery or write image links to images (`<a href="image"><img /></a>`)
* Pop-up effects are simple & fast compared to Lightbox, ColorBox, FancyBox, Shadowbox, Slimbox and so on
  * ThickBox will be the answer if you prefer no animation effects & no fancy design
* Automatically resizes images that are larger than the browser window
* Uses WordPress built-in ThickBox library (no need to install the script and refer to it)

= Extra Features =

* Overlays images in either "Gallery Images" or "**Single Image**" style
* Automatically applies ThickBox to **text links** to images (`<a href="image">text</a>`)
* **Auto Resize** feature can be disabled if you prefer
* ThickBox window can be **moved/resized by dragging** mouse
* Can be customized the behavior & design through **more than 60 options**
  * e.g. Click action can be selected from 'Close', 'None', 'Next' and 'Prev/Next'
* Supports also BMP and [WebP](http://code.google.com/speed/webp/) image formats
* Supports **Inline content** on the page (`#TB_inline`)
* Supports **AJAX content** (displays internal files on the page without iframe)
* Compatible widely down to even **obsolete WordPress 2.7**
* Compatible with default theme in WordPress 3.2/3.3 called **Twenty Eleven**
* Compatible with cache plugins such as [W3 Total Cache](http://wordpress.org/extend/plugins/w3-total-cache/) and [Head Cleaner](http://wordpress.org/extend/plugins/head-cleaner/)
* Improved ThickBox is also available in another ThickBox plugins such as [NextGEN Gallery](http://wordpress.org/extend/plugins/nextgen-gallery/)
* Uses WordPress translations
  * Now ThickBox window is localized to **more than 70 languages** (Arabic, Chinese, Dutch, French, German, Hindi, Italy, Japanese, Korean, Polish, Portuguese, Russian, Spanish and more)
* And fixed a lot of bugs in Auto Thickbox plugin and built-in ThickBox (See [Changelog](changelog/))

= How to Install =

See [Installation](installation/).

= How to Use =

See [Usage in Other Notes](other_notes/#Usage).

= Support Me =

* To keep my motivation, put rating stars and vote compatibility (works/broken) via the right sidebar
* If you have any questions, view [support forum](http://wordpress.org/support/plugin/auto-thickbox-plus) or post a new topic
* See [how to localize](other_notes/#Localization) if you can translate the plugin into your language
* I would be grateful if you would [donate to support plugin development](http://attosoft.info/en/donate/)
* [Contact me](http://attosoft.info/en/contact/) if you have any feedback

Any comments will be very helpful and appreciated. Thank you for your support!

= Special Thanks =

* Dutch (nl_NL) translation - [Michel Bats](http://www.batssoft.nl/)
* French (fr_FR) translation - [Jean-Bastien Prévots](http://www.jbprevots.fr/)
* Button images in screenshot - [Lukas Häusler](http://lukashausler.com/)
* Bug fixed code - Olivier Burgard

= Links =

* [attosoft.info](http://attosoft.info/en/) \[[日本語](http://attosoft.info/)\]
* [Auto ThickBox Plus Official Site](http://attosoft.info/en/blog/auto-thickbox-plus/) \[[日本語](http://attosoft.info/blog/auto-thickbox-plus/)\]
* [Auto Thickbox](http://www.semiologic.com/software/auto-thickbox/) (Original)
* [ThickBox 3.1](http://thickbox.net/) (JavaScript Library)

== Installation ==

= Auto Install =

1. Access Dashboard screen in WordPress
1. Select [Plugins] - [Add New]
1. Input "thickbox" into text field, and click [Search Plugins]
1. Click 'Install Now' at 'Auto ThickBox Plus'
1. Click 'Activate Plugin'
1. Upload images to WordPress Gallery or write links to images, inline, iFrame or AJAX contents

= Manual Install =

1. Download [auto-thickbox-plus.zip](http://downloads.wordpress.org/plugin/auto-thickbox-plus.zip)
1. Access Dashboard screen in WordPress
1. Select [Plugins] - [Add New] - 'Upload' tab
1. Upload the plugin zip file, and click [Install Now]
1. Click 'Activate Plugin'
1. Upload images to WordPress Gallery or write links to images, inline, iFrame or AJAX contents

= Manual Install via FTP =

1. Download [auto-thickbox-plus.zip](http://downloads.wordpress.org/plugin/auto-thickbox-plus.zip), and unzip it
1. Upload the plugin folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Upload images to WordPress Gallery or write links to images, inline, iFrame or AJAX contents

\* Note: If Auto Thickbox (not Plus) plugin is installed, you need to deactivate or uninstall it before activating Auto ThickBox Plus plugin. You cannot activate both this plugin and the original plugin at the same time.

= Customization =
This is available options at Auto ThickBox Plus Options in 'Settings' menu. You can customize the behavior & design of the plugin through these options. See also [Screenshots](../screenshots/).

* General
  * Display Style
  * Auto ThickBox
  * No ThickBox
  * Auto Resize
  * ThickBox Resources
* Action
  * Mouse Click
  * Mouse Wheel (Scroll)
  * Drag & Drop
  * Keyboard Shortcuts
* View
  * Position
  * Font Family & Weight
  * Font Size
  * Text Color
  * Background Color
  * Margin
  * Border
  * Border Raidus
  * Opacity
  * Box Shadow
  * Text Shadow
* Text
  * Title, Caption
* Image
  * Prev/Next, Close, Loading image, etc.
* Effect (beta)
  * Open, Close, Transition, Title/Caption effects and Speed

== Frequently Asked Questions ==

= Auto ThickBox Plus does not work =

Most likely, some sort of JavaScript error has occurred in your blog. Please make sure that you can see any messages (errors/warnings) in [browser console](../other_notes/#Error-Console-in-Browsers).

\* If you can send me your blog URL that the plugin does not work, it's easy for me to debug the problem and find the cause quickly.

If JavaScript errors have occurred, ThickBox may not work even when loaded properly. If you use cache plugins such as [W3 Total Cache](http://wordpress.org/extend/plugins/w3-total-cache/) and [Head Cleaner](http://wordpress.org/extend/plugins/head-cleaner/), try to deactivate them first.

If no errors have occurred, ThickBox resources may not have been loaded properly. Please check the followings.

**1. Can you see the HTML source codes bellow in your blog?**

Inside the `<head>` tag:

    <link rel='stylesheet' id='thickbox-css' href='http://example.com/wp-content/plugins/auto-thickbox-plus/thickbox.min.css?ver=1.x' type='text/css' media='all' />

Inside the `<head>` tag or before the closing `</body>` tag:

    <script type='text/javascript' src='http://example.com/wp-content/plugins/auto-thickbox-plus/thickbox.min.js?ver=1.x'></script>

If you cannot see these tags, you may need to modify theme files because some WordPress themes do not output proper header/footer.

And if you see duplicate `<link href="thickbox.css" />` or `<script src="thickbox.js">` tags, you may need to modify theme files or other plugins to avoid confict with this plugin because some WordPress themes and plugins output these tags in a wrong way.

For instance, [Events Calendar](http://wordpress.org/extend/plugins/events-calendar/) plugin outputs `<link href="thickbox.css" />` tag directly without calling WordPress function. As a result, improved thickbox.css has been overwritten with built-in thickbox.css, then ThickBox window layout has broken.

**2. Can you see `class="thickbox"` attribute in `<a>` tag?**

This plugin sets `class="thickbox"` attribute to links to images automatically. So `<a>` tags like below must have `class="thickbox"` attribute.

    <a href="image.png" class="thickbox">
        <img src="image_s.png" alt="foo" />
    </a>
    
    <a href="image.png" class="thickbox" title="foo">Text</a>

**3. Try to use built-in ThickBox resources**

Access 'Options' page, and check [ThickBox Resources - Use WordPress built-in thickbox.js/css] option. If ThickBox will work, there will be issues in Auto ThickBox Plus.

= Options are not effective =

If option values are saved, I guess the problem is caused by WordPress theme. Please make sure that ThickBox custom script and style rules (i.e. `<script>`/`<style>` tag) are output in page footer. If they are not output, you may need to modify theme files.

\* If you use [Head JS](http://headjs.com/), select [General - ThickBox Resources - Footer] option.

= Differences between Auto Thickbox and Auto ThickBox Plus =

Auto ThickBox Plus plugin is the improved version of [Auto Thickbox](http://wordpress.org/extend/plugins/auto-thickbox/) plugin, with some extra features and bug fixes.

See "Basic/Extra Features" in [Description](../) for more information. "Basic Features" are also available in Auto Thickbox, but "Extra Features" are available only in Auto ThickBox Plus.

\* Note: Auto ThickBox Plus plugin is besed on Auto Thickbox v.2.0.3 (Jul 20th, 2011).

= Can I use the improved ThickBox in NextGEN Gallery? =

Yes, the improved ThickBox included in this plugins is also available in another ThickBox plugins such as [NextGEN Gallery](http://wordpress.org/extend/plugins/nextgen-gallery/). 

1. Install Auto ThickBox Plus and NextGEN Gallery, and activate them
2. Access [Dashboard] - [Gallery] - [Options] - [Effects] tab
3. Select "Thickbox" at [JavaScript Thumbnail effect] option
4. Write [NextGEN Gallery tags](http://wordpress.org/extend/plugins/nextgen-gallery/faq/) on your post/page (e.g. `[nggallery id=1]`)

Most plugins supported for ThickBox such as NextGEN Gallery use **built-in ThickBox** in WordPress. Auto ThickBox Plus can replace built-in ThickBox with **the improved version** with some extra features and bug fixes. See also "Extra Features" in [Description](../).

= How to access Auto ThickBox Plus Options =

1. Access Dashboard screen in WordPress
1. Click [Settings] - [Auto ThickBox Plus] in sidebar

= About [Display Style] option =

When [Gallery Images] is selected, this plugin displays all images on a page as one gallery. You can switch the current image on ThickBox window by clicking "Prev/Next" links without closing ThickBox window.

When [Single Image] is selected, this plugin displays images on a page one by one. You cannot switch the current image on ThickBox window. If you want to display some images as a gallery, you need to set `rel="gallery-id"` attribute to `<a>` tag manually as below.

    <a href="image1.png" rel="gallery-id-foo">
        <img src="image1_s.png" alt="image1" />
    </a>
    <a href="image2.png" rel="gallery-id-foo">
        <img src="image2_s.png" alt="image2" />
    </a>

= About [Auto ThickBox] option =

When [Text links to images] is checked, this plugin applies ThickBox to text links to images automatically.

When [Text links to images] is not checked, this plugin does not apply ThickBox to text links. If you want to apply ThickBox to text links, you need to set `class="thickbox"` attribute to `<a>` tag manually as below.

    <a href="image.png" class="thickbox" title="foo">Text</a>

= How to display only an image =

When "View" options are as follows, only an image will be displayed without margin, padding, border, caption and button.

* Position - Caption - None
* Margin - Image - 0 (px)
* Border - Window, Image (Top left / Bottom right) - None

= How to upload images via Media Uploader =

1. Click [Select a File] button at "Image" options
1. Drag an image file from your computer and drop to "Drop files here"
  * or Click [Select Files] button and choose an image file from your computer
1. Click [Insert Image] button

= Can I use built-in ThickBox resources instead? =

Yes, you can. Access 'Options' page, and check [ThickBox Resources - Use WordPress built-in thickbox.js/css] option.

\* Note: some extra features will be disabled. For instance, most mouse/keyboard actions, animation effects, disabled Auto Resize, compatibility with cache plugins. And many improvements and bug fixes won't be applied.

= How to insert line feeds in title/caption =

ThickBox Plus supports Multiline (line breaks) in title/caption. LF (`&#10&#xFEFF;;`/`&#x0A&#xFEFF;;`), CR (`&#13&#xFEFF;;`/`&#x0D&#xFEFF;;`) and CRLF (`&#13&#xFEFF;;&#10&#xFEFF;;`/`&#x0D&#xFEFF;;&#x0A&#xFEFF;;`) codes in caption/title are replaced with `<br />`.

    <a href="image.png" title="line1&#10&#xFEFF;;line2&#10&#xFEFF;;line3">Text</a>

= How to localize the plugin into your language =

You can localize the plugin with [Poedit](http://www.poedit.net/). All you do is open "languages/auto-thickbox.pot" file and translate it. See [Localization in Other Notes](../other_notes/#Localization) for details.

== Screenshots ==

1. Pop-up image in "Single Image" style
1. Pop-up image in "Gallery Images" style (with "Prev/Next" links)
1. Auto ThickBox Plus Options page
1. Customization example (pink background, transparent window, rounded corners, no borders, custom images, bold font, etc.)

== Changelog ==

= Latest Version =
* NEW: Enabled "Screen Options" menu to show/hide meta boxes in Options page
* CHANGED: Changed "Gallery - Caption" item to "Image - Caption" item in "Text - Caption" option. Supports also image caption that is not in WordPress Gallery.
* FIXED: Conflict with [iFeature](http://wordpress.org/extend/themes/ifeature) theme
* FIXED: Removes whitespaces from both ends of gallery caption (regression in v1.5)
* UPDATED: French translation, props JB Prévots

= 1.6 =
* NEW: "General - Display Style - Set a different gallery-id for each WordPress Gallery" option. You can use "Gallery Images" style in WordPress Gallery even when using "Single Image" style.
* NEW: "General - Auto ThickBox" otpion
  * "Text links to images" (replaces "ThickBox on Text Links" option)
  * "Links with target attribute" - `class="thickbox"` is not added to links with `target` attribute by default
* NEW: "General - No ThickBox" option. You can specify class names that ThickBox is not applied. (e.g. `nothickbox`)
* NEW: "View - Border - WordPress Gallery" option
* NEW: "About" meta box in Options page
* NEW: Adds `class="thickbox"` to links automatically when URL contains "TB_iframe" or "#TB_inline". You can omit `class="thickbox"` in iframe/ajax content.
* CHANGED: Separated "View - Border" options into width, style and color options
* CHANGED: Improved Media Uploader called from "Select a File" buttons in "Image" option
  * Uploaded images are now collected and shown in "Gallery" tab
  * Replaced "Insert into Post" button with "Insert Image" button
* CHANGED: Supports "TB_iframe" parameter without value. ThickBox does not check a value of "TB_iframe" parameter in fact. ThickBox opens as iframe content even when "TB_iframe=false".
* FIXED: tb_show() function had lost the compatibility with original ThickBox (regression in v1.5)
* FIXED: Title bar does not have enough height in empty title (regression in v1.5)
* FIXED: Scroll bar is hidden in newer Google Chrome when opening external URL as iframe content with Zoom/Fade effect
* FIXED: At "Effect - Speed" option, text filed has lost focus (caret) in Firefox when clicking it
* UPDATED: Added **French (fr_FR) translation (props Jean-Bastien Prévots)** and updated Japanese translation
* UPDATED: readme.txt (Supprt Me, FAQ, [Localization](../other_notes/#Localization), etc.)

= 1.5 =
* NEW: "General - ThickBox Resources - Header/Footer" option. You can place ThickBox script in header/footer. For instance, select "Footer" at the option when you use [Head JS](http://headjs.com/).
* NEW: "Effect - Title/Caption - Zoom/Slide/Fade/None" option. Title/caption can be showed/hidden by hovering the mouse over ThickBox window.
  * Added "Title/Caption - Hide initially" option
* NEW: Multiline (line breaks) are supported in caption/title. LF (`&#10&#xFEFF;;`/`&#x0A&#xFEFF;;`), CR (`&#13&#xFEFF;;`/`&#x0D&#xFEFF;;`) and CRLF (`&#13&#xFEFF;;&#10&#xFEFF;;`/`&#x0D&#xFEFF;;&#x0A&#xFEFF;;`) codes in caption/title are replaced with `<br />`.
  * e.g. `<a href="image.png" title="line1&#10&#xFEFF;;line2&#10&#xFEFF;;line3">`
* NEW: ThickBox accepts `width`/`height`/`modal` parameters in image content also
  * e.g. `<a href="image.png?width=100&height=100&modal=true">`
* CHANGED: Supports a gallery that contains images with same URL. ThickBox had regarded images with same URL as one image. Now ThickBox checks images with an object instead of its URL.
* CHANGED: Some improvements on "Mouse Click" action in a gallery that contains only one image
* CHANGED: In iframe/ajax content, uses close keys set in options instead of fixed ESC key
* FIXED: In Firefox, an image in a gallery that contains only one image cannot be opened under certain condition
* FIXED: In iframe/ajax content, close key (ESC) cannot be disabled with `modal` parameter (ThickBox bug)
* FIXED: anchor-utils filter does not detect an empty anchor tag (e.g. `<a name="test"></a>`), props Olivier Burgard. If an empty anchor tag is in front of a link, "thickbox" class won't be added to the link then ThickBox window won't be opened by clicking it. (Auto Thickbox bug)
* FIXED: "Transparent" checkboxes cannot be unchecked when using Color Picker (regression in v1.1)
* UPDATED: Dutch translation (props Michel Bats) and Japanese translation

= 1.4 =
* CHANGED: Moved `<script src='thickbox.js'>` tag from footer to header. Now ThickBox works in themes without calling `wp_footer()` function.
* FIXED: Auto ThickBox Plus had lost the compatibility with WordPress 3.2.1 or earlier. Now the plugin is compatible widely down to even **obsolete WordPress 2.7**, and tested up to WordPress 3.4-alpha.
  * ThickBox window size is completely wrong when showing images in WordPress 3.2.1 or earlier
  * The width of title bar is smaller than inline/AJAX content in WordPress 3.0.6 or earlier
  * Close/Loading image paths are incorrect when using built-in ThickBox in WordPress 3.1.4 or earlier
  * Click event handlers are triggered by drag-moving images in WordPress 2.9.2 or earlier
  * In Options page, several options using JavaScript/CSS are not effective in WordPress 3.2.1 or earlier (e.g. PostBox, Color Picker, Color Preview, UI Slider, Media Uploader, etc.)
  * In Options page, most content is not output in WordPress 2.9.2 or earlier
* FIXED: Caption margin/padding are wrong when background color is specified (regression in v1.2)
* UPDATED: readme.txt ([NextGEN Gallery](http://wordpress.org/extend/plugins/nextgen-gallery/), FAQ, Usage, Special Thanks, etc.)

= 1.3 =
* NEW: "Action - Mouse Click - Clickable Range" option
* NEW: The following "View" options
  * Position - Window - Fixed/Absolute
  * Border Radius - Image
  * Opacity - Thumbnail
* NEW: **"Text - Title/Caption" options**. Now title/caption can be retrieved from several elements/attributes in chosen order. For instance, title can be set to empty, and caption can be set to gallery caption.
* CHANGED: Modified the code of Loading image option when the image is in external domain or `allow_url_fopen = Off` in php.ini
* FIXED: ThickBox window is not opened by clicking link after mouse up outside browser window in drag
* FIXED: Link/Dynamic pseudo-classes (:link, :visited, :hover, :active and :focus) are enabled even in inline/ajax content (ThickBox bug)
* FIXED: Several bugs on invoking ThickBox from inline/AJAX content (ThickBox bug)
  * In inline content, new content is added to the bottom of current content
  * In AJAX content, ThickBox window is not displayed at the right position, and has no "Transition" effect
  * In AJAX content, multiple click event handlers are bound redundantly
* FIXED: Uncaught exception occurs when opening images (ThickBox bug)
* FIXED: '?'/'&' before "TB_iframe" parameter remains in iframe source URL (ThickBox bug)
* UPDATED: Dutch translation (props Michel Bats) and Japanese translation

= 1.2 =
* NEW: The following "View" options
  * Position - Title/Caption - Top/Bottom/None
  * Font Size - Title/Caption/Navigation
  * Background Color - Window (Content)
  * Margin - Image
* CHANGED: Improved ThickBox UI
  * Used larger font size and set top margin in navigation menu
* CHANGED: Applied "Border Radius" option to iframe/ajax window (including title bar)
* FIXED: The following bugs on ThickBox UI
  * Caption and close button are not displayed in the exact vertical center (ThickBox bug)
  * ThickBox window is not displayed in the exact center of browser window (ThickBox bug)
  * Rewrote sizing algorithm of iframe/ajax window accurately (ThickBox bug)
* FIXED: Compatibility with cache plugins is enabled only when WP_DEBUG is true
* FIXED: Replaced white blank.gif with transparent blank.gif
* FIXED: Does not output `<script>` tag when built-in thickbox.js is enabled
* UPDATED: Japanese translation

= 1.1 =
* NEW: Improved Options page UI
  * [Farbtastic Color Picker](http://acko.net/blog/farbtastic-jquery-color-picker-plug-in/) and Color Preview at color options
  * [jQuery UI Slider](http://jqueryui.com/demos/slider/) at "Opacity" option
  * WordPress Media Uploader at "Image" options
  * "Transparent" checkbox at "Background Color" options, "None" checkbox at "Border" and "Box/Text Shadow" options
* NEW: Compatible with **cache plugins** such as [W3 Total Cache](http://wordpress.org/extend/plugins/w3-total-cache/) and [Head Cleaner](http://wordpress.org/extend/plugins/head-cleaner/) (ThickBox bug)
* CHANGED: Broke down auto-thickbox.php into modules (auto-thickbox-options.php, auto-thickbox.js/css)
* UPDATED: Dutch translation (props Michel Bats) and Japanese translation

= 1.0 =
* NEW: Supports **animation effects** (beta)
  * Open/Close/Transition - Zoom/Slide/Fade/None
  * Speed - Fast/Normal/Slow or arbitrary value
* NEW: Improved Options page UI
  * Uses meta boxes to drag to change order, and click to toggle open/close
* CHANGED: Loading image option accepts the URL without scheme and host (i.e. started with '/')
* FIXED: iFramed content is not shown smoothly in Google Chrome and Safari (ThickBox bug)
* FIXED: Jump to current page with double click on image (ThickBox bug)
* FIXED: Scroll bar appears and gray overlay shifts when closing ThickBox window in IE6 (ThickBox bug)
* FIXED: Shortcut keys with Shift key do not work (regression in v0.9)
* UPDATED: Dutch translation (props Michel Bats) and Japanese translation

= 0.9 =
* NEW: Supports **"Drag & Drop" action**. Now ThickBox window can be moved/resized by drag.
  * Added "Drag & Drop - Window (Image/Content) - Move/Resize" options
* NEW: "Auto Resize" option. Auto Resize feature can be disabled if you prefer.
* CHANGED: Optimized global option variables. Brought together multiple variables as an object literal.
* FIXED: Some bound event handlers does not removed. It causes memory leaks. (ThickBox bug)
* FIXED: Hides dotted lines around the left/right side of image for IE6/7 when clicking links
* UPDATED: Minified thickbox.js with [Closure Compiler](https://developers.google.com/closure/compiler/) (reduced about 15% file size)
* UPDATED: Japanese translation

= 0.8 =
* NEW: Supports more mouse/keyboard actions, and added related options
  * Mouse Click - Next, Prev / Next (click on the left/right side of image)
     * Close, None, Loop (click on the first/last image)
  * Keyboard Shortcuts - Home / End
* NEW: **"Image" options** to specify arbitrary images for Prev/Next, Close, Loading, etc.
* CHANGED: Set links to CSS Reference (MDN) from "View" option label
* CHANGED: Shrank padding-bottom of ThickBox window when displayed in "Gallery Images" without caption
* CHANGED: Uses uncompressed thickbox.js/css when WP_DEBUG is true
* FIXED: Loading image is not displayed in the exact center of browser window (ThickBox bug)
* UPDATED: Japanese translation

= 0.7 =
* NEW: Supports more mouse/keyboard actions, and added **"Action" options** to Options page
  * Mouse Click - Close, None
  * Mouse Wheel (Scroll) - Prev / Next, None
  * Keyboard Shortcuts - Esc, Enter, < / >, Left / Right, [Shift +] Tab, [Shift +] Space, BackSpace
* NEW: Uses WordPress translations as much as possible
  * Now ThickBox window is localized to **more than 70 languages**
  * e.g. Arabic, Chinese, Dutch, French, German, Hindi, Italy, Japanese, Korean, Polish, Portuguese, Russian, Spanish, etc.
* CHANGED: Suppresses redundant `<script>`/`<style>` tag output
* UPDATED: Japanese translation

= 0.6 =
* NEW: **"View" options** in Options page
  * Font Family & Weight, Text Color, Background Color, Border, Border Radius, Opacity, Box Shadow and Text Shadow
* NEW: "Reset" button in Options page
* NEW: Added "Support"/"Donate" links to Plugins page
* CHANGED: Switches padding-bottom of ThickBox window depending on Single/Gallery style
* FIXED: Auto Thickbox corrupts links with custom data-* attributes (Auto Thickbox bug)
* FIXED: Image is not displayed in the exact center of ThickBox window (ThickBox bug)
* UPDATED: Added **Dutch (nl_NL) translation, props Michel Bats**
* UPDATED: readme.txt (Usage, Installation, Screenshots, Customization, etc.)

= 0.5 =
* CHANGED: Improved URL string generated by "Full iFrame support" in Auto Thickbox plugin
* FIXED: Supports **AJAX content**. Internal files are displayed on the page without iframe. (Auto Thickbox & ThickBox bug)
* FIXED: Supports **Twenty Eleven** theme (ThickBox bug)

= 0.4 =
* FIXED: Supports **inline content** (#TB_inline) on the page (Auto Thickbox bug)
* FIXED: Supports URL has '?' parameter such as default permalinks and post/page preview (ThickBox bug)
  * e.g. `http://blog.example.com/?p=123&preview=true`

= 0.3 =
* NEW: Added optimized (compressed & tweaked) resources (thickbox.js, thickbox.css)
  * The file size is reduced by about 25%
  * Supports BMP and WebP image formats (now no need to tweak built-in thickbox.js)
  * Rounds corners and shrinks padding-bottom of pop-up window
* CHANGED: Replaced "additional CSS load" option with "optimized resources load" option
* UPDATED: Deleted additional CSS file (auto-thickbox.css)

= 0.2 =
* NEW: Added additional CSS file (auto-thickbox.css), and "CSS load" option
* NEW: Supports **BMP** and [**WebP**](http://code.google.com/speed/webp/) image formats
  * Note: To pop up WebP image requires to tweak thickbox.js
* NEW: Added meta links to Plugins page (Show Details, Settings, Contact Me)
* UPDATED: Include screenshot images in release zip

= 0.1 =
* **Initial release** (based on Auto Thickbox v.2.0.3)
* NEW: By default, overlays images in not "Gallery Images" but "**Single Image**" style
* NEW: By default, automatically also applies ThickBox to **text links** to images (text enclosed with link tag)
* NEW: **Auto ThickBox Plus Options** in Settings menu
  * Default Display Style (Single Image or Gallery Images)
  * ThickBox on Text Links (Auto or Manual)
* UPDATED: Added French, Japanese and Romanian translations
* UPDATED: Added missing MO files of Czech, German and Portuguese

== Usage ==

= WordPress Gallery =

Upload images to WordPress Gallery through the 'Post/Page' screen, then write [Gallery Shortcode](http://codex.wordpress.org/Gallery_Shortcode) with `link="file"` option.

    [gallery link="file"]

= NextGEN Gallery =

Upload images to NextGEN Gallery through [Dashboard] - [Gallery] - [Add Gallery / Images] page, then write [NextGEN Gallery tags](http://wordpress.org/extend/plugins/nextgen-gallery/faq/) like below.

    [nggallery id=1]

= Single Image =

Write image links to images. Image caption is specified by `img@alt` (`<img alt="foo" />`).

    <a href="image.png">
        <img src="image_s.png" alt="foo" />
    </a>

Or write text links to images. Image caption is specified by `a@title` (`<a title="foo">`).

    <a href="image.png" title="foo">Text</a>

\* You can set `width`, `height` and `modal` parameters like below.

    <a href="image.png?width=100&height=100&modal=true">Text</a>

= Gallery Images =

To display images in "Gallery Images" style, add arbitrary value to `a@rel` (`<a rel="foo">`).

    <a href="image1.png" rel="foo">
        <img src="image1_s.png" alt="image1" />
    </a>
    <a href="image2.png" rel="foo">
        <img src="image2_s.png" alt="image2" />
    </a>

= No ThickBox =

To disable ThickBox on specific images, add "nothickbox" to `a@class` (`<a class="nothickbox">`).

    <a href="image.png" class="nothickbox">
        Anchor (image or text)
    </a>

= Inline Content =

1. Write inline content with `@id` (e.g. `<div id="foo">...</div>`)
  * Inline content can be set to hide (e.g. `<div style="display: none">` or `<div style="visibility: hidden">`)
1. Write links and add "thickbox" to `a@class` (`<a class="thickbox">`)
  * Window title is specified by `a@title` (`<a title="bar">`)
1. Set `#TB_inline` to `a@href` (`<a href="#TB_inline">`)
1. Add `inlineId` parameter to `a@href` (`<a href="#TB_inline?inlineId=foo">`)

<!-- code -->

    <div id="foo" style="display: none">
        <div>Here is inline content.</div>
    </div>
    <a href="#TB_inline?inlineId=foo" class="thickbox" title="bar">Anchor</a>

\* You can set `width`, `height` and `modal` parameters like below. For details, see [Inline Content Examples](http://thickbox.net/#container-4).

    <a href="#TB_inline?inlineId=foo&width=600&height=400&modal=true" class="thickbox">Anchor</a>

= iFramed Content =

Write links to internal/external URLs, add "thickbox" to `a@class` (`<a class="thickbox">`) and add `TB_iframe` parameter to `a@href` (`<a href="URL?TB_iframe">`). URLs are opened inside `<iframe>`. Window title is specified by `a@title` (`<a title="foo">`).

    <a href="http://example.com/?TB_iframe" class="thickbox" title="foo">Web page</a>

Here are sample codes to open static/dynamic page, PDF, plain text, QuickTime, MPEG, Adobe Flash and Shockwave.

    <a href="file.html?TB_iframe" class="thickbox">Static page</a>
    <a href="file.php?bar=baz&TB_iframe" class="thickbox">Dynamic page</a>
    <a href="file.pdf?TB_iframe" class="thickbox">Adobe PDF</a>
    <a href="file.txt?TB_iframe" class="thickbox">Plain Text</a>
    <a href="file.mov?TB_iframe" class="thickbox">QuickTime Movie</a>
    <a href="file.mpg?TB_iframe" class="thickbox">MPEG</a>
    <a href="file.swf?TB_iframe" class="thickbox">Adobe Flash</a>
    <a href="file.dcr?TB_iframe" class="thickbox">Adobe Shockwave</a>

Here are sample codes to open [Google Maps](http://maps.google.com/), [Google Docs](http://docs.google.com/), [Evernote](http://evernote.com), [Flickr](http://www.flickr.com/), [Picasa Web Albums](http://picasaweb.google.com/), [SkyDrive](http://skydrive.live.com/), [YouTube](http://www.youtube.com/), [Vimeo](http://vimeo.com/) and [Dailymotion](http://www.dailymotion.com/). You need to use embedded URL.

    <a href="http://maps.google.com/maps?ll=51.477222,0&output=embed" class="thickbox">Google Maps</a>
    <a href="https://docs.google.com/document/pub?id=XXX&embedded=true" class="thickbox">Google Docs</a>
    <a href="https://docs.google.com/spreadsheet/pub?key=XXX&widget=true" class="thickbox">Google Spreadsheets</a>
    <a href="https://www.evernote.com/shard/s23/sh/XXX/YYY" class="thickbox">Evernote</a>
    <a href="http://farm5.staticflickr.com/4051/4386822005_c434921844.jpg">Flickr (Image)</a>
    <a href="https://picasaweb.google.com/lh/photo/XXX?feat=embedwebsite" class="thickbox">Picasa Web Albums</a>
    <a href="https://lh4.googleusercontent.com/XXX/YYY/ZZZ/image.png" class="thickbox">Picasa Web Albums (Image)</a>
    <a href="https://skydrive.live.com/embed?resid=XXX" class="thickbox">SkyDrive</a>
    <a href="http://youtube.com/embed/K-Rs6YEZAt8" class="thickbox">YouTube</a>
    <a href="http://player.vimeo.com/video/12297655" class="thickbox">Vimeo</a>
    <a href="http://dailymotion.com/embed/video/xninjh" class="thickbox">DailyMotion</a>

\* You can set `width`, `height` and `modal` parameters like below. For details, see [iFramed Content Examples](http://thickbox.net/#container-5).

    <a href="http://example.com/?TB_iframe&width=600&height=400&modal=true" class="thickbox">Web page</a>
    <a href="http://example.com/?bar=baz&TB_iframe&width=600&height=400&modal=true" class="thickbox">Web page</a>

= AJAX Content =

Write links to internal files and add "thickbox" to `a@class` (`<a class="thickbox">`). The files on the same domain are opened without `<iframe>`. Window title is specified by `a@title` (`<a title="foo">`).

    <a href="file.html" class="thickbox" title="foo">Static page</a>
    <a href="file.php?bar=baz" class="thickbox" title="foo">Dynamic page</a>

\* You can set `width`, `height` and `modal` parameters like below. For details, see [AJAX Content Examples](http://thickbox.net/#container-6).

    <a href="file.html?width=600&height=400&modal=true" class="thickbox">Static page</a>
    <a href="file.php?bar=baz&width=600&height=400&modal=true" class="thickbox">Dynamic page</a>

To force internal files to open inside `<iframe>`, Add `TB_iframe` parameter to `a@href` (`<a href="file?TB_iframe">`).

    <a href="file.html?TB_iframe" class="thickbox">Static page</a>
    <a href="file.php?bar=baz&TB_iframe" class="thickbox">Dynamic page</a>

\* Note: Parameters after `TB_iframe` are removed (i.e. Parameters before `TB_iframe` are kept as query). In the code above, "&TB_iframe&modal=true" is removed and "bar=baz" is kept as query.

= Invocation from Buttons and Image Maps =

ThickBox can be invoked from the following elements.

* link elements - URL is specified by `href` attribute (`<a href=""></a>`)
* `input` elements - URL is specified by `alt` attribute (`<input alt="" />`)
* `area` elements - URL is specified by `href` attribute (`<area href="" />`)

Here is a sample code to invoke ThickBox from a button.

    <input type="button" alt="image.jpg" class="thickbox" title="foo" value="bar" />

== Localization ==

You can localize the plugin with [Poedit](http://www.poedit.net/). Here is how to translate the plugin into your language.

1. [Download Poedit](http://www.poedit.net/download.php) and install it
2. Run Poedit and select your language
3. Input your name and mail address (optional)
4. Open "auto-thickbox-plus/languages/auto-thickbox.pot" file
5. Select original string and input its translation
6. Save the file as "auto-thickbox-[LANG].po"

"[LANG]" is a language code. For instance, "de_DE" is for German, "sv_SE" is for Swedish, "pt_BR" is for Portuguese spoken in Brazil. If you want to know your language code, see [WordPress in Your Language](http://codex.wordpress.org/WordPress_in_Your_Language). If you need more information, see [Translating WordPress](http://codex.wordpress.org/Translating_WordPress).

I would be grateful if you would [send me](http://attosoft.info/en/contact/) any translation files. Here are the available translations included in the latest plugin.

* Dutch (nl_NL) translation by [Michel Bats](http://www.batssoft.nl/)
* French (fr_FR) translation by [Jean-Bastien Prévots](http://www.jbprevots.fr/)
* Japanese (ja) translation by [attosoft](http://attosoft.info/)

If you have any questions, feel free to [contact me](http://attosoft.info/en/contact/).

== Error Console in Browsers ==

* **Internet Explorer**: Double-click the warning icon in status bar or [Tools] - [Developer Tools] (F12)
* **Mozilla Firefox**: [Firefox/Tools] - [Web Developer] - [Error Console] (Ctrl+Shift+J)
* **Google Chrome**: [Tools] - [JavaScript console] (Ctrl+Shift+J)
* **Opera**: [Opera] - [Page] - [Developer Tools] - [Error Console] (Ctrl+Shift+O)
* **Safari (Mac)**: [Develop] - [Show Error Console] (Option-Command-C)
  * **Safari (Windows)**: Page Menu Button - [Developer] - [Show Error Console] (Ctrl+Alt+C)
  * To enable the developer tools, click Advanced in Safari preferences and check "Show Develop menu in menu bar"
