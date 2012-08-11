<?php

    $arg = NULL;
    $groups = $this->get_groups();

    if ( isset( $_REQUEST['orderby'] ) )
        $arg['orderby'] = $_REQUEST['orderby'];

    if ( isset( $_REQUEST['sortby'] ) )
        $arg['sortby'] = $_REQUEST['sortby'];

    if ( isset( $_REQUEST['order'] ) )
        $arg['order'] = $_REQUEST['order'];


    //Pagination option
    if ( isset( $_REQUEST['per_page'] ) )
        $per_page = $_REQUEST['per_page'];
    else
        $per_page = 15;


    //pagination for non filter
    if ( ! isset( $_REQUEST['filter'] ) ) {
        $count = $this->get_count_members();

        $members_pagination = $this->get_pagination_data( $count, $per_page );

        if ( isset( $members_pagination['limit'] ) )
            $arg['limit'] = $members_pagination['limit'];
    }


    if ( isset( $_REQUEST['order'] ) && "desc" == $_REQUEST['order'] )
        $order = "asc";
    else
        $order = "desc";


    if (  isset( $_REQUEST['filter'] ) && "group" == $_REQUEST['filter'] ) {
        if ( 0 < $_REQUEST['group_id'] ) {

            //pagination for filter by group
            $count = $this->get_count_members_of_group( $_REQUEST['group_id'] );

            $members_pagination = $this->get_pagination_data( $count, $per_page );
            if ( isset( $members_pagination['limit'] ) )
                $limit = $members_pagination['limit'];
            else
                $limit = '';


            $members_id = $this->get_members_of_group( $_REQUEST['group_id'], $limit );
            foreach( $members_id as $member_id )
                $members[] = $this->get_member( $member_id );

            $filter = "&filter=group&group_id=" . $_REQUEST['group_id'];

            if ( isset( $arg['orderby'] ) )
                $members = $this->sort_array_by_field( $members, $arg['orderby'], $arg['order'] );
            else if ( isset( $arg['sortby'] ) )
                $members = $this->sort_array_by_field( $members, $arg['sortby'], $arg['order'] );
        }

    } else if (  isset( $_REQUEST['filter'] ) && "unsubscribed" == $_REQUEST['filter'] ) {
        $count = $this->get_count_unsubscribe_members( );

        $members_pagination = $this->get_pagination_data( $count, $per_page );
        if ( isset( $members_pagination['limit'] ) )
            $limit = $members_pagination['limit'];
        else
            $limit = '';

        $members = $this->get_unsubscribe_member( $limit );

        $filter = "&filter=unsubscribed";

    } else {
        $members = $this->get_members( $arg );
    }

    $siteurl = get_option( 'siteurl' );

    //Display status message
    if ( isset( $_GET['updated'] ) ) {
        ?><div id="message" class="updated fade"><p><?php echo urldecode( $_GET['dmsg'] ); ?></p></div><?php
    }

