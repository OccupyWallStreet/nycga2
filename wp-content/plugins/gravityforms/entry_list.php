<?php
class GFEntryList{
    public static function all_leads_page(){

        if(!GFCommon::ensure_wp_version())
            return;

        $forms = RGFormsModel::get_forms(null, "title");
        $id = RGForms::get("id");

        if(sizeof($forms) == 0)
        {
            ?>
            <div style="margin:50px 0 0 10px;">
                <?php echo sprintf(__("You don't have any active forms. Let's go %screate one%s", "gravityforms"), '<a href="?page=gf_new_form">', '</a>'); ?>
            </div>
            <?php
        }
        else{
            if(empty($id))
                $id = $forms[0]->id;

            self::leads_page($id);
        }
    }

    public static function leads_page($form_id){
        global $wpdb;

        //quit if version of wp is not supported
        if(!GFCommon::ensure_wp_version())
            return;

        echo GFCommon::get_remote_message();
        $action = RGForms::post("action");
        $update_message = "";
        switch($action){
            case "delete" :
                check_admin_referer('gforms_entry_list', 'gforms_entry_list');
                $lead_id = $_POST["action_argument"];
                RGFormsModel::delete_lead($lead_id);
                $update_message = __("Entry deleted.", "gravityforms");
            break;

            case "bulk":
                check_admin_referer('gforms_entry_list', 'gforms_entry_list');

                $bulk_action = !empty($_POST["bulk_action"]) ? $_POST["bulk_action"] : $_POST["bulk_action2"];
                $leads = $_POST["lead"];

                $entry_count = count($leads) > 1 ? sprintf(__("%d entries", "gravityforms"), count($leads)) : __("1 entry", "gravityforms");

                switch($bulk_action) {
                    case "delete":
                        RGFormsModel::delete_leads($leads);
                        $update_message = sprintf(__("%s deleted.", "gravityforms"), $entry_count);
                    break;

                    case "trash":
                        RGFormsModel::update_leads_property($leads, "status", "trash");
                        $update_message = sprintf(__("%s moved to Trash.", "gravityforms"), $entry_count);
                    break;

                    case "restore":
                        RGFormsModel::update_leads_property($leads, "status", "active");
                        $update_message = sprintf(__("%s restored from the Trash.", "gravityforms"), $entry_count);
                    break;

                    case "unspam":
                        RGFormsModel::update_leads_property($leads, "status", "active");
                        $update_message = sprintf(__("%s restored from the spam.", "gravityforms"), $entry_count);
                    break;

                    case "spam":
                        RGFormsModel::update_leads_property($leads, "status", "spam");
                        $update_message = sprintf(__("%s marked as spam.", "gravityforms"), $entry_count);
                    break;

                    case "mark_read":
                        RGFormsModel::update_leads_property($leads, "is_read", 1);
                        $update_message = sprintf(__("%s marked as read.", "gravityforms"), $entry_count);
                    break;

                    case "mark_unread":
                        RGFormsModel::update_leads_property($leads, "is_read", 0);
                        $update_message = sprintf(__("%s marked as unread.", "gravityforms"), $entry_count);
                    break;

                    case "add_star":
                        RGFormsModel::update_leads_property($leads, "is_starred", 1);
                        $update_message = sprintf(__("%s starred.", "gravityforms"), $entry_count);
                    break;

                    case "remove_star":
                        RGFormsModel::update_leads_property($leads, "is_starred", 0);
                        $update_message = sprintf(__("%s unstarred.", "gravityforms"), $entry_count);
                    break;
                }
            break;

            case "change_columns":
                check_admin_referer('gforms_entry_list', 'gforms_entry_list');
                $columns = GFCommon::json_decode(stripslashes($_POST["grid_columns"]), true);
                RGFormsModel::update_grid_column_meta($form_id, $columns);
            break;
        }

        $filter = rgget("filter");
        if(rgpost("button_delete_permanently")){
            RGFormsModel::delete_leads_by_form($form_id, $filter);
        }

        $sort_field = empty($_GET["sort"]) ? 0 : $_GET["sort"];
        $sort_direction = empty($_GET["dir"]) ? "DESC" : $_GET["dir"];
        $search = RGForms::get("s");
        $page_index = empty($_GET["paged"]) ? 0 : intval($_GET["paged"]) - 1;
        
        $star = $filter == "star" ? 1 : null; // is_numeric(RGForms::get("star")) ? intval(RGForms::get("star")) : null;
        $read = $filter == "unread" ? 0 : null; //is_numeric(RGForms::get("read")) ? intval(RGForms::get("read")) : null;

        $page_size = apply_filters("gform_entry_page_size", apply_filters("gform_entry_page_size_{$form_id}", 20, $form_id), $form_id);
        $first_item_index = $page_index * $page_size;

        $form = RGFormsModel::get_form_meta($form_id);
        $sort_field_meta = RGFormsModel::get_field($form, $sort_field);
        $is_numeric = $sort_field_meta["type"] == "number";

        $status = in_array($filter, array("trash", "spam")) ? $filter : "active";
        $leads = RGFormsModel::get_leads($form_id, $sort_field, $sort_direction, $search, $first_item_index, $page_size, $star, $read, $is_numeric, null, null, $status);
        $lead_count = RGFormsModel::get_lead_count($form_id, $search, $star, $read);

        $summary = RGFormsModel::get_form_counts($form_id);
        $active_lead_count = $summary["total"];
        $unread_count = $summary["unread"];
        $starred_count = $summary["starred"];
        $spam_count = $summary["spam"];
        $trash_count = $summary["trash"];

        $columns = RGFormsModel::get_grid_columns($form_id, true);

        $search_qs = empty($search) ? "" : "&s=" . urlencode($search);
        $sort_qs = empty($sort_field) ? "" : "&sort=$sort_field";
        $dir_qs = empty($sort_field) ? "" : "&dir=$sort_direction";
        $star_qs = $star !== null ? "&star=$star" : "";
        $read_qs = $read !== null ? "&read=$read" : "";
        $filter_qs = "&filter=" . $filter;

        // determine which counter to use for paging and set total count
        switch ($filter)
        {
			case "trash" :
				$display_total = ceil($trash_count / $page_size);
				$total_lead_count = $trash_count;
				break;
			case "spam" :
				$display_total = ceil($spam_count / $page_size);
				$total_lead_count = $spam_count;
				break;
			case "star" :
				$display_total = ceil($starred_count / $page_size);
				$total_lead_count = $starred_count;
				break;
			case "unread" :
				$display_total = ceil($unread_count / $page_size);
				$total_lead_count = $unread_count;
				break;
			default :
				$display_total = ceil($active_lead_count / $page_size);
				$total_lead_count = $active_lead_count;
				break;
        }

        $page_links = paginate_links( array(
            'base' =>  admin_url("admin.php") . "?page=gf_entries&view=entries&id=$form_id&%_%" . $search_qs . $sort_qs . $dir_qs. $star_qs . $read_qs . $filter_qs,
            'format' => 'paged=%#%',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $display_total,
            'current' => $page_index + 1,
            'show_all' => false
        ));

        wp_print_scripts(array("thickbox"));
        wp_print_styles(array("thickbox"));

        ?>

        <script src="<?php echo GFCommon::get_base_url() ?>/js/jquery.json-1.3.js?ver=<?php echo GFCommon::$version ?>"></script>
        <script src="<?php echo includes_url() ?>/js/wp-lists.dev.js" type="text/javascript"></script>
        <script src="<?php echo includes_url() ?>/js/wp-ajax-response.dev.js" type="text/javascript"></script>

        <script type="text/javascript">

            var messageTimeout = false;

            function ChangeColumns(columns){
                jQuery("#action").val("change_columns");
                jQuery("#grid_columns").val(jQuery.toJSON(columns));
                tb_remove();
                jQuery("#lead_form")[0].submit();
            }

            function Search(sort_field_id, sort_direction, form_id, search, star, read, filter){
                var search_qs = search == "" ? "" : "&s=" + search;
                var star_qs = star == "" ? "" : "&star=" + star;
                var read_qs = read == "" ? "" : "&read=" + read;
                var filter_qs = filter == "" ? "" : "&filter=" + filter;

                var location = "?page=gf_entries&view=entries&id=" + form_id + "&sort=" + sort_field_id + "&dir=" + sort_direction + search_qs + star_qs + read_qs + filter_qs;
                document.location = location;
            }

            function ToggleStar(img, lead_id, filter){
                var is_starred = img.src.indexOf("star1.png") >=0
                if(is_starred)
                    img.src = img.src.replace("star1.png", "star0.png");
                else
                    img.src = img.src.replace("star0.png", "star1.png");

                jQuery("#lead_row_" + lead_id).toggleClass("lead_starred");
                //if viewing the starred entries, hide the row and adjust the paging counts
                if (filter == "star")
                {
                	var title = jQuery("#lead_row_" + lead_id);
                	title.css("display", "none");
                	UpdatePagingCounts(1);
				}

                UpdateCount("star_count", is_starred ? -1 : 1);

                UpdateLeadProperty(lead_id, "is_starred", is_starred ? 0 : 1);
            }

            function ToggleRead(lead_id, filter){
                var title = jQuery("#lead_row_" + lead_id);
                marking_read = title.hasClass("lead_unread");

                jQuery("#mark_read_" + lead_id).css("display", marking_read ? "none" : "inline");
                jQuery("#mark_unread_" + lead_id).css("display", marking_read ? "inline" : "none");
                jQuery("#is_unread_" + lead_id).css("display", marking_read ? "inline" : "none");
                title.toggleClass("lead_unread");
                //if viewing the unread entries, hide the row and adjust the paging counts
                if (filter == "unread")
                {
                	title.css("display", "none");
                	UpdatePagingCounts(1);
				}

                UpdateCount("unread_count", marking_read ? -1 : 1);
                UpdateLeadProperty(lead_id, "is_read", marking_read ? 1 : 0);
            }

            function UpdateLeadProperty(lead_id, name, value){
                var mysack = new sack("<?php echo admin_url("admin-ajax.php")?>" );
                mysack.execute = 1;
                mysack.method = 'POST';
                mysack.setVar( "action", "rg_update_lead_property" );
                mysack.setVar( "rg_update_lead_property", "<?php echo wp_create_nonce("rg_update_lead_property") ?>" );
                mysack.setVar( "lead_id", lead_id);
                mysack.setVar( "name", name);
                mysack.setVar( "value", value);
                mysack.encVar( "cookie", document.cookie, false );
                mysack.onError = function() { alert('<?php echo esc_js(__("Ajax error while setting lead property", "gravityforms")) ?>' )};
                mysack.runAJAX();

                return true;
            }

            function UpdateCount(element_id, change){
                var element = jQuery("#" + element_id);
                var count = parseInt(element.html()) + change
                element.html(count + "");
            }

            function UpdatePagingCounts(change){
				//update paging header/footer Displaying # - # of #, use counts from header, no need to use footer since they are the same, just update footer paging with header info
                var paging_range_max_header = jQuery("#paging_range_max_header");
                var paging_range_max_footer = jQuery("#paging_range_max_footer");
                var range_change_max = parseInt(paging_range_max_header.html()) - change;
                var paging_total_header = jQuery("#paging_total_header");
                var paging_total_footer = jQuery("#paging_total_footer");
                var total_change = parseInt(paging_total_header.html()) - change;
                var paging_range_min_header = jQuery("#paging_range_min_header");
                var paging_range_min_footer = jQuery("#paging_range_min_footer");
				//if min and max are the same, this is the last entry item on the page, clear out the displaying # - # of # text
                if (parseInt(paging_range_min_header.html()) == parseInt(paging_range_max_header.html()))
                {
					var paging_header = jQuery("#paging_header");
					paging_header.html("");
					var paging_footer = jQuery("#paging_footer");
					paging_footer.html("");
                }
                else
                {
                	paging_range_max_header.html(range_change_max + "");
                	paging_range_max_footer.html(range_change_max + "");
                	paging_total_header.html(total_change + "");
                	paging_total_footer.html(total_change + "");
				}

            }

            function DeleteLead(lead_id){
                jQuery("#action").val("delete");
                jQuery("#action_argument").val(lead_id);
                jQuery("#lead_form")[0].submit();
                return true;
            }

            function handleBulkApply(actionElement){

                var action = jQuery("#" + actionElement).val();
                var defaultModalOptions = '';
                var leadIds = getLeadIds();

                if(leadIds.length == 0){
                    alert('<?php _e('Please select at least one entry.', 'gravityforms'); ?>');
                    return false;
                }

                switch(action){

                case 'resend_notifications':
                    resetResendNotificationsUI();
                    tb_show('<?php _e("Resend Notifications", "gravityforms"); ?>', '#TB_inline?width=350&amp;inlineId=notifications_modal_container', '');
                    return false;
                    break;

                case 'print':
                    resetPrintUI();
                    tb_show('<?php _e("Print Entries", "gravityforms"); ?>', '#TB_inline?width=350&amp;height=250&amp;inlineId=print_modal_container', '');
                    return false;
                    break;

                default:
                    jQuery('#action').val('bulk');
                }

            }

            function getLeadIds(){

                var leads = jQuery(".check-column input[name='lead[]']:checked");
                var leadIds = new Array();

                jQuery(leads).each(function(i){
                    leadIds[i] = jQuery(leads[i]).val();
                });

                return leadIds;
            }

            function BulkResendNotifications(){

                var sendAdmin = jQuery("#notification_admin").is(":checked") ? 1 : 0;
                var sendUser = jQuery("#notification_user").is(":checked") ? 1 : 0;
                var leadIds = getLeadIds();

                var sendTo = jQuery('#notification_override_email').val();

                if(!sendAdmin && !sendUser) {
                    displayMessage("<?php _e("You must select at least one type of notification to resend.", "gravityforms"); ?>", "error", "#notifications_container");
                    return;
                }

                jQuery('#please_wait_container').fadeIn();

                jQuery.post(ajaxurl, {
                    action : "gf_resend_notifications",
                    gf_resend_notifications : '<?php echo wp_create_nonce('gf_resend_notifications'); ?>',
                    sendAdmin : sendAdmin,
                    sendUser : sendUser,
                    sendTo : sendTo,
                    leadIds : leadIds,
                    formId : '<?php echo $form['id']; ?>'
                    },
                    function(response){

                        jQuery('#please_wait_container').hide();

                        if(response) {
                            displayMessage(response, "error", "#notifications_container");
                        } else {
                            var message = '<?php _e("Notifications for %s were resent successfully.", "gravityforms"); ?>';
                            displayMessage(message.replace('%s', leadIds.length + ' ' + getPlural(leadIds.length, '<?php _e('entry', 'gravityforms'); ?>', '<?php _e('entries', 'gravityforms'); ?>')), "updated", "#lead_form");
                            closeModal(true);
                        }

                    }
                );

            }

            function resetResendNotificationsUI(){

                jQuery('#notification_admin, #notification_user').attr('checked', false);
                jQuery('#notifications_container .message, #notifications_override_settings').hide();

            }

            function BulkPrint(){

                var leadIds = getLeadIds();
                var leadsQS = '&lid=' + leadIds.join(',');
                var notesQS = jQuery('#gform_print_notes').is(':checked') ? '&notes=1' : '';
                var pageBreakQS = jQuery('#gform_print_page_break').is(':checked') ? '&page_break=1' : '';

                var url = '<?php echo site_url() ?>/?gf_page=print-entry&fid=<?php echo $form['id'] ?>' + leadsQS + notesQS + pageBreakQS;
                window.open (url,'printwindow');

                closeModal(true);
                hideMessage('#lead_form', false);
            }

            function resetPrintUI(){

                jQuery('#print_options input[type="checkbox"]').attr('checked', false);

            }

            function displayMessage(message, messageClass, container){

                hideMessage(container, true);

                var messageBox = jQuery('<div class="message ' + messageClass + '" style="display:none;"><p>' + message + '</p></div>');
                jQuery(messageBox).prependTo(container).slideDown();

                if(messageClass == 'updated')
                    messageTimeout = setTimeout(function(){ hideMessage(container, false); }, 10000);

            }

            function hideMessage(container, messageQueued){

                if(messageTimeout)
                    clearTimeout(messageTimeout);

                var messageBox = jQuery(container).find('.message');

                if(messageQueued)
                    jQuery(messageBox).remove();
                else
                    jQuery(messageBox).slideUp(function(){ jQuery(this).remove(); });

            }

            function closeModal(isSuccess) {

                if(isSuccess)
                    jQuery('.check-column input[type="checkbox"]').attr('checked', false);

                tb_remove();

            }

            function getPlural(count, singular, plural) {
                return count > 1 ? plural : singular;
            }

            function toggleNotificationOverride(isInit) {

                if(isInit)
                    jQuery('#notification_override_email').val('');

                if(jQuery('#notification_admin').is(':checked') || jQuery('#notification_user').is(':checked')) {
                    jQuery('#notifications_override_settings').slideDown();
                } else {
                    jQuery('#notifications_override_settings').slideUp(function(){
                        jQuery('#notification_override_email').val('');
                    });
                }

            }

            jQuery(document).ready(function(){
                jQuery("#lead_search").keypress(function(event){
                    if(event.keyCode == 13){
                        Search('<?php echo $sort_field ?>', '<?php echo $sort_direction ?>', <?php echo $form_id ?>, this.value, '<?php echo $star ?>', '<?php echo $read ?>', '<?php echo $filter ?>');
                        event.preventDefault();
                    }
                });

                var action = '<?php echo $action; ?>';
                var message = '<?php echo $update_message; ?>';
                if(action && message)
                    displayMessage(message, 'updated', '#lead_form');


                var list = jQuery("#gf_entry_list").wpList( { alt: '<?php echo esc_js(__('Entry List', 'gravityforms')) ?>'} );
                list.bind('wpListDelEnd', function(e, s){

                    var currentStatus = "<?php echo $filter == "trash" || $filter == "spam" ? $filter : "active" ?>";
                    var filter = "<?php echo $filter ?>";
                    var movingTo = "active";
                    if(s.target.className.indexOf(':status=trash') != -1)
                        movingTo = "trash";
                    else if(s.target.className.indexOf(':status=spam') != -1)
                        movingTo = "spam";
                    else if(s.target.className.indexOf(':status=delete') != -1)
                        movingTo = "delete";

                    var id = s.data.entry;

                    var title = jQuery("#lead_row_" + id);
                    var isUnread = title.hasClass("lead_unread");
                    var isStarred = title.hasClass("lead_starred");

                    if(movingTo != "delete"){
                        //Updating All count
                        var allCount = currentStatus == "active" ? -1 : 1;
                        UpdateCount("all_count", allCount);

                        //Updating Unread count
                        if(isUnread){
                            var unreadCount = currentStatus == "active" ? -1 : 1;
                            UpdateCount("unread_count", unreadCount);
                        }

                        //Updating Starred count
                        if(isStarred){
                            var starCount = currentStatus == "active" ? -1 : 1;
                            UpdateCount("star_count", starCount);
                        }
                    }

                    //Updating Spam count
                    if(currentStatus == "spam" || movingTo == "spam"){
                        var spamCount = movingTo == "spam" ? 1 : -1;
                        UpdateCount("spam_count", spamCount);
                        //adjust paging counts
                        if (filter == "spam")
                        {
                        	UpdatePagingCounts(1);
						}
						else
						{
							UpdatePagingCounts(spamCount);
						}
                    }

                    //Updating trash count
                    if(currentStatus == "trash" || movingTo == "trash"){
                        var trashCount = movingTo == "trash" ? 1 : -1;
                        UpdateCount("trash_count", trashCount);
                        //adjust paging counts
                        if (filter == "trash")
                        {
                        	UpdatePagingCounts(1);
						}
						else
						{
							UpdatePagingCounts(trashCount);
						}
                    }

                });;
            });

        </script>
        <link rel="stylesheet" href="<?php echo GFCommon::get_base_url() ?>/css/admin.css" type="text/css" />
        <style>
            /*#TB_window { height: 400px !important; }
            #TB_ajaxContent[style] { height: 370px !important; }*/
            .lead_unread a, .lead_unread td{font-weight: bold;}
            .lead_spam_trash a, .lead_spam_trash td{font-weight:normal;}
            .row-actions a { font-weight:normal;}
            .entry_nowrap{ overflow:hidden; white-space:nowrap; }
            .message { margin: 15px 0 0 !important; }
        </style>


        <div class="wrap">

            <div class="icon32" id="gravity-entry-icon"><br></div>
            <h2><?php _e("Entries", "gravityforms"); ?> : <?php echo $form["title"] ?> </h2>

            <?php RGForms::top_toolbar() ?>

            <form id="lead_form" method="post">
                <?php wp_nonce_field('gforms_entry_list', 'gforms_entry_list') ?>

                <input type="hidden" value="" name="grid_columns" id="grid_columns" />
                <input type="hidden" value="" name="action" id="action" />
                <input type="hidden" value="" name="action_argument" id="action_argument" />

                <ul class="subsubsub">
                    <li><a class="<?php echo empty($filter) ? "current" : "" ?>" href="?page=gf_entries&view=entries&id=<?php echo $form_id ?>"><?php _e("All", "gravityforms"); ?> <span class="count">(<span id="all_count"><?php echo $active_lead_count ?></span>)</span></a> | </li>
                    <li><a class="<?php echo $read !== null ? "current" : ""?>" href="?page=gf_entries&view=entries&id=<?php echo $form_id ?>&filter=unread"><?php _e("Unread", "gravityforms"); ?> <span class="count">(<span id="unread_count"><?php echo $unread_count ?></span>)</span></a> | </li>
                    <li><a class="<?php echo $star !== null ? "current" : ""?>" href="?page=gf_entries&view=entries&id=<?php echo $form_id ?>&filter=star"><?php _e("Starred", "gravityforms"); ?> <span class="count">(<span id="star_count"><?php echo $starred_count ?></span>)</span></a> | </li>
                    <?php
                    if(GFCommon::akismet_enabled($form_id)){
                        ?>
                        <li><a class="<?php echo $filter == "spam" ? "current" : ""?>" href="?page=gf_entries&view=entries&id=<?php echo $form_id ?>&filter=spam"><?php _e("Spam", "gravityforms"); ?> <span class="count">(<span id="spam_count"><?php echo $spam_count ?></span>)</span></a> | </li>
                        <?php
                    }
                    ?>
                    <li><a class="<?php echo $filter == "trash" ? "current" : ""?>" href="?page=gf_entries&view=entries&id=<?php echo $form_id ?>&filter=trash"><?php _e("Trash", "gravityforms"); ?> <span class="count">(<span id="trash_count"><?php echo $trash_count ?></span>)</span></a></li>
                </ul>
                <p class="search-box">
                    <label class="hidden" for="lead_search"><?php _e("Search Entries:", "gravityforms"); ?></label>
                    <input type="text" id="lead_search" value="<?php echo $search ?>"><a class="button" id="lead_search_button" href="javascript:Search('<?php echo $sort_field ?>', '<?php echo $sort_direction ?>', <?php echo $form_id ?>, jQuery('#lead_search').val(), '<?php echo $star ?>', '<?php echo $read ?>', '<?php echo $filter ?>');"><?php _e("Search", "gravityforms") ?></a>

                </p>
                <div class="tablenav">

                    <div class="alignleft actions" style="padding:8px 0 7px 0;">
                        <label class="hidden" for="bulk_action"> <?php _e("Bulk action", "gravityforms") ?></label>
                        <select name="bulk_action" id="bulk_action">
                            <option value=''><?php _e(" Bulk action ", "gravityforms") ?></option>
                            <?php
                            switch($filter){
                                case "trash" :
                                    ?>
                                    <option value='restore'><?php _e("Restore", "gravityforms") ?></option>
                                    <?php
                                    if(GFCommon::current_user_can_any("gravityforms_delete_entries")){
                                        ?>
                                        <option value='delete'><?php _e("Delete Permanently", "gravityforms") ?></option>
                                        <?php
                                    }
                                break;
                                case "spam" :
                                    ?>
                                    <option value='unspam'><?php _e("Not Spam", "gravityforms") ?></option>
                                    <?php
                                    if(GFCommon::current_user_can_any("gravityforms_delete_entries")){
                                        ?>
                                        <option value='delete'><?php _e("Delete Permanently", "gravityforms") ?></option>
                                        <?php
                                    }
                                break;

                                default:
                                    ?>
                                    <option value='mark_read'><?php _e("Mark as Read", "gravityforms") ?></option>
                                    <option value='mark_unread'><?php _e("Mark as Unread", "gravityforms") ?></option>
                                    <option value='add_star'><?php _e("Add Star", "gravityforms") ?></option>
                                    <option value='remove_star'><?php _e("Remove Star", "gravityforms") ?></option>
                                    <option value='resend_notifications'><?php _e("Resend Notifications", "gravityforms") ?></option>
                                    <option value='print'><?php _e("Print", "gravityforms") ?></option>

                                    <?php
                                    if(GFCommon::akismet_enabled($form_id)){
                                        ?>
                                        <option value='spam'><?php _e("Spam", "gravityforms") ?></option>
                                        <?php
                                    }

                                    if(GFCommon::current_user_can_any("gravityforms_delete_entries")){
                                        ?>
                                        <option value='trash'><?php _e("Trash", "gravityforms") ?></option>
                                        <?php
                                    }
                            }?>
                        </select>
                        <?php
                        $apply_button = '<input type="submit" class="button" value="' . __("Apply", "gravityforms") . '" onclick="return handleBulkApply(\'bulk_action\');" />';
                        echo apply_filters("gform_entry_apply_button", $apply_button);

                        if(in_array($filter, array("trash", "spam"))){
                            $message = $filter == "trash" ? __("WARNING! This operation cannot be undone. Empty trash? \'Ok\' to empty trash. \'Cancel\' to abort.") : __("WARNING! This operation cannot be undone. Permanently delete all spam? \'Ok\' to delete. \'Cancel\' to abort.");
                            $button_label = $filter == "trash" ? __("Empty Trash", "gravityforms") : __("Delete All Spam", "gravityforms");
                            ?>
                            <input type="submit" class="button" name="button_delete_permanently" value="<?php echo $button_label ?>" onclick="return confirm('<?php echo esc_attr($message) ?>');" />
                            <?php
                        }
                        ?>
                        <div id="notifications_modal_container" style="display:none;">
                            <div id="notifications_container">

                                <div id="post_tag" class="tagsdiv">
                                    <div id="resend_notifications_options">

                                        <p class="description"><?php _e("Specify which notifications you would like to resend for the selected entries.", "gravityforms"); ?></p>

                                        <?php if(GFCommon::has_admin_notification($form)) { ?>
                                            <input type="checkbox" name="notification_admin" id="notification_admin" onclick="toggleNotificationOverride();" /> <label for="notification_admin"><?php _e("Admin Notification", "gravityforms"); ?></label> <br /><br />
                                        <?php } ?>
                                        <?php if(GFCommon::has_user_notification($form)) { ?>
                                            <input type="checkbox" name="notification_user" id="notification_user" onclick="toggleNotificationOverride();" /> <label for="notification_user"><?php _e("User Notification", "gravityforms"); ?></label> <br /><br />
                                        <?php } ?>

                                        <div id="notifications_override_settings" style="display:none;">

                                            <p class="description" style="padding-top:0; margin-top:0;">You may override the default notification settings
                                             by entering a comma delimited list of emails to which the selected notifications should be sent.</p>
                                            <label for="notification_override_email"><?php _e("Send To", "gravityforms"); ?> <?php gform_tooltip("notification_override_email") ?></label><br />
                                            <input type="text" name="notification_override_email" id="notification_override_email" style="width:99%;" /><br /><br />

                                        </div>

                                        <input type="button" name="notification_resend" id="notification_resend" value="<?php _e("Resend Notifications", "gravityforms") ?>" class="button" style="" onclick="BulkResendNotifications();"/>
                                        <span id="please_wait_container" style="display:none; margin-left: 5px;">
                                            <img src="<?php echo GFCommon::get_base_url()?>/images/loading.gif"> <?php _e("Resending...", "gravityforms"); ?>
                                        </span>

                                    </div>

                                    <div id="resend_notifications_close" style="display:none;margin:10px 0 0;">
                                        <input type="button" name="resend_notifications_close_button" value="<?php _e("Close Window", "gravityforms") ?>" class="button" style="" onclick="closeModal(true);"/>
                                    </div>

                                </div>

                            </div>
                        </div> <!-- / Resend Notifications -->

                        <div id="print_modal_container" style="display:none;">
                            <div id="print_container">

                                <div id="post_tag" class="tagsdiv">
                                    <div id="print_options">

                                        <p class="description"><?php _e("Print all of the selected entries at once.", "gravityforms"); ?></p>

                                        <?php if(GFCommon::current_user_can_any("gravityforms_view_entry_notes")) { ?>
                                            <input type="checkbox" name="gform_print_notes" value="print_notes" checked="checked" id="gform_print_notes" />
                                            <label for="gform_print_notes"><?php _e("Include notes", "gravityforms"); ?></label>
                                            <br /><br />
                                        <?php } ?>

                                        <input type="checkbox" name="gform_print_page_break" value="print_notes" checked="checked" id="gform_print_page_break" />
                                        <label for="gform_print_page_break"><?php _e("Add page break between entries", "gravityforms"); ?></label>
                                        <br /><br />

                                        <input type="button" value="<?php _e("Print", "gravityforms"); ?>" class="button" onclick="BulkPrint();" />

                                    </div>
                                </div>

                            </div>
                        </div> <!-- / Print -->

                    </div>

                    <?php echo self::display_paging_links("header", $page_links, $first_item_index, $page_size, $total_lead_count);?>

                    <div class="clear"></div>
                </div>

                <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr>
                        <th scope="col" id="cb" class="manage-column column-cb check-column"><input type="checkbox" class="headercb" /></th>
                        <?php
                        if(!in_array($filter, array("spam", "trash"))){
                            ?>
                            <th scope="col" id="cb" class="manage-column column-cb check-column" >&nbsp;</th>
                            <?php
                        }

                        foreach($columns as $field_id => $field_info){
                            $dir = $field_id == 0 ? "DESC" : "ASC"; //default every field so ascending sorting except date_created (id=0)
                            if($field_id == $sort_field) //reverting direction if clicking on the currently sorted field
                                $dir = $sort_direction == "ASC" ? "DESC" : "ASC";
                            ?>
                            <th scope="col" class="manage-column entry_nowrap" onclick="Search('<?php echo $field_id ?>', '<?php echo $dir ?>', <?php echo $form_id ?>, '<?php echo $search ?>', '<?php echo $star ?>', '<?php echo $read ?>', '<?php echo $filter ?>');" style="cursor:pointer;"><?php echo esc_html($field_info["label"]) ?></th>
                            <?php
                        }
                        ?>
                        <th scope="col" align="right" width="50">
                            <a title="<?php _e("Select Columns" , "gravityforms") ?>" href="<?php echo site_url() ?>/?gf_page=select_columns&id=<?php echo $form_id ?>&TB_iframe=true&height=365&width=600" class="thickbox entries_edit_icon"><?php _e("Edit", "gravityforms") ?></a>
                        </th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox" /></th>
                        <?php
                        if(!in_array($filter, array("spam", "trash"))){
                            ?>
                            <th scope="col" id="cb" class="manage-column column-cb check-column" >&nbsp;</th>
                        <?php
                        }
                        foreach($columns as $field_id => $field_info){
                            $dir = $field_id == 0 ? "DESC" : "ASC"; //default every field so ascending sorting except date_created (id=0)
                            if($field_id == $sort_field) //reverting direction if clicking on the currently sorted field
                                $dir = $sort_direction == "ASC" ? "DESC" : "ASC";
                            ?>
                            <th scope="col" class="manage-column entry_nowrap" onclick="Search('<?php echo $field_id ?>', '<?php echo $dir ?>', <?php echo $form_id ?>, '<?php echo $search ?>', '<?php echo $star ?>', '<?php echo $read ?>', '<?php echo $filter ?>');" style="cursor:pointer;"><?php echo esc_html($field_info["label"]) ?></th>
                            <?php
                        }
                        ?>
                        <th scope="col" style="width:15px;">
                            <a href="<?php echo site_url() ?>/?gf_page=select_columns&id=<?php echo $form_id ?>&TB_iframe=true&height=365&width=600" class="thickbox entries_edit_icon"><?php _e("Edit", "gravityforms") ?></a>
                        </th>
                    </tr>
                </tfoot>

                <tbody class="list:gf_entry user-list" id="gf_entry_list">
                    <?php
                    if(sizeof($leads) > 0){
                        $field_ids = array_keys($columns);

                        foreach($leads as $position => $lead){

                            $position = ($page_size * $page_index) + $position;

                            ?>
                            <tr id="lead_row_<?php echo $lead["id"] ?>" class='author-self status-inherit <?php echo $lead["is_read"] ? "" : "lead_unread" ?> <?php echo $lead["is_starred"] ? "lead_starred" : "" ?> <?php echo in_array($filter, array("trash", "spam")) ? "lead_spam_trash" : "" ?>'  valign="top">
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="lead[]" value="<?php echo $lead["id"] ?>" />
                                </th>
                                <?php
                                if(!in_array($filter, array("spam", "trash"))){
                                    ?>
                                    <td >
                                        <img id="star_image_<?php echo $lead["id"]?>" src="<?php echo GFCommon::get_base_url() ?>/images/star<?php echo intval($lead["is_starred"]) ?>.png" onclick="ToggleStar(this, <?php echo $lead["id"] . ",'" . $filter . "'" ?>);" />
                                    </td>
                                <?php
                                }

                                $is_first_column = true;

                                $nowrap_class="entry_nowrap";
                                foreach($field_ids as $field_id){
                                    
                                    /* maybe move to function */
                                    
                                    $field = RGFormsModel::get_field($form, $field_id);
                                    $value = rgar($lead, $field_id);

                                    if($field['type'] == 'post_category')
                                        $value = GFCommon::prepare_post_category_value($value, $field, 'entry_list');

                                    //filtering lead value
                                    $value = apply_filters("gform_get_field_value", $value, $lead, $field);

                                    $input_type = !empty($columns[$field_id]["inputType"]) ? $columns[$field_id]["inputType"] : $columns[$field_id]["type"];
                                    switch($input_type){
                                        case "checkbox" :
                                            $value = "";

                                            //if this is the main checkbox field (not an input), display a comma separated list of all inputs
                                            if(absint($field_id) == $field_id){
                                                $lead_field_keys = array_keys($lead);
                                                $items = array();
                                                foreach($lead_field_keys as $input_id){
                                                    if(is_numeric($input_id) && absint($input_id) == $field_id){
                                                        $items[] = GFCommon::selection_display(rgar($lead, $input_id), null, $lead["currency"], false);
                                                    }
                                                }
                                                $value = GFCommon::implode_non_blank(", ", $items);

                                                // special case for post category checkbox fields
                                                if($field['type'] == 'post_category')
                                                    $value = GFCommon::prepare_post_category_value($value, $field, 'entry_list');

                                            }
                                            else{
                                                $value = "";
                                                //looping through lead detail values trying to find an item identical to the column label. Mark with a tick if found.
                                                $lead_field_keys = array_keys($lead);
                                                foreach($lead_field_keys as $input_id){
                                                    //mark as a tick if input label (from form meta) is equal to submitted value (from lead)
                                                    if(is_numeric($input_id) && absint($input_id) == absint($field_id)){
                                                        if($lead[$input_id] == $columns[$field_id]["label"]){
                                                            $value = "<img src='" . GFCommon::get_base_url() . "/images/tick.png'/>";
                                                        }
                                                        else{
                                                            $field = RGFormsModel::get_field($form, $field_id);
                                                            if(rgar($field, "enableChoiceValue") || rgar($field, "enablePrice")){
                                                                foreach($field["choices"] as $choice){
                                                                    if($choice["value"] == $lead[$field_id]){
                                                                        $value = "<img src='" . GFCommon::get_base_url() . "/images/tick.png'/>";
                                                                        break;
                                                                    }
                                                                    else if($field["enablePrice"]){
                                                                        $ary = explode("|", $lead[$field_id]);
                                                                        $val = count($ary) > 0 ? $ary[0] : "";
                                                                        $price = count($ary) > 1 ? $ary[1] : "";

                                                                        if($val == $choice["value"]){
                                                                            $value = "<img src='" . GFCommon::get_base_url() . "/images/tick.png'/>";
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        break;

                                        case "post_image" :
                                            list($url, $title, $caption, $description) = rgexplode("|:|", $value, 4);
                                            if(!empty($url)){
                                                //displaying thumbnail (if file is an image) or an icon based on the extension
                                                $thumb = self::get_icon_url($url);
                                                $value = "<a href='" . esc_attr($url) . "' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$thumb'/></a>";
                                            }
                                        break;

                                        case "fileupload" :
                                            $file_path = $value;
                                            if(!empty($file_path)){
                                                //displaying thumbnail (if file is an image) or an icon based on the extension
                                                $thumb = self::get_icon_url($file_path);
                                                $file_path = esc_attr($file_path);
                                                $value = "<a href='$file_path' target='_blank' title='" . __("Click to view", "gravityforms") . "'><img src='$thumb'/></a>";
                                            }
                                        break;

                                        case "source_url" :
                                            $value = "<a href='" . esc_attr($lead["source_url"]) . "' target='_blank' alt='" . esc_attr($lead["source_url"]) ."' title='" . esc_attr($lead["source_url"]) . "'>.../" . esc_attr(GFCommon::truncate_url($lead["source_url"])) . "</a>";
                                        break;

                                        case "textarea" :
                                        case "post_content" :
                                        case "post_excerpt" :
                                            $value = esc_html($value);
                                        break;

                                        case "date_created" :
                                        case "payment_date" :
                                            $value = GFCommon::format_date($value, false);
                                        break;

                                        case "date" :
                                            $field = RGFormsModel::get_field($form, $field_id);
                                            $value = GFCommon::date_display($value, $field["dateFormat"]);
                                        break;

                                        case "radio" :
                                        case "select" :
                                            $field = RGFormsModel::get_field($form, $field_id);
                                            $value = GFCommon::selection_display($value, $field, $lead["currency"]);
                                        break;

                                        case "number" :
                                            $field = RGFormsModel::get_field($form, $field_id);
                                            $value = GFCommon::format_number($value, rgar($field, "numberFormat"));
                                        break;

                                        case "total" :
                                        case "payment_amount" :
                                            $value = GFCommon::to_money($value, $lead["currency"]);
                                        break;

                                        case "created_by" :
                                            if(!empty($value)){
                                                $userdata = get_userdata($value);
                                                $value = $userdata->user_login;
                                            }
                                        break;
                                        
                                        case "multiselect":
                                            // add space after comma-delimited values
                                            $value = implode(', ', explode(',', $value));
                                            break;
                                        
                                        default:
                                            $value = esc_html($value);
                                    }

                                    $value = apply_filters("gform_entries_field_value", $value, $form_id, $field_id, $lead);
                                    
                                    /* ^ maybe move to function */
                                    
                                    $query_string = "gf_entries&view=entry&id={$form_id}&lid={$lead["id"]}{$search_qs}{$sort_qs}{$dir_qs}{$filter_qs}&paged=" . ($page_index + 1);
                                    if($is_first_column){
                                        ?>
                                        <td class="column-title" >
                                            <a href="admin.php?page=gf_entries&view=entry&id=<?php echo $form_id ?>&lid=<?php echo $lead["id"] . $search_qs . $sort_qs . $dir_qs . $filter_qs?>&paged=<?php echo ($page_index + 1)?>&pos=<?php echo $position; ?>"><?php echo $value ?></a>
                                            <div class="row-actions">
                                                <?php
                                                switch($filter){
                                                    case "trash" :
                                                        ?>
                                                        <span class="edit">
                                                            <a title="<?php _e("View this entry", "gravityforms"); ?>" href="admin.php?page=gf_entries&view=entry&id=<?php echo $form_id ?>&lid=<?php echo $lead["id"] . $search_qs . $sort_qs . $dir_qs . $filter_qs?>&paged=<?php echo ($page_index + 1)?>&pos=<?php echo $position; ?>"><?php _e("View", "gravityforms"); ?></a>
                                                            |
                                                        </span>

                                                        <span class="edit">
                                                            <a class='delete:gf_entry_list:lead_row_<?php echo $lead["id"] ?>::status=active&entry=<?php echo $lead["id"] ?>' title="<?php echo _e("Restore this entry", "gravityforms") ?>" href="<?php echo wp_nonce_url("?page=gf_entries", "gf_delete_entry") ?>"><?php _e("Restore", "gravityforms"); ?></a>
                                                            <?php echo GFCommon::current_user_can_any("gravityforms_delete_entries") ? "|" : "" ?>
                                                        </span>

                                                        <?php
                                                        if(GFCommon::current_user_can_any("gravityforms_delete_entries"))
                                                        {
                                                            ?>
                                                            <span class="delete">
                                                                <?php
                                                                $delete_link ='<a class="delete:gf_entry_list:lead_row_' . $lead["id"] . '::status=delete&entry=' . $lead["id"] . '" title="' . __("Delete this entry permanently", "gravityforms"). '"  href="' . wp_nonce_url("?page=gf_entries", "gf_delete_entry") . '">' . __("Delete Permanently", "gravityforms") .'</a>';
                                                                echo apply_filters("gform_delete_entry_link", $delete_link);
                                                                ?>
                                                            </span>
                                                            <?php
                                                        }
                                                    break;

                                                    case "spam" :
                                                        ?>
                                                        <span class="edit">
                                                            <a title="<?php _e("View this entry", "gravityforms"); ?>" href="admin.php?page=gf_entries&view=entry&id=<?php echo $form_id ?>&lid=<?php echo $lead["id"] . $search_qs . $sort_qs . $dir_qs . $filter_qs?>&paged=<?php echo ($page_index + 1)?>&pos=<?php echo $position; ?>"><?php _e("View", "gravityforms"); ?></a>
                                                            |
                                                        </span>

                                                        <span class="unspam">
                                                            <a class='delete:gf_entry_list:lead_row_<?php echo $lead["id"] ?>::status=unspam&entry=<?php echo $lead["id"] ?>' title="<?php echo _e("Mark this entry as not spam", "gravityforms") ?>" href="<?php echo wp_nonce_url("?page=gf_entries", "gf_delete_entry") ?>"><?php _e("Not Spam", "gravityforms"); ?></a>
                                                            <?php echo GFCommon::current_user_can_any("gravityforms_delete_entries") ? "|" : "" ?>
                                                        </span>

                                                        <?php
                                                        if(GFCommon::current_user_can_any("gravityforms_delete_entries"))
                                                        {
                                                            ?>
                                                            <span class="delete">
                                                                <?php
                                                                $delete_link ='<a class="delete:gf_entry_list:lead_row_' . $lead["id"] . '::status=delete&entry=' . $lead["id"] . '" title="' . __("Delete this entry permanently", "gravityforms"). '"  href="' . wp_nonce_url("?page=gf_entries", "gf_delete_entry") . '">' . __("Delete Permanently", "gravityforms") .'</a>';
                                                                echo apply_filters("gform_delete_entry_link", $delete_link);
                                                                ?>
                                                            </span>
                                                            <?php
                                                        }

                                                    break;

                                                    default:
                                                        ?>
                                                        <span class="edit">
                                                            <a title="<?php _e("View this entry", "gravityforms"); ?>" href="admin.php?page=gf_entries&view=entry&id=<?php echo $form_id ?>&lid=<?php echo $lead["id"] . $search_qs . $sort_qs . $dir_qs . $filter_qs?>&paged=<?php echo ($page_index + 1)?>&pos=<?php echo $position; ?>"><?php _e("View", "gravityforms"); ?></a>
                                                            |
                                                        </span>
                                                        <span class="edit">
                                                            <a id="mark_read_<?php echo $lead["id"] ?>" title="Mark this entry as read" href="javascript:ToggleRead(<?php echo $lead["id"] . ",'" . $filter . "'" ?>);" style="display:<?php echo $lead["is_read"] ? "none" : "inline" ?>;"><?php _e("Mark read", "gravityforms"); ?></a><a id="mark_unread_<?php echo $lead["id"] ?>" title="<?php _e("Mark this entry as unread", "gravityforms"); ?>" href="javascript:ToggleRead(<?php echo $lead["id"] . ",'" . $filter . "'" ?>);" style="display:<?php echo $lead["is_read"] ? "inline" : "none" ?>;"><?php _e("Mark unread", "gravityforms"); ?></a>
                                                            <?php echo GFCommon::current_user_can_any("gravityforms_delete_entries") || GFCommon::akismet_enabled($form_id) ? "|" : "" ?>
                                                        </span>
                                                        <?php
                                                        if(GFCommon::akismet_enabled($form_id)){
                                                            ?>
                                                            <span class="spam">
                                                                <a class='delete:gf_entry_list:lead_row_<?php echo $lead["id"] ?>::status=spam&entry=<?php echo $lead["id"] ?>' title="<?php _e("Mark this entry as spam", "gravityforms") ?>" href="<?php echo wp_nonce_url("?page=gf_entries", "gf_delete_entry") ?>"><?php _e("Spam", "gravityforms"); ?></a>
                                                                <?php echo GFCommon::current_user_can_any("gravityforms_delete_entries") ? "|" : "" ?>
                                                            </span>

                                                        <?php
                                                        }
                                                        if(GFCommon::current_user_can_any("gravityforms_delete_entries"))
                                                        {
                                                            ?>
                                                            <span class="trash">
                                                                <a class='delete:gf_entry_list:lead_row_<?php echo $lead["id"] ?>::status=trash&entry=<?php echo $lead["id"] ?>' title="<?php _e("Move this entry to the trash", "gravityforms") ?>" href="<?php echo wp_nonce_url("?page=gf_entries", "gf_delete_entry") ?>"><?php _e("Trash", "gravityforms"); ?></a>
                                                            </span>
                                                            <?php
                                                        }
                                                    break;
                                                }

                                                do_action("gform_entries_first_column_actions", $form_id, $field_id, $value, $lead, $query_string);
                                                ?>

                                            </div>
                                            <?php
                                            do_action("gform_entries_first_column", $form_id, $field_id, $value, $lead, $query_string);
                                            ?>
                                        </td>
                                        <?php

                                    }
                                    else{
                                        ?>
                                        <td class="<?php echo $nowrap_class ?>">
                                            <?php echo apply_filters("gform_entries_column_filter", $value, $form_id, $field_id, $lead, $query_string); ?>&nbsp;
                                            <?php do_action("gform_entries_column", $form_id, $field_id, $value, $lead, $query_string); ?>
                                        </td>
                                        <?php
                                    }
                                    $is_first_column = false;
                                }
                                ?>
                                <td>&nbsp;</td>
                            </tr>
                            <?php
                        }
                    }
                    else{
                        $message = "";
                        $column_count = sizeof($columns) + 3;

                        switch($filter){
                            case "unread" :
                                $message = __("This form does not have any unread entries.", "gravityforms");
                            break;

                            case "star" :
                                $message = __("This form does not have any starred entries.", "gravityforms");
                            break;

                            case "spam" :
                                $message = __("This form does not have any spam.", "gravityforms");
                                $column_count = sizeof($columns) + 2;
                            break;

                            case "trash" :
                                $message = __("This form does not have any entries in the trash.", "gravityforms");
                                $column_count = sizeof($columns) + 2;
                            break;

                            default :
                                $message = __("This form does not have any entries yet.", "gravityforms");

                        }
                        ?>
                        <tr>
                            <td colspan="<?php echo $column_count?>" style="padding:20px;"><?php echo $message ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                </table>

                <div class="clear"></div>

                <div class="tablenav">

                    <div class="alignleft actions" style="padding:8px 0 7px 0;">
                        <label class="hidden" for="bulk_action2"> <?php _e("Bulk action", "gravityforms") ?></label>
                        <select name="bulk_action2" id="bulk_action2">
                            <option value=''><?php _e(" Bulk action ", "gravityforms") ?></option>
                            <?php
                            switch($filter){
                                case "trash" :
                                    ?>
                                    <option value='restore'><?php _e("Restore", "gravityforms") ?></option>
                                    <?php
                                    if(GFCommon::current_user_can_any("gravityforms_delete_entries")){
                                        ?>
                                        <option value='delete'><?php _e("Delete Permanently", "gravityforms") ?></option>
                                        <?php
                                    }
                                break;
                                case "spam" :
                                    ?>
                                    <option value='unspam'><?php _e("Not Spam", "gravityforms") ?></option>
                                    <?php
                                    if(GFCommon::current_user_can_any("gravityforms_delete_entries")){
                                        ?>
                                        <option value='delete'><?php _e("Delete Permanently", "gravityforms") ?></option>
                                        <?php
                                    }
                                break;

                                default:
                                ?>
                                <option value='mark_read'><?php _e("Mark as Read", "gravityforms") ?></option>
                                <option value='mark_unread'><?php _e("Mark as Unread", "gravityforms") ?></option>
                                <option value='add_star'><?php _e("Add Star", "gravityforms") ?></option>
                                <option value='remove_star'><?php _e("Remove Star", "gravityforms") ?></option>
                                <option value='resend_notifications'><?php _e("Resend Notifications", "gravityforms") ?></option>
                                <option value='print'><?php _e("Print", "gravityforms") ?></option>
                                <?php
                                if(GFCommon::akismet_enabled($form_id)){
                                    ?>
                                    <option value='spam'><?php _e("Spam", "gravityforms") ?></option>
                                    <?php
                                }

                                if(GFCommon::current_user_can_any("gravityforms_delete_entries")){
                                    ?>
                                    <option value='trash'><?php _e("Trash", "gravityforms") ?></option>
                                    <?php
                                }
                            }?>
                        </select>
                        <?php
                        $apply_button = '<input type="submit" class="button" value="' . __("Apply", "gravityforms") . '" onclick="return handleBulkApply(\'bulk_action2\');" />';
                        echo apply_filters("gform_entry_apply_button", $apply_button);
                        ?>
                    </div>

                    <?php echo self::display_paging_links("footer", $page_links, $first_item_index, $page_size, $total_lead_count);?>

                    <div class="clear"></div>
                </div>

            </form>
        </div>
        <?php
    }

    private static function get_icon_url($path){
        $info = pathinfo($path);
        switch(strtolower(rgar($info, "extension"))){

            case "css" :
                $file_name = "icon_css.gif";
            break;

            case "doc" :
                $file_name = "icon_doc.gif";
            break;

            case "fla" :
                $file_name = "icon_fla.gif";
            break;

            case "html" :
            case "htm" :
            case "shtml" :
                $file_name = "icon_html.gif";
            break;

            case "js" :
                $file_name = "icon_js.gif";
            break;

            case "log" :
                $file_name = "icon_log.gif";
            break;

            case "mov" :
                $file_name = "icon_mov.gif";
            break;

            case "pdf" :
                $file_name = "icon_pdf.gif";
            break;

            case "php" :
                $file_name = "icon_php.gif";
            break;

            case "ppt" :
                $file_name = "icon_ppt.gif";
            break;

            case "psd" :
                $file_name = "icon_psd.gif";
            break;

            case "sql" :
                $file_name = "icon_sql.gif";
            break;

            case "swf" :
                $file_name = "icon_swf.gif";
            break;

            case "txt" :
                $file_name = "icon_txt.gif";
            break;

            case "xls" :
                $file_name = "icon_xls.gif";
            break;

            case "xml" :
                $file_name = "icon_xml.gif";
            break;

            case "zip" :
                $file_name = "icon_zip.gif";
            break;

            case "gif" :
            case "jpg" :
            case "jpeg":
            case "png" :
            case "bmp" :
            case "tif" :
            case "eps" :
                $file_name = "icon_image.gif";
            break;

            case "mp3" :
            case "wav" :
            case "wma" :
                $file_name = "icon_audio.gif";
            break;

            case "mp4" :
            case "avi" :
            case "wmv" :
            case "flv" :
                $file_name = "icon_video.gif";
            break;

            default:
                $file_name = "icon_generic.gif";
            break;
        }

        return GFCommon::get_base_url() . "/images/doctypes/$file_name";
    }

    private static function update_message(){



    }

    private function display_paging_links($which, $page_links, $first_item_index, $page_size, $total_lead_count) {
		//Displaying paging links if appropriate
		//$which - header or footer, so the items can have unique names
		if($page_links)
		{
			$paging_html = '
			<div class="tablenav-pages">
			<span id="paging_' . $which . '" class="displaying-num">';
			$range_max = '<span id="paging_range_max_' . $which . '">';
			if (($first_item_index + $page_size) > $total_lead_count)
			{
				$range_max .= $total_lead_count;
			}
			else
			{
				$range_max .= ($first_item_index + $page_size);
			}
			$range_max .= "</span>";
			$range_min = '<span id="paging_range_min_' . $which . '">' . ($first_item_index + 1) . "</span>";
			$paging_total = '<span id="paging_total_' . $which . '">' . $total_lead_count . "</span>";
			$paging_html .= sprintf(__("Displaying %s - %s of %s", "gravityforms"), $range_min,  $range_max , $paging_total);
			$paging_html .= "</span>" .$page_links . "</div>";
		}
		else
		{
			$paging_html = "";
		}
		return $paging_html;
    }
    
}

?>