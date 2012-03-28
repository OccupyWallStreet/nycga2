<div id="wpbody-content">
	<div class="wrap">
		<div id="icon-options-general" class="icon32"><br /></div>
			
			<h2><?php echo $themename; ?> Options</h2>
			
				<br />
			
    			<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

					<table class="widefat" id="active-plugins-table" cellspacing="0">
					
						<tbody class="plugins">

						<?php foreach ($options as $value) { ?>
						
							<?php if($value['type'] == "section") { ?>
							
								<tr class="active"><td colspan="3"><h3><?php echo $value['name']; ?></h3></td></tr>
							
							<?php } 
								switch ( $value['type'] ) {
								case 'sub-section' :
							?>
								
								<tr>
									<td class="plugin-update" colspan="3" style="text-align:left; border-top:0;">
										<?php echo $value['name']; ?>
									</td>
								</tr>
							
							<?php
								break;
								case 'text':
							?>
								<tr valign="top">
									<th scope="row">
										<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
									</th>
									<td>
										<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes( get_settings( $value['id'] ) ); } else { echo stripslashes( $value['std'] ); } ?>" size="90" />
										<p class="setting-description"><?php echo $value['description']; ?></p>
									</td>
									<td align="right">
										<?php if( $value['icon'] != "" ) : ?>
										<img src="<?php bloginfo( 'template_directory' ); ?>/inc/functions/options/images/<?php echo $value['icon']; ?>" alt="<?php echo $value['name']; ?>" style="border:1px solid #CCCCCC; padding:1px;" />
										<?php endif; ?>
									</td>
								</tr>
							
							<?php										
								break;
								case 'select' :
							?>
							
								<tr valign="middle">
									<td style="font-weight:bold">
										<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
									</td>
									<td>
										<select name="<?php echo $value['id']; ?>">
											<?php foreach ($value['options'] as $option) { ?>
                								<option<?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
                							<?php } ?>
										</select>
										<p class="setting-description"><?php echo $value['description']; ?></p>
									</td>
									<td align="right">
										<?php if( $value['icon'] != "" ) : ?>
										<img src="<?php bloginfo( 'template_directory' ); ?>/inc/functions/options/images/<?php echo $value['icon']; ?>" alt="<?php echo $value['name']; ?>" style="border:1px solid #CCCCCC; padding:1px;" />
										<?php endif; ?>
									</td>
								</tr>
								
							<?php						
								break;
								case 'checkbox' :
																
								if( get_settings($value['id'] ) ) { 
									$checked = " checked=\"checked\""; 
								} else { 
									$checked = ""; 
								}
							?>
							
								<tr valign="middle">
									<td style="font-weight:bold">
										<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
									</td>
									<td>
										<input type="checkbox" class="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true"<?php echo $checked; ?> />
										<p class="setting-description"><?php echo $value['description']; ?></p>
									</td>
									<td align="right">
										<?php if( $value['icon'] != "" ) : ?>
										<img src="<?php bloginfo( 'template_directory' ); ?>/inc/functions/options/images/<?php echo $value['icon']; ?>" alt="<?php echo $value['name']; ?>" style="border:1px solid #CCCCCC; padding:1px;" />
										<?php endif; ?>
									</td>
								</tr>
								
							<?php
								break;
								case 'textarea' :
							?>
							
								<tr valign="top">
									<th scope="row">
										<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
									</th>
									<td>
										<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" cols="90" rows="4"><?php if ( get_settings( $value['id'] ) != "") { echo stripslashes( get_settings( $value['id'] ) ); } else { echo stripslashes( $value['std'] ); } ?></textarea>
										<p class="setting-description"><?php echo $value['description']; ?></p>
									</td>
									<td align="right">
										<?php if( $value['icon'] != "" ) : ?>
										<img src="<?php bloginfo( 'template_directory' ); ?>/inc/functions/options/images/<?php echo $value['icon']; ?>" alt="<?php echo $value['name']; ?>" style="border:1px solid #CCCCCC; padding:1px;" />
										<?php endif; ?>
									</td>
								</tr>
							
							<?php
								break;
								}
							?>
					<?php } ?>
					
					</tbody>
					
				</table>
				
				<p class="submit">
					<input name="save" type="submit" value="Save changes" class="button-primary" />    
					<input type="hidden" name="action" value="save" />
				</p>
								
			</form>
	</div>
</div>