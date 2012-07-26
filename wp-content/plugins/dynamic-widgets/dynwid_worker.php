<?php
/**
 * dynwid_worker.php - The worker does the actual work.
 *
 * @version $Id: dynwid_worker.php 528159 2012-04-06 15:53:56Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	$DW->message('Worker START');
	$DW->message('WhereAmI = ' . $DW->whereami);

	// Registering Custom Post Type & Custom Taxonomy to $DW (object overload)
	include(DW_MODULES . 'custompost_module.php');
	DWModule::registerPlugin(DW_CustomPost::$plugin);

	// Template
	$tpl = get_page_template();
	if ( $DW->whereami == 'pods' ) {
		global $pod_page_exists;
		if (! empty($pod_page_exists['page_template']) ) {
			$tpl = $pod_page_exists['page_template'];
		}
	}
	$DW->template = basename($tpl);
	$DW->message('Template = ' . $DW->template);

	// WPML Plugin support
	include_once(DW_MODULES . 'wpml_module.php');
	if ( DW_WPML::detect(FALSE) ) {
		$curlang = DW_WPML::detectLanguage();
	}

	// QT Plugin support
	include_once(DW_MODULES . 'qt_module.php');
	if ( DW_QT::detect(FALSE) ) {
		$curlang = DW_QT::detectLanguage();
	}

	// Hide title
	$dw_hide_title = get_option('dw_hide_title');

  foreach ( $sidebars as $sidebar_id => $widgets ) {
    // Only processing active sidebars with widgets
    if ( $sidebar_id != 'wp_inactive_widgets' && count($widgets) > 0 && is_array($widgets) ) {
      foreach ( $widgets as $widget_key => $widget_id ) {
        // Check if the widget has options set
        if ( in_array($widget_id, $DW->dynwid_list) ) {
          $act = array();
          $opt = $DW->getOpt($widget_id, $DW->whereami, FALSE);
          $DW->message('Number of rules to check for widget ' . $widget_id . ': ' . count($opt));

        	$init = array_merge(array('display'), $DW->overrule_maintype);
        	foreach ( $init as $m ) {
        		$$m = TRUE;
        	}

          foreach ( $opt as $condition ) {
            if ( empty($condition->name) && $condition->value == '0' && $condition->maintype == $DW->whereami ) {
              $DW->message('Default for ' . $widget_id . ' set to FALSE (rule D1)');
              $display = FALSE;
              $other = TRUE;
              break;
            } else if (! in_array($condition->maintype, $DW->overrule_maintype) ) {
              // Get default value
              if ( $condition->name == 'default' ) {
                $default = $condition->value;
              	if ( $default == '0' ) {
              		$DW->message('Default for ' . $widget_id . ' set to FALSE (rule D2)');
              		$display = FALSE;
              		$other = TRUE;
              	} else {
              		$DW->message('Default for ' . $widget_id . ' set to TRUE (rule D3)');
              		$display = TRUE;
              		$other = FALSE;
              	}
              } else {
                $act[ ] = $condition->name;
              }
            } else {
            	foreach ( $DW->overrule_maintype as $m ) {
            		if ( $condition->maintype == $m && $condition->name == 'default' ) {
            			$DW->message('Default for ' . $widget_id . ' set to ' . ( (bool) ($condition->value) ? 'TRUE' : 'FALSE' ) . ' by ' . $m . ' (rule OM1)');
            			$$m = (bool) $condition->value;
            		}
            	}
            }
          }

          // Act the condition(s) when there are options set
          if ( count($opt) > 0 ) {
            // Role exceptions
          	if ( isset($role) ) {
          		foreach ( $opt as $condition ) {
          			if ( $condition->maintype == 'role' && in_array($condition->name, $DW->userrole) ) {
          				$DW->message('Exception triggered for Role, sets display to ' . ( (bool) ($condition->value) ? 'TRUE' : 'FALSE' ) . ' (rule ER1)');
          				$role = (bool) $condition->value;;
          			}
          		}
          	}

            // Date exceptions
						if (! $date ) {
							$dates = array();
							foreach ( $opt as $condition ) {
								if ( $condition->maintype == 'date' ) {
									switch ( $condition->name ) {
										case 'date_start':
											$date_start = $condition->value;
											break;

										case 'date_end':
											$date_end = $condition->value;
											break;
									}
								}
							}
							$now = time();
							if (! empty($date_end) ) {
								@list($date_end_year, $date_end_month, $date_end_day) = explode('-', $date_end);
								if ( mktime(23, 59, 59, $date_end_month, $date_end_day, $date_end_year) > $now ) {
									$date = TRUE;
									$DW->message('End date is in the future, sets Date to TRUE (rule EDT1)');
									if (! empty($date_start) ) {
										@list($date_start_year, $date_start_month, $date_start_day) = explode('-', $date_start);
										if ( mktime(0, 0, 0, $date_start_month, $date_start_day, $date_start_year) > $now ) {
											$date = FALSE;
											$DW->message('From date is in the future, sets Date to FALSE (rule EDT2)');
										}
									}
								}
							} else if (! empty($date_start) ) {
								@list($date_start_year, $date_start_month, $date_start_day) = explode('-', $date_start);
								if ( mktime(0, 0, 0, $date_start_month, $date_start_day, $date_start_year) < $now ) {
									$date = TRUE;
									$DW->message('From date is in the past, sets Date to TRUE (rule EDT3)');
								}
							}
						}

          	// WPML
          	if ( isset($wpml) && isset($curlang) ) {
          		foreach ( $opt as $condition ) {
          			if ( $condition->maintype == 'wpml' && $condition->name == $curlang ) {
          				(bool) $wpml_tmp = $condition->value;
          			}
          		}

          		if ( isset($wpml_tmp) && $wpml_tmp != $wpml ) {
          			$DW->message('Exception triggered for WPML language, sets display to ' . ( ($wpml_tmp) ? 'TRUE' : 'FALSE' ) . ' (rule EML1)');
          			$wpml = $wpml_tmp;
          		}
          	}
          	unset($wpml_tmp);

          	// QTranslate
          	if ( isset($qt) && isset($curlang) ) {
          		foreach ( $opt as $condition ) {
          			if ( $condition->maintype == 'qt' && $condition->name == $curlang ) {
          				(bool) $qt_tmp = $condition->value;
          			}
          		}

          		if ( isset($qt_tmp) && $qt_tmp != $qt ) {
          			$DW->message('Exception triggered for QT language, sets display to ' . ( ($qt_tmp) ? 'TRUE' : 'FALSE' ) . ' (rule EQT1)');
          			$qt = $qt_tmp;
          		}
          	}
          	unset($qt_tmp);

          	// Browser and Template
          	foreach ( $opt as $condition ) {
          		if ( $condition->maintype == 'browser' && $condition->name == $DW->useragent ) {
          			(bool) $browser_tmp = $condition->value;
          		} else if ( $condition->maintype == 'tpl' && $condition->name == $DW->template ) {
          			(bool) $tpl_tmp = $condition->value;
          		}
          	}

          	if ( isset($browser_tmp) && $browser_tmp != $browser ) {
          		$DW->message('Exception triggered for browser, sets display to ' . ( ($browser_tmp) ? 'TRUE' : 'FALSE' ) . ' (rule EB1)');
          		$browser = $browser_tmp;
          	}
          	unset($browser_tmp);

          	if ( isset($tpl_tmp) && $tpl_tmp != $tpl ) {
          		$DW->message('Exception triggered for template, sets display to ' . ( ($tpl_tmp) ? 'TRUE' : 'FALSE' ) . ' (rule ETPL1)');
          		$tpl = $tpl_tmp;
          	}
          	unset($tpl_tmp);

            // For debug messages
            $e = ( isset($other) && $other ) ? 'TRUE' : 'FALSE';

            // Display exceptions (custom post type)
            if ( $DW->custom_post_type ) {
              // Custom Post Type behaves the same as a single post
               $post = $GLOBALS['post'];
              if ( count($act) > 0 ) {
                $id = $post->ID;
                $DW->message('PostID: ' . $id);
                if ( $DW->wpml ) {
                  $id = DW_WPML::getID($id, 'post_' . $DW->whereami);
                  $DW->message('WPML ObjectID: ' . $id);
                }

              	$act_custom = array();
              	$act_childs = array();
              	/* foreach ( $opt as $condition ) {
              		if ( $condition->name != 'default' ) {
              			switch ( $condition->maintype ) {
              				case $DW->whereami:
              					$act_custom[ ] = $condition->name;
              					break;

              				case $DW->whereami . '-childs':
              					$act_childs[ ] = $condition->name;
              					break;
              			}
              		}
              	} */

              	// Taxonomies within CPT
              	$act_tax = array();
              	$act_tax_childs = array();
              	foreach ( get_object_taxonomies($DW->whereami) as $t ) {
              		$m = $DW->whereami . '-tax_' . $t;
              		foreach ( $opt as $condition ) {
              			if ( $condition->maintype == $m ) {
              				if (! key_exists($t, $act_tax) ) {
              					$act_tax[$t] = array();
              					$act_tax_childs[$t] = array();
              				}
              			}

             				if ( $condition->name != 'default' ) {
             					switch ( $condition->maintype ) {
             						case $m:
             							$act_tax[$t][ ] = $condition->name;
             							break;
             						case $m . '-childs':
             							$act_tax_childs[$t][ ] = $condition->name;
             							break;
             					} // END switch
             				}
              		} // END $opt
              	} // END object_taxonomies

              	$term = wp_get_object_terms($id, get_object_taxonomies($DW->whereami), array('fields' => 'all'));

                if ( in_array($id, $act_custom) ) {
                  $display = $other;
                  $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ECP1)');
                } else if ( count($act_childs) > 0 ) {
                	$parents = $DW->getParents('post', array(), $id);
                	if ( (bool) array_intersect($act_childs, $parents) ) {
                		$display = $other;
                		$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ECP2)');
                	}
                } else if ( count($act_tax) > 0 ) {
                	// bcause $id has already been moved to default language, term doesn't need to be converted. WPML takes care of default language term
									foreach ( $term as $t ) {
										if ( isset($act_tax[$t->taxonomy]) && is_array($act_tax[$t->taxonomy]) && in_array($t->term_id, $act_tax[$t->taxonomy]) ) {
											$display = $other;
											$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ECP3)');
											break;
										}
										$parents = $DW->getTaxParents($t->taxonomy, array(), $t->term_id);
										if ( isset($act_tax_childs[$t->taxonomy]) && is_array($act_tax_childs[$t->taxonomy]) && (bool) array_intersect($act_tax_childs[$t->taxonomy], $parents) ) {
											$display = $other;
											$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ECP4)');
										}
									}
                }
                unset($act_custom, $act_childs, $act_tax);
              } // END count($act)
            } else if ( $DW->custom_taxonomy ) {		// Custom Taxonomy Archive
            	$wp_query = &$GLOBALS['wp_query'];
            	$taxonomy = $wp_query->get('taxonomy');
            	$term = $wp_query->get_queried_object_id();
            	if ( $DW->wpml ) {
            		$term = DW_WPML::getID($term, $DW->whereami);
            		$DW->message('WPML ObjectID: ' . $term);
            	}

            	$act_custom = array();
							$act_custom_childs = array();
            	foreach ( $opt as $condition ) {
            		if ( $condition->name != 'default' ) {
            			switch ( $condition->maintype ) {
            				case $DW->whereami:
            					$act_custom[ ] = $condition->name;
            				 	break;
            				case $DW->whereami . '-childs':
            						$act_custom_childs[ ] = $condition->name;
            					break;
            			} // switch
            		}
            	}

            	if ( in_array($term, $act_custom) ) {
            		$display = $other;
            		$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ECT1)');
            	} else if ( count($act_custom_childs) > 0 ) {
            		$parents = $DW->getTaxParents($taxonomy, array(), $term);
            		if ( (bool) array_intersect($act_custom_childs, $parents) ) {
            			$display = $other;
            			$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ECT2)');
            		}
            	}
            	unset($act_custom);
            	unset($act_custom_childs);
            } else {
              // no custom post type
              switch ( $DW->whereami ) {
                case 'single':
                	$post = $GLOBALS['post'];
                  $act_author = array();
                  $act_category = array();
                	$act_category_childs = array();
                  $act_post = array();
                  $act_tag = array();
                  $post_category = array();
                  $post_tag = array();

                  // Get the categories from the post
                  $categories = get_the_category();
                  foreach ( $categories as $category ) {
                    $id =  $category->cat_ID;
                    if ( $DW->wpml ) {
                      $id = DW_WPML::getID($id, 'tax_category');
                    }
                    $post_category[ ] = $id;
                  }

                  // Get the tags form the post
                  if ( has_tag() ) {
                    $tags = get_the_tags();
                    foreach ( $tags as $tag ) {
                      $post_tag[ ] = $tag->term_id;
                    }
                  } else {
                    $tags = array();
                  }

                  // Split out the conditions
                  foreach ( $opt as $condition ) {
                    if ( $condition->name != 'default' ) {
                      switch ( $condition->maintype ) {
                        case 'single-author':
                          $act_author[ ] = $condition->name;
                          break;

                        case 'single-category':
                          $act_category[ ] = $condition->name;
                          break;

                        case 'single-category-childs':
                        	$act_category_childs[ ] = $condition->name;
                        	break;

                        case 'single-tag':
                          $act_tag[ ] = $condition->name;
                          break;

                        case 'single-post':
                          $act_post[ ] = $condition->name;
                          break;
                      } // END switch
                    }
                  }

                  /* Author AND Category */
                  if ( count($act_author) > 0 && count($act_category) > 0 ) {
                    // Use of array_intersect to be sure one value in both arrays returns true
                  	if ( in_array($post->post_author, $act_author) ) {
                  		if ( (bool) array_intersect($post_category, $act_category) ) {
            	          $display = $other;
              	        $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ES1)');
                  		} else if ( count($act_category_childs) > 0 ) {
												$parents = $DW->getPostCatParents($post_category);
                  			if ( (bool) array_intersect($act_category_childs, $parents) ) {
                  				$display = $other;
                  				$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ES6)');
                  			}
                  		}
                  	}
                    /* Only Author */
                  } else if ( count($act_author) > 0 && count($act_category == 0) ) {
                    if ( in_array($post->post_author, $act_author) ) {
                      $display = $other;
                      $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ES2)');
                    }
                    /* Only Category */
                  } else if ( count($act_author) == 0 && count($act_category) > 0 ) {
                    if ( (bool) array_intersect($post_category, $act_category) ) {
                      $display = $other;
                      $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ES3)');
                    } else if ( count($act_category_childs) > 0 ) {
                    	$parents = $DW->getPostCatParents($post_category);
                    	if ( (bool) array_intersect($act_category_childs, $parents) ) {
                    		$display = $other;
                    		$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ES7)');
                    	}
                    }
                    /* None or individual checked - individual is not included in the $opt */
                  } else {
                    /* Tags */
                    if ( count($act_tag) > 0 ) {
                      if ( (bool) array_intersect($post_tag, $act_tag) ) {
                        $display = $other;
                        $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ES4)');
                      }
                    }
                    /* Posts */
                    if ( count($act_post) > 0 ) {
                      if ( in_array($post->ID, $act_post) ) {
                        $display = $other;
                        $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ES5)');
                      }
                    }
                  }
                  break;

                case 'front-page':
                	if ( count($act) > 0 ) {
                		$pagenr = ( get_query_var('paged') == 0 ) ? 1 : get_query_var('paged');
                  	if ( in_array($pagenr, $act) ) {
                  		$display = $other;
                    	$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EFP1)');
                    }
                  }
                	break;

                case 'home':
                  if ( count($act) > 0 ) {
                    $home_id = get_option('page_for_posts');
                  	$DW->message('ID = ' . $home_id);
                    if ( $DW->wpml ) {
                      $home_id = DW_WPML::getID($home_id);
                      $DW->message('WPML ObjectID: ' . $home_id);
                    }

                    if ( in_array($home_id, $act) ) {
                      $display = $other;
                      $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EH1)');
                    }
                  }
                  break;

                case 'page':
                  if ( count($act) > 0 ) {
                  	$act_page = array();
                  	$act_childs = array();

                    $post = $GLOBALS['post'];
                    $id = $post->ID;
                  	$DW->message('ID = ' . $id);

		                $page_act_tax = array();
		              	$page_act_tax_childs = array();

                    if ( $DW->wpml ) {
                      $id = DW_WPML::getID($id);
                      $DW->message('WPML ObjectID: ' . $id);
                    }

                  	foreach ( $opt as $condition ) {
                  		if ( $condition->name != 'default' ) {
                  			switch ( $condition->maintype ) {
                  				case 'page':
                  					$act_page[ ] = $condition->name;
                  					break;

                  				case 'page-childs':
                  					$act_childs[ ] = $condition->name;
                  					break;
                  			}
                  		}
                  	}

                    if ( in_array($id, $act_page) ) {
                      $display = $other;
                      $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EP1)');
                    } else if ( count($act_childs) > 0 ) {
                    	$parents = $DW->getParents('page', array(), $id);
                    	if ( (bool) array_intersect($act_childs, $parents) ) {
                    		$display = $other;
                    		$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EP2)');
                    	}
                    } else {
                    	$term = wp_get_object_terms($id, get_object_taxonomies($DW->whereami), array('fields' => 'all'));
		              		if ( count($term) > 0 ) {
												foreach ( get_object_taxonomies($DW->whereami) as $t ) {
		              				$m = $DW->whereami . '-tax_' . $t;
		              				foreach ( $opt as $condition ) {
		              					if ( $condition->maintype == $m ) {
		              						if (! key_exists($t, $page_act_tax) ) {
		              							$page_act_tax[$t] = array();
		              							$page_act_tax_childs[$t] = array();
		              						}
		              					}

		              					if ( $condition->name != 'default' ) {
		              						switch ( $condition->maintype ) {
		              							case $m:
		              								$page_act_tax[$t][ ] = $condition->name;
		              								break;
		              							case $m . '-childs':
		              								$page_act_tax_childs[$t][ ] = $condition->name;
		              								break;
		              						} // END switch
		              					}

		              				} // END $opt
		              			}

		              		} // END count($term)
		              	}

										if (! is_wp_error($term) && ! empty($term) ) {
	                  	foreach ( $term as $t ) {
	                  		if ( isset($page_act_tax[$t->taxonomy]) && is_array($page_act_tax[$t->taxonomy]) && in_array($t->term_id, $page_act_tax[$t->taxonomy]) ) {
	                  			$display = $other;
	                  			$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EP3)');
	                  			break;
	                  		}
	                  		$page_parents = $DW->getTaxParents($t->taxonomy, array(), $t->term_id);
	                  		if ( isset($page_act_tax_childs[$t->taxonomy]) && is_array($page_act_tax_childs[$t->taxonomy]) && (bool) array_intersect($page_act_tax_childs[$t->taxonomy], $page_parents) ) {
	                  			$display = $other;
	                  			$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EP4)');
	                  		}
	                  	}
										}

                  }
                  break;

                case 'author':
                  if ( count($act) > 0 && is_author($act) ) {
                    $display = $other;
                    $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EA1)');
                  }
                  break;

                case 'category':
                  if ( count($act) > 0 ) {
                  	$act_cat = array();
                  	$act_childs = array();

                    $id = get_query_var('cat');
                    $DW->message('CatID: ' . $id);
                    if ( $DW->wpml ) {
                      $id = DW_WPML::getID($id, 'tax_category');
                      $DW->message('WPML ObjectID: ' . $id);
                    }

                  	foreach ( $opt as $condition ) {
                  		if ( $condition->name != 'default' ) {
                  			switch ( $condition->maintype ) {
                  				case 'category':
                  					$act_cat[ ] = $condition->name;
                  					break;

                  				case 'category-childs':
                  					$act_childs[ ] = $condition->name;
                  					break;
                  			}
                  		}
                  	}

                    if ( in_array($id, $act_cat) ) {
                      $display = $other;
                      $DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EC1)');
                    } else if ( count($act_childs) > 0 ) {
                    	$parents = $DW->getTaxParents('category', array(), $id);
                    	if ( (bool) array_intersect($act_childs, $parents) ) {
                    		$display = $other;
                    		$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EC2)');
                    	}
                    }
                  }
                  break;

              	case 'tag':
              		if ( count($act) > 0 ) {
              			global $wp_query;
              			$tag = $wp_query->get_queried_object_id();
              			if ( in_array($tag, $act) ) {
              				$display = $other;
              				$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule T1)');
              			}
              		}
              		break;

                case 'cp_archive':
                	if ( count($act) > 0 ) {
                		/*
                		   is_post_type_archive() is natively supported in WP 3.1
                		   WP 3.0.x gets is_post_type_archive() via plugin
                		   'Custom Post Type Archive', but does not accept array
                		*/
                		$is_cpa = FALSE;

                		if ( version_compare(substr($GLOBALS['wp_version'], 0, 3), '3.1', '>=') ) {
                			if ( is_post_type_archive($act) ) {
                				$is_cpa = TRUE;
                			}
                		} else {
                			$post_type = get_query_var('post_type');
                			if ( in_array($post_type, $act) ) {
                				$is_cpa = TRUE;
                			}
                		}

                		if ( $is_cpa ) {
                			$display = $other;
                			$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ECPA1)');
                		}
                	}
                	break;

              	case 'wpsc':
              		if ( count($act) > 0 ) {
              			include_once(DW_MODULES . 'wpec_module.php');

              			if ( DW_WPSC::is_dw_wpsc_category($act) ) {
              				$display = $other;
              				$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule ESC1)');
              			}
              		}
              		break;

              	case 'bp':
              		// We have to split out the conditions as we don't want the bp-groups to interfere
              		$act = array();
              		foreach ( $opt as $condition ) {
              			if ( $condition->name != 'default' && $condition->maintype == 'bp' ) {
              				$act[ ] = $condition->name;
              			}
              		}

              		if ( count($act) > 0 ) {
              			include_once(DW_MODULES . 'bp_module.php');

              			if ( DW_BP::is_dw_bp_component($act) ) {
              				$display = $other;
              				$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EBP1)');
              			}
              		}
              		break;

              	case 'bp-group':
              		if ( count($act) > 0 ) {
              			include_once(DW_MODULES . 'bp_module.php');

              			if ( DW_BP::is_dw_bp_group($act) ) {
              				$display = $other;
              				$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EBPG1)');
              			} else if ( DW_BP::is_dw_bp_group_forum($act) ) {
             					$display = $other;
             					$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EBPG2)');
              			} else if ( DW_BP::is_dw_bp_group_members($act) ) {
             					$display = $other;
             					$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EBPG3)');
              			}
              		}
              		break;

              	case 'pods':
              		if ( count($act) > 0 ) {
              			include_once(DW_MODULES . 'pods_module.php');

              			if ( DW_Pods::is_dw_pods_page($act) ) {
              				$display = $other;
              				$DW->message('Exception triggered for ' . $widget_id . ' sets display to ' . $e . ' (rule EPDS1)');
              			}
              		}
              		break;
              } // END switch ( $DW->whereami )
            } // END if/else ( $DW->custom_post_type )
          } /* END if ( count($opt) > 0 ) */

					if ( $display ) {
						foreach ( $DW->overrule_maintype as $mt ) {
							if (! $$mt ) {
        				$display = FALSE;
								break;
							}
						}
					}

          if (! $display ) {
            $DW->message('Removed ' . $widget_id . ' from display, SID = ' . $sidebar_id . ' / WID = ' . $widget_id . ' / KID = ' . $widget_key);
          	if ( DW_OLD_METHOD ) {
          		unset($DW->registered_widgets[$widget_id]);
          	} else {
          		unset($sidebars[$sidebar_id][$widget_key]);
          		if (! isset($DW->removelist[$sidebar_id]) ) {
          			$DW->removelist[$sidebar_id] = array();
          		}
          		$DW->removelist[$sidebar_id][ ] = $widget_key;
          	}
          }
        } // END if ( in_array($widget_id, $DW->dynwid_list) )

      	// Hide title
      	/* if ( in_array($widget_id, $dw_hide_title) ) {

      	} */

      } // END foreach ( $widgets as $widget_id )
    } // END if ( $sidebar_id != 'wp_inactive_widgets' && count($widgets) > 0 )
  } // END foreach ( $DW->sidebars as $sidebar_id => $widgets )

  $DW->listmade = TRUE;
  $DW->message('Dynamic Widgets END');
?>