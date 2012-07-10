<?php
/**
 * SlideDeck YouTube Content Source
 * 
 * More information on this project:
 * http://www.slidedeck.com/
 * 
 * Full Usage Documentation: http://www.slidedeck.com/usage-documentation 
 * 
 * @package SlideDeck
 * @subpackage SlideDeck 2 Pro for WordPress
 * @author dtelepathy
 */

/*
Copyright 2012 digital-telepathy  (email : support@digital-telepathy.com)

This file is part of SlideDeck.

SlideDeck is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

SlideDeck is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with SlideDeck.  If not, see <http://www.gnu.org/licenses/>.
*/

$search_hidden = ( $slidedeck['options']['search_or_user'] == 'user' ) ? ' style="display: none;"' : '';
$username_hidden = ( $slidedeck['options']['search_or_user'] == 'search' ) ? ' style="display: none;"' : '';
?>
<div id="content-source-youtube"> 
    <input type="hidden" name="source[]" value="<?php echo $this->name; ?>" />
    <div class="inner">
        <ul class="content-source-fields">
            <li>
                <?php slidedeck2_html_input( 'options[search_or_user]', $slidedeck['options']['search_or_user'], array( 'type' => 'radio', 'label' => __( "Videos From", $this->namespace ), 'attr' => array( 'class' => 'fancy' ), 'values' => array( 
                    'search' => __( "Search Term", $this->namespace ),
                    'user' => __( "Username", $this->namespace )
                 ) ) ); ?>
            </li>
            <li class="youtube-search"<?php echo $search_hidden; ?>>
                <?php slidedeck2_html_input( 'options[youtube_q]', $slidedeck['options']['youtube_q'], array( 'label' => __( "Search Terms", $this->namespace ), 'attr' => array( 'size' => 20, 'maxlength' => 255 ), 'required' => true ) ); ?>
            </li>
            <li class="youtube-username"<?php echo $username_hidden; ?>>
                <?php slidedeck2_html_input( 'options[youtube_username]', $slidedeck['options']['youtube_username'], array( 'label' => __( "YouTube Username", $this->namespace ), 'attr' => array( 'size' => 20, 'maxlength' => 255 ), 'required' => true ) ); ?>
                <a class="youtube-username-ajax-update button" href="#update"><?php _e( "Update", $this->namespace ); ?></a>
            </li>
            <li class="youtube-username"<?php echo $username_hidden; ?>>
                <?php if( $playlists_select ): ?>
                <div id="youtube-user-playlists">
                    <?php echo $playlists_select; ?>
                </div>
                <?php endif; ?>
            </li>
        </ul>
    </div>
</div>
