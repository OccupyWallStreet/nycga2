<?php
/**
 * Custom Post Type Module
 *
 * @version $Id: custompost_module.php 523481 2012-03-25 19:49:08Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	class DW_CustomPost extends DWModule {
		public static $plugin = array( 'custom_post_type' => FALSE, 'custom_taxonomy' => FALSE );
		protected static $post_types;
		protected static $type = 'custom';

		public static function admin() {
			parent::admin();
			self::customPosts();
			self::customTax();
			self::customArchive();
		}

		public static function customArchive() {
			$DW = $GLOBALS['DW'];

			if ( function_exists('is_post_type_archive') && count(self::$post_types) > 0 ) {
				self::$type = 'complex';
				$new_name = 'cp_archive';
				$title = 'Custom Post Type Archives';
				$question = 'Show widget on Custom Post Type Archives';
				$except = 'Except for';

				$list = array();
				foreach ( self::$post_types as $key => $value ) {
					$list[$key] = $value->label;
				}

				self::mkGUI(self::$type, $title, $question, FALSE, $except, $list, $new_name);
			}
		}

		public static function customPosts() {
			$DW = $GLOBALS['DW'];

			$args = array(
				'public'   => TRUE,
				'_builtin' => FALSE
			);

			// Custom Post Type
			self::$post_types = get_post_types($args, 'objects', 'and');
			foreach ( self::$post_types as $type => $ctid ) {
				// Prepare
				self::$opt = $DW->getDWOpt($_GET['id'], $type);

				// -- Childs
				/* if ( $ctid->hierarchical ) {
					$opt_custom_childs = $DW->getDWOpt($_GET['id'], $type . '-childs');
				} else {
					unset($opt_custom_childs);
				}

				$loop = new WP_Query( array('post_type' => $type, 'posts_per_page' => -1) );
				if ( $loop->post_count > DW_LIST_LIMIT ) {
					$custom_condition_select_style = DW_LIST_STYLE;
				}

				$cpmap = self::getCPostChilds($type, array(), 0, array()); */
				$tax_list = get_object_taxonomies($type, 'objects');

				// Output
				echo '<input type="hidden" name="post_types[]" value="' . $type . '" />';
				echo '<h4><b>' . $ctid->label . '</b> ' . ( self::$opt->count > 0 ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . ( $DW->wpml ? DW_WPML::$icon : '' ) . '</h4>';
				echo '<div class="dynwid_conf">';
				echo __('Show widget on', DW_L10N_DOMAIN) . ' ' . $ctid->label . '? ' . ( ($ctid->hierarchical || count($tax_list) > 0) ? '<img src="' . $DW->plugin_url . 'img/info.gif" alt="info" onclick="divToggle(\'custom_' . $type . '\');" />' : '' ) . '<br />';
				echo '<div>';
				echo '<div id="custom_' . $type . '" class="infotext">';
				echo ( $ctid->hierarchical ? '<p>' . DW_Page::infoText() . '</p>' : '' );
				echo ( (count($tax_list) > 0) ? '<p>' . __('All exceptions (Titles and Taxonomies) work in a logical OR condition. That means when one of the exceptions is met, the exception rule is applied.', DW_L10N_DOMAIN) . '</p>' : '' );
				echo '</div>';
				echo '</div>';

				self::GUIOption($type);
				echo '<br />'; 

				/* if ( isset($opt_custom_childs) ) {
					$DW->dumpOpt($opt_custom_childs);
				}

				if ( $loop->post_count > 0 ) {
					echo __('Except for', DW_L10N_DOMAIN) . ':<br />';
					echo '<div id="' . $type . '-select" class="condition-select" ' . ( (isset($custom_condition_select_style)) ? $custom_condition_select_style : '' ) . '>';

					echo '<div style="position:relative;left:-15px">';

					if ( isset($opt_custom_childs) ) {
						$childs = $opt_custom_childs->act;
					} else {
						$childs = array();
					}
					self::prtCPost($type, $ctid, $cpmap, self::$opt->act, $childs);

					echo '</div>'; 
					echo '</div>'; 
				} */

				// Taxonomy in Custom Post Type
				foreach ( $tax_list as $tax_type ) {
					// Prepare
					$opt_tax = $DW->getDWOpt($_GET['id'], $type . '-tax_' . $tax_type->name);
					if ( $tax_type->hierarchical ) {
						$opt_tax_childs = $DW->getDWOpt($_GET['id'], $type . '-tax_' . $tax_type->name . '-childs');
					} else {
						unset($opt_tax_childs);
					}

					$tax = get_terms($tax_type->name, array('get' => 'all'));
					if ( count($tax) > 0 ) {
						if ( count($tax) > DW_LIST_LIMIT ) {
							$tax_condition_select_style = DW_LIST_STYLE;
						}

						$tree = self::getTaxChilds($tax_type->name, array(), 0, array());

						echo '<br />';
						$DW->dumpOpt($opt_tax);
						if ( isset($opt_tax_childs) ) {
							$DW->dumpOpt($opt_tax_childs);
						}

						echo '<input type="hidden" name="tax_list[]" value="' . $type . '-tax_' . $tax_type->name . '" />';
						echo __('Except for', DW_L10N_DOMAIN) . ' ' . $tax_type->label . ':<br />';
						echo '<div id="' . $type . '-tax_' . $tax_type->name . '-select" class="condition-select" ' . ( (isset($tax_condition_select_style)) ? $tax_condition_select_style : '' ) . '>';
						echo '<div style="position:relative;left:-15px">';
						if (! isset($opt_tax_childs) ) {
							$childs = FALSE;
						} else {
							$childs = $opt_tax_childs->act;
						}
						self::prtTax($tax_type->name, $tree, $opt_tax->act, $childs, $type . '-tax_' . $tax_type->name);
						echo '</div>';
						echo '</div>';
					}
				}

				self::GUIFooter();
			}
		}

		public static function customTax() {
			$DW = $GLOBALS['DW'];

			$args = array(
				'public'   => TRUE,
				'_builtin' => FALSE
			);

			if ( function_exists('is_tax') ) {
				$taxlist = get_taxonomies($args, 'objects', 'and');

				if ( count($taxlist) > 0 ) {
					foreach ( $taxlist as $tax_id => $tax ) {
						
						// Getting the linked post type : Only Pages and CPT supported
						$cpt_label = array();
						foreach ( $tax->object_type as $obj ) {
							if ( $obj == 'page' ) {
								$cpt_label[ ] = _('Pages');
							} else if ( isset(self::$post_types[$obj]) ) {
								$cpt_label[ ] = self::$post_types[$obj]->label;
							}
						}
						
						if ( count($cpt_label) > 0 ) {
							$ct = 'tax_' . $tax_id;
							$ct_archive_yes_selected = 'checked="checked"';
							$opt_ct_archive = $DW->getDWOpt($_GET['id'], $ct);
							if ( $tax->hierarchical ) {
								$opt_ct_archive_childs = $DW->getDWOpt($_GET['id'], $ct . '-childs');
							}
	
							$t = get_terms($tax->name, array('get' => 'all'));
							if ( count($t) > DW_LIST_LIMIT ) {
								$ct_archive_condition_select_style = DW_LIST_STYLE;
							}
	
							$tree = self::getTaxChilds($tax->name, array(), 0, array());
	
							echo '<h4><b>' . $tax->label . ' ' . _('archive') . '</b> (<em>' . implode(', ', $cpt_label) . '</em>)' . ( ($opt_ct_archive->count > 0) ? ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" />' : '' ) . '</h4>';
							echo '<div class="dynwid_conf">';
							echo __('Show widget on', DW_L10N_DOMAIN) . ' ' . $tax->label . ' ' . _('archive') . '?' . ( ($tax->hierarchical || count($t) > 0) ? ' <img src="' . $DW->plugin_url . 'img/info.gif" alt="info" onclick="divToggle(\'custom_' . $ct . '\');" />' : '' ) . '<br />';
							echo '<input type="hidden" name="dw_taxonomy[]" value="' . $tax_id . '" />';
							$DW->dumpOpt($opt_ct_archive);
							if ( isset($opt_ct_archive_childs) ) {
								$DW->dumpOpt($opt_ct_archive_childs);
							}
	
							echo '<div>';
							echo '<div id="custom_' . $ct . '" class="infotext">';
							echo ( $tax->hierarchical ? '<p>' . DW_Page::infoText() . '</p>' : '' );
							echo ( (count($t) > 0) ? '<p>' . __('All exceptions work in a logical OR condition. That means when one of the exceptions is met, the exception rule is applied.', DW_L10N_DOMAIN) . '</p>' : '' );
							echo '</div>';
							echo '</div>';
	
							echo '<input type="radio" name="' . $ct . '" value="yes" id="' . $ct . '-yes" ' . ( ($opt_ct_archive->selectYes()) ? $opt_ct_archive->checked : '' ) . ' /> <label for="' . $ct . '-yes">' . __('Yes') . '</label> ';
							echo '<input type="radio" name="' . $ct . '" value="no" id="' . $ct . '-no" ' . ( ($opt_ct_archive->selectNo()) ? $opt_ct_archive->checked : '' ) . ' /> <label for="' . $ct . '-no">' . __('No') . '</label><br />';
	
							if ( count($t) > 0 ) {
								echo __('Except for', DW_L10N_DOMAIN) . ':<br />';
								echo '<div id="' . $ct . '-select" class="condition-select" ' . ( (isset($ct_archive_condition_select_style)) ? $ct_archive_condition_select_style : '' ) . '>';
								echo '<div style="position:relative;left:-15px">';
								if (! isset($opt_ct_archive_childs) ) {
									$childs = FALSE;
								} else {
									$childs = $opt_ct_archive_childs->act;
								}
								self::prtTax($tax->name, $tree, $opt_ct_archive->act, $childs, $ct);
								echo '</div>';
								echo '</div>';
							}
							// echo '</div><!-- end dynwid_conf -->';
							self::GUIFooter();
						}
					}
				}
			}
		}

/*		public static function getCPostChilds($type, $arr, $id, $i) {
			$post = get_posts('post_type=' . $type . '&post_parent=' . $id . '&posts_per_page=-1');

			foreach ($post as $p ) {
				if (! in_array($p->ID, $i) ) {
					$i[ ] = $p->ID;
					$arr[$p->ID] = array();
					$arr[$p->ID] = self::getCPostChilds($type, $arr[$p->ID], $p->ID, $i);
				}
			}
			return $arr;
		} */

/*		public static function prtCPost($type, $ctid, $posts, $posts_act, $posts_childs_act) {
			$DW = &$GLOBALS['DW'];

			foreach ( $posts as $pid => $childs ) {
				$run = TRUE;

				if ( $DW->wpml ) {
					include_once(DW_MODULES . 'wpml_module.php');
					$wpml_id = DW_WPML::getID($pid, 'post_' . $type);
					if ( $wpml_id > 0 && $wpml_id <> $pid ) {
						$run = FALSE;
					}
				}

				if ( $run ) {
					$post = get_post($pid);

					echo '<div style="position:relative;left:15px;">';
					echo '<input type="checkbox" id="' . $type . '_act_' . $post->ID . '" name="' . $type . '_act[]" value="' . $post->ID . '" ' . ( isset($posts_act) && count($posts_act) > 0 && in_array($post->ID, $posts_act) ? 'checked="checked"' : '' ) . ' onchange="chkCPChild(\'' . $type . '\',' . $pid . ')" /> <label for="' . $type . '_act_' . $post->ID . '">' . $post->post_title . '</label><br />';

					if ( $ctid->hierarchical ) {
						echo '<div style="position:relative;left:15px;">';
						echo '<input type="checkbox" id="' . $type . '_childs_act_' . $pid . '" name="' . $type . '_childs_act[]" value="' . $pid . '" ' . ( isset($posts_childs_act) && count($posts_childs_act) > 0 && in_array($pid, $posts_childs_act) ? 'checked="checked"' : '' ) . ' onchange="chkCPParent(\'' . $type . '\',' . $pid . ')" /> <label for="' . $type . '_childs_act_' . $pid . '"><em>' . __('All childs', DW_L10N_DOMAIN) . '</em></label><br />';
						echo '</div>';
					}

					if ( count($childs) > 0 ) {
						self::prtCPost($type, $ctid, $childs, $posts_act, $posts_childs_act);
					}
					echo '</div>';
				}
			}
		} */

		public static function getTaxChilds($term, $arr, $id, $i) {
			$tax = get_terms($term, array('hide_empty' => FALSE, 'parent' => $id));

			foreach ($tax as $t ) {
				if (! in_array($t->term_id, $i) && $t->parent == $id ) {
					$i[ ] = $t->term_id;
					$arr[$t->term_id] = array();
					$a = &$arr[$t->term_id];
					$a = self::getTaxChilds($term, $a, $t->term_id, $i);
				}
			}

			return $arr;
		}

		public static function prtTax($tax, $terms, $terms_act, $terms_childs_act, $prefix) {
			$DW = &$GLOBALS['DW'];

			foreach ( $terms as $pid => $childs ) {
				$run = TRUE;

				if ( $DW->wpml ) {
					include_once(DW_MODULES . 'wpml_module.php');
					$wpml_id = DW_WPML::getID($pid, 'tax_' . $tax);
					if ( $wpml_id > 0 && $wpml_id <> $pid ) {
						$run = FALSE;
					}
				}

				if ( $run ) {
					$term = get_term_by('id', $pid, $tax);

					echo '<div style="position:relative;left:15px;">';
					echo '<input type="checkbox" id="' . $prefix . '_act_' . $pid . '" name="' . $prefix . '_act[]" value="' . $pid . '" ' . ( isset($terms_act) && count($terms_act) > 0 && in_array($pid, $terms_act) ? 'checked="checked"' : '' ) . ' onchange="chkChild(\'' . $prefix . '\', ' . $pid . ')" /> <label for="' . $prefix . '_act_' . $pid . '">' . $term->name . '</label><br />';;

					if ( $terms_childs_act !== FALSE ) {
						echo '<div style="position:relative;left:15px;">';
						echo '<input type="checkbox" id="' . $prefix . '_childs_act_' . $pid . '" name="' . $prefix . '_childs_act[]" value="' . $pid . '" ' . ( isset($terms_childs_act) && count($terms_childs_act) > 0 && in_array($pid, $terms_childs_act) ? 'checked="checked"' : '' ) . ' onchange="chkParent(\'' . $prefix . '\', ' . $pid . ')" /> <label for="' . $prefix . '_childs_act_' . $pid . '"><em>' . __('All childs', DW_L10N_DOMAIN) . '</em></label><br />';
						echo '</div>';

						if ( count($childs) > 0 ) {
							self::prtTax($tax, $childs, $terms_act, $terms_childs_act, $prefix);
						}
					}
					echo '</div>';
				}
			}
		}

		public static function registerOption() {
			$option = array( 'cp_archive'	=> 'Custom Post Type Archives' );

			// Adding Custom Post Types to $DW->dwoptions
			$args = array(
				'public'   => TRUE,
				'_builtin' => FALSE
			);
			$post_types = get_post_types($args, 'objects', 'and');
			foreach ( $post_types as $ctid ) {
				$option[key($post_types)] = $ctid->label;
			}

			// Adding Custom Taxonomies to $DW->dwoptions
			$taxonomy = get_taxonomies($args, 'objects', 'and');
			foreach ( $taxonomy as $tax_id => $tax ) {
				$option['tax_' . $tax_id] = $tax->label;
			}
			parent::registerOption($option);
		}
	}
?>