?>
    <script type="text/javascript">
        jQuery( document ).ready( function() {

            jQuery.fn.changeGroups = function ( id ) {

                if ( '<?php _e( 'Save Groups', 'email-newsletter' ) ?>' == jQuery( "#change_button_" + id ).val() ) {
                    jQuery( "#newsletter_action" ).val( "change_group" );
                    jQuery( "#member_id" ).val( id );
                    jQuery( "#form_members" ).submit();
                    return;
                }
                jQuery( "body" ).css( "cursor", "wait" );
                jQuery( "#form_members input[type=button]" ).attr( 'disabled', true );
                jQuery.ajax({
                    type: "POST",
                    url: "<?php echo $siteurl;?>/wp-admin/admin-ajax.php",
                    data: "action=change_groups&member_id=" + id,
                    success: function(html){
                        jQuery( "#change_group_block_" + id ).html( html );
                        jQuery( "#close_block_" + id ).html( '<input type="button" onClick="jQuery(this).closeChangeGroups( ' + id + ' );" value="<?php _e( 'Close', 'email-newsletter' ) ?>" />' );

                        jQuery( "#change_button_" + id ).val('<?php _e( 'Save Groups', 'email-newsletter' ) ?>');

                        if ( jQuery( "#change_group_block_" + id + " input[type=checkbox]" ).length )
                            jQuery( "#change_button_" + id ).attr( 'disabled', false );

                        jQuery( "body" ).css( "cursor", "default" );
                    }
                });




            };

            jQuery.fn.closeChangeGroups = function ( id ) {
                jQuery( "#form_members input[type=button]" ).attr( 'disabled', false );
                jQuery( "#change_group_block_" + id ).html( '' );
                jQuery( "#close_block_" + id ).html( '' );
                jQuery( "#change_button_" + id ).val('<?php _e( 'Change groups', 'email-newsletter' ) ?>');
            };



            //Add new member
            jQuery( "#add_member" ).click( function() {
                if ( "" == jQuery( "#member_email" ).val() ) {
                    alert('<?php _e( 'Please write Email of the member', 'email-newsletter' ) ?>');
                    return false;
                }
                jQuery( "#newsletter_action2" ).val( 'add_member' );
                jQuery( "#add_new_member" ).submit();

            });

            //Import new members
            jQuery( "#import_members" ).click( function() {
                if ( "" == jQuery( "#import_members_file" ).val() ) {
                    jQuery( "#import_file_line" ).attr('class', 'newsletter_error');
                    return false;
                }

                jQuery( "#newsletter_action2" ).val( 'import_members' );
                jQuery( "#add_new_member" ).submit();

            });


            //Some actions
            jQuery( "#apply" ).click( function() {
                if ( -1 == jQuery( "#some_action" ).val() ) {
                    return false;
                } else if ( ( 'add_members_group' == jQuery( "#some_action" ).val() || 'delete_members_group' == jQuery( "#some_action" ).val() )
                                && -1 == jQuery( "#list_group_id" ).val() ) {
                    return false;
                }

                jQuery( "#newsletter_action" ).val( jQuery( "#some_action" ).val() );
                jQuery( "#form_members" ).submit();
                return false;
            });

            //show/hide select box of groups list
            jQuery( "#some_action" ).change( function() {
                if ( 'add_members_group' == jQuery( "#some_action" ).val() || 'delete_members_group' == jQuery( "#some_action" ).val() ) {
                    jQuery( "#list_group_id" ).show();
                } else {
                    jQuery( "#list_group_id" ).hide();
                }
            });


            //change per page count
            jQuery( "#per_page" ).change( function() {
                jQuery( "#newsletter_action" ).val( '' );
                jQuery( "#form_members" ).submit();
                return false;
            });


            jQuery( "#show_add_form" ).click( function() {
                jQuery( "#panel" ).slideToggle( "slow" );

                if ( "<?php _e( 'Show the New Member / Import forms', 'email-newsletter' ) ?>" == jQuery(this).val() )
                    jQuery(this).val( '<?php _e( 'Hide the New Member / Import forms', 'email-newsletter' ) ?>' );
                else
                    jQuery(this).val( '<?php _e( 'Show the New Member / Import forms', 'email-newsletter' ) ?>' );

                return false;
            });


        });
    </script>

    <div class="wrap">
        <h2><?php _e( 'Members', 'email-newsletter' ) ?></h2>
        <p><?php _e( 'At this page you can add or remove members from groups.', 'email-newsletter' ) ?></p>


        <p class="slide">
            <input type="button" class="button-secondary action" id="show_add_form" value="<?php _e( 'Show the New Member / Import forms', 'email-newsletter' ) ?>" />
        </p>

        <div id="panel">
            <form action="" method="post" name="add_new_member" id="add_new_member" enctype="multipart/form-data">
                <input type="hidden" name="newsletter_action" id="newsletter_action2" value="" />
                <input type="hidden" name="members_import" id="members_import" value="" />
                <table cellspacing="10">
                    <tr>
                        <td valign="top">
                            <table class="create_mamber">
                                <tr>
                                    <td colspan="2">
                                        <h3><?php _e( 'Create the new member:', 'email-newsletter' ) ?></h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php _e( 'Member Email:', 'email-newsletter' ) ?><span class="required">*</span>
                                    </td>
                                    <td>
                                        <input type="text" name="member[email]" id="member_email" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php _e( 'First Name:', 'email-newsletter' ) ?>
                                    </td>
                                    <td>
                                        <input type="text" name="member[fname]" id="member_fname" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php _e( 'Last Name:', 'email-newsletter' ) ?>
                                    </td>
                                    <td>
                                        <input type="text" name="member[lname]" id="member_lname" />
                                    </td>
                                </tr>

                                <?php if ( $groups ):?>
                                    <tr>
                                        <td>
                                            <?php _e( 'Groups:', 'email-newsletter' ) ?>
                                        </td>
                                        <td>
                                            <?php foreach( $groups as $group ) : ?>
                                                <input type="checkbox" name="member[groups_id][]" value="<?php echo $group['group_id'];?>" />
                                                <label for="member[groups_id][]">
                                                    <?php echo ( $group['public'] ) ? $group['group_name'] .' (public)' : $group['group_name']; ?>
                                                </label>
                                                <br />
                                            <?php endforeach; ?>
                                        </td>
                                    </tr>
                                <?php endif;?>

                                <tr>
                                    <td colspan="2">
                                        <input type="button" name="add_member" id="add_member" value="<?php _e( 'Add Member', 'email-newsletter' ) ?>" />
                                    </td>
                                </tr>
                            </table>
                       </td>
                       <td valign="top">
                            <table class="import_members">
                                <tr>
                                    <td colspan="2">
                                        <h3><?php _e( 'Import members:', 'email-newsletter' ) ?></h3>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                       <span class="description"><?php _e( 'Note: The file should have the next columns: Email (required), First Name (not required), Last Name (not required). Without headers.', 'email-newsletter' ) ?></span>
                                    </td>
                                </tr>
                                <tr id="import_file_line">
                                    <td>
                                        <?php _e( 'From .csv file:', 'email-newsletter' ) ?><span class="required">*</span>
                                    </td>
                                    <td>
                                        <input type="file" name="import_members_file" id="import_members_file" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php _e( 'Separated by:', 'email-newsletter' ) ?>
                                    </td>
                                    <td>
                                        <select name="separ_sign">
                                            <option value="1" <?php echo ( isset( $_GET['separ_sign'] ) && 1 == $_GET['separ_sign'] ) ? 'selected': ''; ?> >
                                                <?php _e( 'Semicolon', 'email-newsletter' ) ?> (;)&nbsp;
                                            </option>
                                            <option value="2" <?php echo ( isset( $_GET['separ_sign'] ) && 2 == $_GET['separ_sign'] ) ? 'selected': ''; ?> >
                                                <?php _e( 'Comma', 'email-newsletter' ) ?> (,)&nbsp;
                                            </option>
                                        </select>
                                    </td>
                                </tr>

                                <?php if ( $groups ):?>
                                    <tr>
                                        <td>
                                            <?php _e( 'Assign with group:', 'email-newsletter' ) ?>
                                        </td>
                                        <td>
                                            <?php foreach( $groups as $group ) : ?>
                                                <input type="checkbox" name="import_groups_id[]" value="<?php echo $group['group_id'];?>" />
                                                <label for="import_groups_id[]">
                                                    <?php echo ( $group['public'] ) ? $group['group_name'] .' (public)' : $group['group_name']; ?>
                                                </label>
                                                <br />
                                            <?php endforeach; ?>
                                        </td>
                                    </tr>
                                <?php endif;?>
                                <tr>
                                    <td colspan="2">
                                        <input type="button" name="import_members" id="import_members" value="<?php _e( 'Import members', 'email-newsletter' ) ?>" />
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </form>
        </div>

        <form method="post" action="" name="form_members" id="form_members" >
            <input type="hidden" name="member_id" id="member_id" value="" />
            <input type="hidden" name="newsletter_action" id="newsletter_action" value="" />
            <table width="700px" class="widefat post fixed" style="width:95%;">
                <thead>
                    <tr>
                        <th style="" class="manage-column column-cb check-column" id="cb" scope="col">
                            <input type="checkbox">
                        </th>
                        <th class="manage-column column-name <?php echo "member_email" == $_REQUEST['orderby'] ? 'sorted ' . $_REQUEST['order'] : 'sortable desc';?>">
                            <a href="admin.php?page=newsletters-members&orderby=member_email&order=<?php echo $order;?><?php echo ( isset( $filter ) ) ? $filter : ''; ?><?php echo '&per_page=' . $per_page ?><?php echo ( isset( $members_pagination['cpage_str'] ) ) ? $members_pagination['cpage_str'] : ''; ?>">
                                <span><?php _e( 'Email Address', 'email-newsletter' ) ?>   </span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th class="manage-column column-name <?php echo "member_fname" == $_REQUEST['orderby'] ? 'sorted ' . $_REQUEST['order'] : 'sortable desc';?>">
                            <a href="admin.php?page=newsletters-members&orderby=member_fname&order=<?php echo $order;?><?php echo ( isset( $filter ) ) ? $filter : ''; ;?><?php echo '&per_page=' . $per_page ?><?php echo ( isset( $members_pagination['cpage_str'] ) ) ? $members_pagination['cpage_str'] : ''; ?>">
                                <span><?php _e( 'Name', 'email-newsletter' ) ?>   </span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th class="manage-column column-name <?php echo "join_date" == $_REQUEST['orderby'] ? 'sorted ' . $_REQUEST['order'] : 'sortable desc';?>">
                            <a href="admin.php?page=newsletters-members&orderby=join_date&order=<?php echo $order;?><?php echo ( isset( $filter ) ) ? $filter : ''; ;?><?php echo '&per_page=' . $per_page ?><?php echo ( isset( $members_pagination['cpage_str'] ) ) ? $members_pagination['cpage_str'] : ''; ?>">
                                <span><?php _e( 'Join Date', 'email-newsletter' ) ?>   </span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th class="manage-column column-name <?php echo "count_sent" == $_REQUEST['sortby'] ? 'sorted ' . $_REQUEST['order'] : 'sortable desc';?>">
                            <a href="admin.php?page=newsletters-members&sortby=count_sent&order=<?php echo $order;?><?php echo ( isset( $filter ) ) ? $filter : ''; ;?><?php echo '&per_page=' . $per_page ?><?php echo ( isset( $members_pagination['cpage_str'] ) ) ? $members_pagination['cpage_str'] : ''; ?>">
                                <span><?php _e( 'Number Sent', 'email-newsletter' ) ?>   </span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th class="manage-column column-name <?php echo "count_opened" == $_REQUEST['sortby'] ? 'sorted ' . $_REQUEST['order'] : 'sortable desc';?>">
                            <a href="admin.php?page=newsletters-members&sortby=count_opened&order=<?php echo $order;?><?php echo ( isset( $filter ) ) ? $filter : ''; ;?><?php echo '&per_page=' . $per_page ?><?php echo ( isset( $members_pagination['cpage_str'] ) ) ? $members_pagination['cpage_str'] : ''; ?>">
                                <span><?php _e( 'Number Opened', 'email-newsletter' ) ?>   </span>
                                <span class="sorting-indicator"></span>
                            </a>
                        </th>
                        <th>
                            <?php _e( 'Groups', 'email-newsletter' ) ?><?php echo isset( $filter ) ? ' <a href="admin.php?page=newsletters-members&per_page=' . $per_page . '">(all)</a>' : ''; ?>
                        </th>
                        <th>
                            <?php _e( 'Actions', 'email-newsletter' ) ?>
                        </th>
                    </tr>
                </thead>
            <?php
            $i = 0;
            if ( $members )
                foreach( $members as $member ) {
                    if ( $i % 2 == 0 )
                        echo "<tr class='alternate'>";
                    else
                        echo "<tr class='' >";

                    $i++;

                    $member['member_nicename'] = $member['member_fname'];
                    $member['member_nicename'] .= $member['member_lname'] ? ' ' . $member['member_lname'] : '';

            ?>
                    <th class="check-column" scope="row">
                        <input type="checkbox" value="<?php echo $member['member_id'];?>" class="administrator" id="user_<?php echo $member['member_id'];?>" name="members_id[]">
                    </th>
                    <td style="vertical-align: middle;">
                       <?php echo $member['member_email']; ?>
                    </td>
                    <td style="vertical-align: middle;">
                        <?php echo $member['member_nicename']; ?>
                    </td>
                    <td style="vertical-align: middle;">
                        <?php echo date( $this->settings['date_format'] . " h:i:s", $member['join_date'] ); ?>
                    </td>
                    <td style="vertical-align: middle;">
                        <?php echo $member['count_sent']; ?> <?php _e( 'newsletters', 'email-newsletter' ) ?>
                    </td>
                    <td style="vertical-align: middle;">
                        <?php echo $member['count_opened']; ?> <?php _e( 'newsletters', 'email-newsletter' ) ?>
                    </td>
                    <td style="vertical-align: middle;">
                    <?php
                        if ( "" != $member['unsubscribe_code'] ) {
                            $groups_id = $this->get_memeber_groups( $member['member_id'] );
                            if ( $groups_id ) {
                                $memeber_groups = "";
                                foreach ( $groups_id as $group_id) {
                                    $group  = $this->get_group_by_id( $group_id );
                                    if ( isset( $_REQUEST['group_id'] ) && $group_id == $_REQUEST['group_id'] )
                                        $memeber_groups .= '<span style="color: green;" >' . $group['group_name'] . '</span>, ';
                                    else
                                        $memeber_groups .= '<a href="admin.php?page=newsletters-members&filter=group&group_id=' . $group['group_id'] . '&per_page=' . $per_page . '" >' . $group['group_name'] . '</a>, ';

                                }
                                echo substr( $memeber_groups, 0, strlen( $memeber_groups )-2 );
                            }
                        } else {
                            echo '<a href="admin.php?page=newsletters-members&filter=unsubscribed"><span class="red" >' . __( 'Unsubscribed', 'email-newsletter' ) . '</span></a>';
                        }
                    ?>
                    </td>
                    <td style="vertical-align: middle;">
                        <span id="close_block_<?php echo $member['member_id'];?>"></span>
                        <div id="change_group_block_<?php echo $member['member_id'];?>"></div>
                        <input type="button" id="change_button_<?php echo $member['member_id'];?>" value="<?php _e( 'Change groups', 'email-newsletter' ) ?>" onclick="jQuery(this).changeGroups( <?php echo $member['member_id'];?> );" />
                    </td>
                </tr>
            <?php
                }
            ?>
            </table>
            <div class="tablenav bottom">
                <div class="alignleft actions">
                    <select name="some_action" id="some_action">
                        <option selected="selected" value="-1"><?php _e( 'Bulk Actions', 'email-newsletter' ) ?></option>

                        <?php if ( $groups ): ?>
                        <option value="add_members_group"><?php _e( 'Add to group', 'email-newsletter' ) ?></option>
                        <option value="delete_members_group"><?php _e( 'Delete from group', 'email-newsletter' ) ?></option>
                        <?php endif; ?>

                        <option value="delete_members"><?php _e( 'Delete', 'email-newsletter' ) ?></option>
                    </select>

                    <?php if ( $groups ): ?>
                    <select name="list_group_id" id="list_group_id" style="display: none;">
                        <option selected="selected" value="-1"> <?php _e( 'Group List', 'email-newsletter' ) ?> </option>
                        <?php foreach( $groups as $group ) : ?>
                            <option value="<?php echo $group['group_id'];?>">
                            <?php echo ( $group['public'] ) ? $group['group_name'] .' (public)' : $group['group_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>

                    <input type="button" value="<?php _e( 'Apply', 'email-newsletter' ) ?>" id="apply" class="button-secondary action" name="">
                </div>

                <div class="tablenav-pages">
                    <span class="displaying-num">
                        <?php echo ( isset( $members_pagination ) ) ? $members_pagination['count'] : $count; ?> <?php _e( 'member(s)', 'email-newsletter' ) ?>
                        <?php _e( 'by', 'email-newsletter' ) ?>
                        <select name="per_page" id="per_page">
                            <option value="15" <?php echo ( 15 == $per_page ) ? 'selected' : ''; ?> >15</option>
                            <option value="30" <?php echo ( 30 == $per_page ) ? 'selected' : ''; ?> >30</option>
                            <option value="50" <?php echo ( 50 == $per_page ) ? 'selected' : ''; ?> >50</option>
                            <option value="100" <?php echo ( 100 == $per_page ) ? 'selected' : ''; ?> >100</option>
                            <option value="all" <?php echo ( 'all' == $per_page ) ? 'selected' : ''; ?> ><?php _e( 'All', 'email-newsletter' ) ?></option>
                        </select>
                        <?php _e( 'per page.', 'email-newsletter' ) ?>
                    </span>

                    <?php
                    if ( isset( $members_pagination ) && is_array( $members_pagination ) ):

                        //count page count before and after current
                        $pagedisprange = 3;

                        $pagescount = ceil( $members_pagination['count'] / $per_page );

                        //start page number
                        $stpage = $members_pagination['cpage'] - $pagedisprange;
                        if ( $stpage < 1 )
                            $stpage = 1;

                        // end page number
                        $endpage = $members_pagination['cpage'] + $pagedisprange;
                        if ( $endpage > $pagescount )
                            $endpage=$pagescount;

                        //gen link url
                        $URL = 'admin.php?page=newsletters-members';
                        if ( isset( $_REQUEST['orderby'] ) )
                            $URL .= '&orderby=' . $_REQUEST['orderby'];


                        if ( isset( $_REQUEST['sortby'] ) )
                            $URL .= '&sortby=' . $_REQUEST['sortby'];


                        if ( isset( $filter ) )
                            $URL .= $filter;


                        if ( isset( $_REQUEST['order'] ) )
                            $URL .= '&order=' . $_REQUEST['order'];
                            $URL .= '&per_page=' . $per_page;

                        ?>

                        <span class="pagination-links">
                        <?php
                            if ( $members_pagination['cpage'] > 1 ) {
                                // first
                                echo '<a href="' . $URL . '&cpage=1" title="Go to the first page" class="first-page" ><<</a> ';
                                // prev
                                echo '<a href="' . $URL . '&cpage=' . ( $members_pagination['cpage'] - 1 ) . '" title="Go to the previous page" class="prev-page" ><</a> ';
                            }

                            if ( $stpage > 1)
                                echo '<span>...</span> ';

                            for ( $i = $stpage; $i <= $endpage; $i++ ) {
                                if ( $i == $members_pagination['cpage'] ) {
                                    echo '<span class="current" style="margin: 0px 7px 0px 3px;"><strong>' . $i . '</strong></span>';
                                } else {
                                    echo '<a href="' . $URL . '&cpage=' . $i . '">' . $i . '</a> ';
                                }
                            }

                            if ( $endpage < $pagescount )
                                echo '<span>...</span> ';

                            if ( $members_pagination['cpage'] < $pagescount ) {
                                // next
                                echo '<a href="' . $URL . '&cpage=' . ( $members_pagination['cpage'] + 1 ) . '" title="Go to the next page" class="next-page" >></a> ';
                                // last
                                echo '<a href="' . $URL . '&cpage=' . $pagescount . '" title="Go to the last page" class="last-page" >>></a> ';
                            }
                        ?>
                        </span>
                    <?php endif;?>
                </div>
            </div>
        </form>

    </div><!--/wrap-->