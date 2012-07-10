<?php
/**
 * SlideDeck Widget control form
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
?>
<p><?php _e( "Display a SlideDeck in a widget area. <em><strong>NOTE:</strong> since most widget areas are narrow sidebars, your SlideDeck may not appear correctly. We only recommend placing SlideDecks in wider widget areas like headers and footers.</em>", $namespace ); ?></p>
<p><label><strong><?php _e( "Choose a SlideDeck", $namespace ); ?>:</strong><br />
<select name="<?php echo $this->get_field_name( 'slidedeck_id' ); ?>" id="<?php echo $this->get_field_id( 'slidedeck_id' ); ?>" class="widefat">
    <?php foreach( (array) $slidedecks as $slidedeck ): ?>
    <option value="<?php echo $slidedeck['id']; ?>"<?php echo $slidedeck_id == $slidedeck['id'] ? ' selected="selected"' : ''; ?>><?php echo $slidedeck['title']; ?></option>
    <?php endforeach; ?>
</select>
</label></p>
<p>
    <label>
        <input type="checkbox" value="1" name="<?php echo $this->get_field_name( $namespace . '_deploy_as_iframe' ); ?>" id="<?php echo $this->get_field_id( $namespace . '_deploy_as_iframe'); ?>"<?php if( $deploy_as_iframe ) echo ' checked="checked"'; ?> />
        <?php _e( "Deploy SlideDeck using an iframe", $namespace ); ?>
    </label>
</p>
