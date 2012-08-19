<div style="display: none;" >
    <?php
    //////////////////////////////////////////////////////////////////////
    //////////////////////// SAVE PRESET /////////////////////////////////
    //////////////////////////////////////////////////////////////////////
    ?>
    <div id="d01" title="Overwrite Preset">
        <p>
            This will overwrite the current preset. Are you sure?
        </p>
    </div>
    <div id="d02" title="Empty Preset Name">
        <p>
            Please type a name for your preset, or press 'load' on any of the 
            presets below to edit.
        </p>
    </div>
    <div id="d03" title="Error with Required Terms">
        <p>
            <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
            Error: If you selected 'Require terms' you must select 'Include' 
            and/or select at least two terms.
        </p>
    </div>
    <div id="d04" title="Error with Required Taxonomy">
        <p>
            <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 75px 0;"></span>
            Error: If you selected 'Require Taxonomy' you must select 'Include' 
            and/or select at least two terms. If the current page does not have 
            any terms in this taxonomy. The post list may not display anything.
        </p>
    </div>
    <div id="d05" title="Error with Required Taxonomy">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 75px 0;"></span>
            Error: No other taxonomies are being used within the post_type.</p>
    </div>
    <?php
    //////////////////////////////////////////////////////////////////////
    //////////////////////// POST TAXONOMY CONTENT ///////////////////////
    //////////////////////////////////////////////////////////////////////
    ?>
    <div id="d10" title="Post Types, Taxonomies, and Terms Info">
        <p>
            Post Types – Each accordion carries a separate post type. The default 
            post types in WordPress are Posts and Pages. Please Note: Each post 
            or page can have only one post type.
        </p>
        <p>
            Post Parent – Each post type that is hierarchical has a Parent 
            selector to choose which children pages to display. You can select 
            more than one parent unlike the previous versions.
        </p>
        <p>
            Current Page – If selected, the post list will include the current 
            page’s children.
        </p>
        <p>
            Taxonomies – Each taxonomy is spit up in two sections and into 
            different tabs. Hierarchies (categories) are located on the left, 
            and non-hierarchies (tags) are located on the right. With each individual
            taxonomies split into different tabs.
        </p>
        <p>
            Require Taxonomy - If more than one ‘Require Taxonomy’ is checked and 
            terms (or include) are selected, or 'any', then each taxonomy must be required 
            within the post type.
        </p>
        <p>
            Require Terms - If selected and more than one term is selected, then 
            each term must be required within the taxonomy.
        </p>
        <p>
            Include Terms – If selected, the post list preset will include the 
            current page/post terms.
        </p>
        <p>
            Any - When checked, any terms will be included within that taxonomy 
            and post type.
        </p>
    </div>
    <?php
    //////////////////////////////////////////////////////////////////////
    //////////////////////// POST QUERY FILTERS //////////////////////////
    //////////////////////////////////////////////////////////////////////
    ?>
    <div id="d11" title="Post List filter info.">
        <p>
            <strong>Posts Status</strong> - Holds the settings to show which posts
            to display based on the user visibility or the page states. To which
            is only visible to the users with the necessary capabilities to view
            them.
        </p>
        <ul style="margin: auto 16px">
            <li><strong>Visibility</strong> - Display posts as either public, 
                private, or both.</li>
            <li><strong>Status States</strong> - Choose from Published, Future, 
                Pending Review, Draft, Auto-save, Inherit, and/or Trash.</li>
        </ul>
        <p>
            <strong>List Amount</strong> - The numeric value of how many posts 
            you want the post list to display. Negative one (-1) will display 
            all the posts that are available after filtering.
        </p>
        <p>
            <strong>Author Filter</strong> - Show or remove posts that were created
            by a certain author, or authors. You only have the choice of either
            adding or removing, not both.
        </p>
        <ul style="margin: auto 16px">
            <li><strong>Operator</strong> - Determines whether you want to include 
                or exclude certain authors.</li>
            <li><strong>Author Names/IDs</strong> - Displays a list of authors the 
                site current has and is divided into separate role groups.</li>
        </ul>
        <p>
            <strong>Order By</strong> - Choose which page properties to sort from. 
            All of which are only built in params used in WP_Query.
        </p>
        <p>
            <strong>Perm</strong> - Uses the user permission through the user
            capabilities to determine what posts to fill the post list to display
            to the user currently viewing it.
        </p>
        <p>
            <strong>Ignore Sticky Posts</strong> - Ment for the built-in post type
            <b>(Posts)</b> function. When checked, this will prevent sticky posts from
            displaying at the top of the post list.
        </p>
        <p>
            <strong>Exclude Current Post</strong> - When checked, the current post 
            being viewed will be excluded from the post list.
        </p>
        <p>
            <strong>Exclude Posts by ID</strong> - Add post/page IDs seperated by
            a comma will prevent those posts from being included into the post list.
        </p>
        <p>
            <strong>Exclude Duplicates from Current Post</strong> - In the <em>order
                that it is received</em>, each created post list being viewed will add what 
            posts have already been displayed to the user. When checked, this will
            remove any posts that have already been displayed to the user.
        </p>
    </div>
    <?php
    //////////////////////////////////////////////////////////////////////
    //////////////////////// POST LIST STYLING ///////////////////////////
    //////////////////////////////////////////////////////////////////////
    ?>
    <div id="d12" title="Post List content styling.">
        <p>
            <strong>Exit Message</strong> - This container hold the HTML & CSS content
            and if no posts are found to display. Then the post list will display
            this message. If no Exit Message is found, then the post list will use
            the Default Exit Message if enabled in the Plugin's Admin Settings. 
            Otherwise, the plugin will display nothing like it was originally set as.
            <b>Please Note:</b> if you are using the Default Exit Message but you
            don't want to display anything in a certain preset post list. Then simple
            create an empty element to fall back on. For example, an empty 'span' HTML element.
        </p>
        <p>
            <strong>Before List</strong> - Used to store any HTML & CSS code that
            exists before the post/content listings. Useful for div, ul, ol, 
            tables, etc.. As well as storing CSS styling for IDs and Classes.
        </p>
        <p>
            <strong>List Content</strong> - This where you design how your posts
            are going to display in the post list. In here you can use HTML, CSS,
            PHP (<i>requires the PHP shortcode</i>), and the plugin's internal 
            shortcodes. Info can be found at the bottom, or by clicking on the
            shortcode info found below "List content". 
        </p>
        <p>
            <strong>After List</strong> - Used for ending any elements that are 
            still open, or to display a final message to the users/visitors. 
        </p>
    </div>
    <?php
    //////////////////////////////////////////////////////////////////////
    //////////////////////// POST LIST SHORTCODES ////////////////////////
    //////////////////////////////////////////////////////////////////////
    ?>
    <div id="d13" title="List of internal post list shortcodes.">
        <p>
            <b>Shortcodes:</b> Use these codes inside the list item content (will 
            throw errors if placed in before or after HTML fields).
        </p>
        <ul style="margin: auto 16px;">
            <li><b>[ID]</b> - the ID number of the page/post</li>
            <li><b>[post_author]</b> - author of the page/post</li>
            <li><b>[post_permalink]</b> - the page permalink</li>
            <li><b>[post_date format="m-d-Y"]</b> - date page/post was created <b>*</b></li>
            <li><b>[post_date_gmt format="m-d-Y"]</b> - date page/post was created in gmt time <b>*</b></li>
            <li><b>[post_title]</b> - page/post title</li>
            <li><b>[post_content]</b> - page/post content</li>
            <li><b>[post_excerpt length="250"]</b> - page/post excerpt (note the optional character 'length' parameter)</li>
            <li><b>[post_name]</b> - page/post slug name</li>
            <li><b>[post_modified format="m-d-Y"]</b> - date page/post was last modified <b>*</b></li>
            <li><b>[post_modified_gmt format="m-d-Y"]</b> - date page/post was last modified in gmt time <b>*</b></li>
            <li><b>[guid]</b> - original URL of the page/post (post_permalink is probably better)</li>
            <li><b>[comment_count]</b> - number of comments posted for this post/page</li>
            <li><b>[item_number offset="1" increment="1"]</b> - the list index for each page/post. Offset parameter sets start position. Increment sets the number you want to increase on each loop.</li>
            <li><b>[final_end]</b> - on the final list item, everything after this shortcode will be excluded. This will allow you to have commas (or anything else) after each item except the last one.</li>
            <li><b>[post_pdf]</b> - URL to the page/post's PDF file. (Requires Kalin's PDF Creation Station plugin. See help menu for more info.)</li>
            <li><b>[post_meta name="custom_field_name"]</b> - page/post custom field value. Correct 'name' parameter required</li>
            <li><b>[post_tags delimeter=", " links="true"]</b> - post tags list. Optional 'delimiter' parameter sets separator text. Use optional 'links' parameter to turn off links to tag pages</li>
            <li><b>[post_categories delimeter=", " links="true"]</b> - post categories list. Parameters work like tag shortcode.</li>
            <li><b>[post_parent link="true"]</b> - post parent. Use optional 'link' parameter to turn off link</li>
            <li><b>[post_comments before="" after=""]</b> - post comments. Parameters represent text/HTML that will be inserted before and after comment list but will not be displayed if there are no comments. PHP coders: <a href="http://kalinbooks.com/2011/customize-comments-pdf-creation-station">learn how to customize comment display (kalinbooks site).</a></li>
            <li><b>[post_thumb]</b> - URL to the page/post's featured image (requires theme support)</li>
            <li><b>[php_function name="function_name" param=""]</b> - call a user-defined custom function. Refer to <a href="http://kalinbooks.com/2011/custom-php-functions/">this blog post (kalinbooks site)</a> for instructions.</li>
        </ul>
        <p>
            <b>*</b> Time shortcodes have an optional format parameter. Format your 
            dates using these possible tokens: m=month, M=text month, F=full text 
            month, d=day, D=short text Day Y=4 digit year, y=2 digit year, H=hour, 
            i=minute, s=seconds. More tokens listed here: 
            <a href="http://php.net/manual/en/function.date.php" target="_blank">http://php.net/manual/en/function.date.php.</a> 
        </p>
        <p>
            Note: these shortcodes only work in the List item content box on this page.
        </p>
    </div>
    <?php
    //////////////////////////////////////////////////////////////////////
    //////////////////////// PRESET PREVIEW ISSUE DETAILS ////////////////
    //////////////////////////////////////////////////////////////////////
    ?>
    <div id="d14" title="Show Preview Issues & Details." >
        <p>
            Eventually this feature will be changed to provide a more accurate 
            preview of the post list and will be more balanced on the on the admin
            page. Currently, the preview feature has difficulty displaying private
            posts and displaying posts with a preset post list with dynamic features 
            (Current Page & Include Terms). The current query uses the current page
            that you are viewing, and from the admin page, it provides limited results.
        </p>
        <p>
            Later on, this feature will be changed to view the post list as if it 
            were on the selected page you want to preview from (only the post list 
            will display). As well as creating a dialog for it, which may or may not 
            have width & height params for the admin to utilize.
        </p>
        <p>
            A more practical use to <i>get an accurate preview</i> just create a test
            page/post to use to display the preset shortcode. Private posts will
            show up in there, and dynamic features can be utilize. Of course, that
            depends on how your test page/post is set up.
        </p>
    </div>
</div>