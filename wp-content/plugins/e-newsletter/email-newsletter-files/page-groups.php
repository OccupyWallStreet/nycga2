<?php

    $groups = $this->get_groups();

    //Display status message
    if ( isset( $_GET['updated'] ) ) {
        ?><div id="message" class="updated fade"><p><?php echo urldecode( $_GET['dmsg'] ); ?></p></div><?php
    }

?>
    <script type="text/javascript">
        jQuery( document ).ready( function() {
            //Save Newsletter action
            jQuery( "#add_group" ).click( function() {
                if ( "" == jQuery( "#group_name" ).val() ) {
                    alert( 'Please write Group name' );
                    return false;
                }

                jQuery( "#newsletter_action" ).val( "create_group" );
                jQuery( "#create_group" ).submit();
            });

            var group_name      = "";
            var group_public    = "";


            jQuery.fn.editGroup = function ( id ) {
//            alert(group_name);

                if ( "<?php _e( 'Edit', 'email-newsletter' ) ?>" == jQuery( this ).val() ) {
                    group_name = jQuery( "#group_name_block_" + id ).html();
                    group_name = group_name.replace(/(^\s+)|(\s+$)/g, "");

                    jQuery( "#group_name_block_" + id ).html( '<input type="text" name="edit_group_name" size="30" id="edit_group_name"  value="' + group_name + '" /><input type="hidden" name="group_id" value="' + id + '" />' );

                    group_public = jQuery( "#public_block_" + id ).html();
                    group_public = group_public.replace(/(^\s+)|(\s+$)/g, "");

                    if ( "Yes" == group_public )
                        jQuery( "#public_block_" + id ).html( '<input type="checkbox" name="edit_public" id="public" value="1" checked="checked" />' );
                    else
                        jQuery( "#public_block_" + id ).html( '<input type="checkbox" name="edit_public" id="public" value="1" />' );


                    jQuery( '#edit_group input[type="button"]' ).attr( 'disabled', true );

                    jQuery( this ).val('<?php _e( 'Close', 'email-newsletter' ) ?>');
                    jQuery( this ).attr( 'disabled', false );

                    jQuery( "#save_block_" + id ).html( '<input type="button" name="save_button" onClick="jQuery(this).saveGroup();" value="<?php _e( 'Save', 'email-newsletter' ) ?>" />' );

                    return;
                }

                if ( "<?php _e( 'Close', 'email-newsletter' ) ?>" == jQuery( this ).val() ) {
                    jQuery( "#group_name_block_" + id ).html( group_name );
                    jQuery( "#public_block_" + id ).html( group_public );

                    jQuery( this ).val('<?php _e( 'Edit', 'email-newsletter' ) ?>');
                    jQuery( '#edit_group input[type="button"]' ).attr( 'disabled', false );

                     jQuery( "#save_block_" + id ).html( '' );

                    return;
                }


            };


            jQuery.fn.saveGroup = function ( ) {
                if ( "" == jQuery( "#edit_group_name" ).val() ) {
                    alert( 'Please write Group name' );
                    return false;
                }

                jQuery( "#newsletter_action2" ).val( "edit_group" );
                jQuery( "#edit_group" ).submit();
            };


            jQuery.fn.deleteGroup = function ( id ) {
                jQuery( "#newsletter_action2" ).val( "delete_group" );
                jQuery( "#group_id" ).val( id );
                jQuery( "#edit_group" ).submit();
            };

        });
    </script>


    <div class="wrap">
        <h2><?php _e( 'Groups', 'email-newsletter' ) ?></h2>
        <p><?php _e( 'This page contains the list of all groups.', 'email-newsletter' ) ?></p>

        <h3><?php _e( 'Create New Group', 'email-newsletter' ) ?></h3>
        <form method="post" action="" name="create_group" id="create_group" >
            <input type="hidden" name="newsletter_action" id="newsletter_action" value="" />
            <table class="form-table">
                <tr>
                    <td>
                        <?php _e( 'Group Name:', 'email-newsletter' ) ?><span class="required">*</span>
                        <input type="text" class="input" name="group_name" id="group_name" value="" size="30" />
                        <label>
                            <input type="checkbox" name="public" id="public" value="1" /> <?php _e( 'public users can join this group', 'email-newsletter' ) ?>
                        </label>
                        <input type="button" name="save" id="add_group" value="<?php _e( 'Add Group', 'email-newsletter' ) ?>" />
                    </td>
                </tr>
            </table>
        </form>



        <h3><?php _e( 'List of Groups:', 'email-newsletter' ) ?></h3>
        <form method="post" action="" name="edit_group" id="edit_group" >
            <input type="hidden" name="newsletter_action" id="newsletter_action2" value="" />
            <input type="hidden" name="group_id" id="group_id" value="" />
            <table width="700px" class="widefat post fixed" style="width:95%;">
                <thead>
                    <tr>
                        <th>
                            <?php _e( 'Group Name', 'email-newsletter' ) ?>
                        </th>
                        <th>
                            <?php _e( 'Public', 'email-newsletter' ) ?>
                        </th>
                        <th>
                            <?php _e( 'Members', 'email-newsletter' ) ?>
                        </th>
                        <th>
                            <?php _e( 'Actions', 'email-newsletter' ) ?>
                        </th>
                    </tr>
                </thead>
            <?php
            $i = 0;
            if ( $groups )
                foreach( $groups as $group ) {
                    if ( $i % 2 == 0 )
                        echo "<tr class='alternate'>";
                    else
                        echo "<tr class='' >";

                    $i++;
            ?>
                    <td style="vertical-align: middle;">
                        <span id="group_name_block_<?php echo $group['group_id'];?>">
                            <?php echo $group['group_name']; ?>
                        </span>
                    </td>
                    <td style="vertical-align: middle;">
                        <span id="public_block_<?php echo $group['group_id'];?>">
                            <?php
                            if ( "1" == $group['public'] )
                                _e( 'Yes', 'email-newsletter' );
                            else
                                _e( 'No', 'email-newsletter' );
                            ?>
                        </span>
                    </td>
                    <td style="vertical-align: middle;">
                        <?php echo count( $this->get_members_of_group( $group['group_id'] ) ); ?>
                    </td>
                    <td style="vertical-align: middle;">
                        <input type="button" id="edit_button_<?php echo $group['group_id'];?>" value="<?php _e( 'Edit', 'email-newsletter' ) ?>" onclick="jQuery(this).editGroup( <?php echo $group['group_id'];?> );" />
                        <span id="save_block_<?php echo $group['group_id'];?>"></span>
                        <input type="button" value="<?php _e( 'Delete', 'email-newsletter' ) ?>" onclick="jQuery(this).deleteGroup( <?php echo $group['group_id'];?> );" />
                    </td>
                </tr>
            <?php
                }
            ?>
            </table>
        </form>
    </div><!--/wrap-->