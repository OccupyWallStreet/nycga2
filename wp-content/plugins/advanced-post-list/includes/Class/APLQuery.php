<?php

class APLQuery
{
    /**
     * @var array
     * @since 0.3.0
     */
    public $_posts;
    /**
     * <p><b>Desc:</b> Plugin's shadowed version of WP_Query Class. This class
     *                 was created to add additional funtions that WP_Query
     *                 doesn't have.</p>
     * @access public
     * @param Object $presetObj Holds the post list preset data.
     * 
     * @since 0.3.0
     * @todo Needs work and to be organzied. This is just some code slapped
     *       into a class at the moment to prepare for future modifications 
     *       and additions.
     * 
     * @uses file.ext|elementname|class::methodname()|class::$variablename|
     *        functionname()|function functionname description of how the 
     *        element is used
     * 
     * @tutorial 
     * <ol>
     * <li value="1"></li>
     * <li value="2"></li>
     * </ol>
     */
    public function __construct($presetObj)
    {
        
        $post_type_names = get_post_types('',
                                          'names');
        $skip_post_types = array('attachment', 'revision', 'nav_menu_item');
        foreach($skip_post_types as $value)
        {
            unset($post_type_names[$value]);
        }
        
        $excludeCurrent = array();
        if ($presetObj->_postExcludeCurrent !== FALSE)
        {
            $excludeCurrent[0] = $presetObj->_postExcludeCurrent;
        }
        
        $tmp_postTax = (array) $presetObj->_postTax;
        if (empty($presetObj->_postParent) && empty($tmp_postTax))
        {
            //// DEFAULT IF POSTTAX AND PARENT IS EMPTY
            foreach ($post_type_names as $post_type_name)
            {
                $arg_query_parents = array();
                $arg_query_reqSel[$post_type_name]['selected_taxonomy'] = array(
                    'post_type' => $post_type_name,
                    'post_status' => $presetObj->_postStatus,
                    'post__not_in' => $excludeCurrent,
                    'nopaging' => true,
                    'order' => $presetObj->_listOrder,
                    'orderby' => $presetObj->_listOrderBy
                );
                $arg_query_reqSel[$post_type_name]['required_taxonomy'] = array();
            }
            
        }
        else
        {
            //// POST PARENTS
            //TODO Add category and tag capabilities
            $arg_query_parents = array();
            foreach ($presetObj->_postParent as $parent_index => $parentID)
            {
                $arg_query_parents[$parent_index] = array(
                    'post_type' => get_post_type($parentID),
                    'post_parent' => $parentID,
                    'post_status' => $presetObj->_postStatus,
                    'post__not_in' => $excludeCurrent,
                    'nopaging' => true,
                    'order' => $presetObj->_listOrder,
                    'orderby' => $presetObj->_listOrderBy
                    
                );
            }

            //// REQUIRED AND SELECTED TAXONOMIES

            $arg_query_reqSel = array();
            foreach ($presetObj->_postTax as $post_type_name => $post_type_value)
            {
                
                $arg_selected = array();
                $arg_required = array();
                $count_req = 0;
                $count_sel = 0;
                foreach($post_type_value->taxonomies as $taxonomy_name => $taxonomy_value)
                {
                    if (!empty($taxonomy_value->terms))
                    {
                        if ($taxonomy_value->require_taxonomy == true)
                        {
                            $arg_required['post_status'] = $presetObj->_postStatus;
                            $arg_required['order'] = $presetObj->_listOrder;
                            $arg_required['orderby'] = $presetObj->_listOrderBy;

                            $arg_required['post_type'] = $post_type_name;
                            $arg_required['tax_query']['relation'] = 'AND';
                            $arg_required['tax_query'][$count_req]['taxonomy'] = $taxonomy_name;
                            $arg_required['tax_query'][$count_req]['field'] = 'id';
                            $arg_required['tax_query'][$count_req]['terms'] = $taxonomy_value->terms;
                            $arg_required['tax_query'][$count_req]['include_children'] = false;
                            $arg_required['tax_query'][$count_req]['operator'] = 'IN';
                            if ($taxonomy_value->require_terms == true)
                            {
                                $arg_required['tax_query'][$count_req]['operator'] = 'AND';
                            }

                            $arg_required['post__not_in'] = $excludeCurrent;
                            $arg_required['nopaging'] = true;
                            $count_req++;
                        }
                        else
                        {
                            $arg_selected['post_status'] = $presetObj->_postStatus;
                            $arg_selected['order'] = $presetObj->_listOrder;
                            $arg_selected['orderby'] = $presetObj->_listOrderBy;

                            $arg_selected['post_type'] = $post_type_name;
                            $arg_selected['tax_query']['relation'] = 'OR';
                            $arg_selected['tax_query'][$count_sel]['taxonomy'] = $taxonomy_name;
                            $arg_selected['tax_query'][$count_sel]['field'] = 'id';
                            $arg_selected['tax_query'][$count_sel]['terms'] = $taxonomy_value->terms;
                            $arg_selected['tax_query'][$count_sel]['include_children'] = false;
                            $arg_selected['tax_query'][$count_sel]['operator'] = 'IN';
                            if ($taxonomy_value->require_terms == true)
                            {
                                $arg_selected['tax_query'][$count_req]['operator'] = 'AND';
                            }

                            $arg_selected['post__not_in'] = $excludeCurrent;
                            $arg_selected['nopaging'] = true;
                            $count_sel++;
                        }
                    }
                    

                }

                $arg_query_reqSel[$post_type_name]['required_taxonomy'] = $arg_required;
                $arg_query_reqSel[$post_type_name]['selected_taxonomy'] = $arg_selected;

            }
        }
        
        
         
        
        
        //// GET WP_QUERIES
        foreach ($arg_query_reqSel as $post_type_name => $post_type_query)
        {
            //$a1 = $post_type_query['selected_taxonomy'];
            wp_reset_postdata();
            wp_reset_query();
            $APL_Query_selected = new WP_Query($post_type_query['selected_taxonomy']);
            $APL_Query_required = new WP_Query($post_type_query['required_taxonomy']);

            ////var_dump($APL_Query_selected);
            ////var_dump($APL_Query_required);
;

            $posts_selected[$post_type_name] = $APL_Query_selected->posts;
            $posts_required[$post_type_name] = $APL_Query_required->posts;
            
            wp_reset_postdata();
            wp_reset_query();
            unset($APL_Query_selected);
            unset($APL_Query_required);
            wp_reset_postdata();
            wp_reset_query();
        }
        foreach ($arg_query_parents as $index => $arg_query_parent)
        {
            //$count = count($APL_Query_parents[$arg_query_parent['post_type']]);
            $APL_Query_parents = new WP_Query($arg_query_parent);
            
            
            $count = count($posts_parents[$arg_query_parent['post_type']]);
            foreach ($APL_Query_parents->posts as $post_parent)
            {
                
                $posts_parents[$arg_query_parent['post_type']][$count] = $post_parent;

                $count++;
                
            }
            wp_reset_postdata();
            wp_reset_query();
            unset($APL_Query_parents);
            wp_reset_postdata();
            wp_reset_query();
            //$posts_parents[$arg_query_parent['post_type']] = array_unique($posts_parents[$arg_query_parent['post_type']]);
        }
        //// MERGE POSTS
        $rtnPosts = array();
        $tmp_posts = array();
        foreach ($post_type_names as $post_type_name)
        {
            
            $tmp_count = 0;
            if (!empty ($posts_selected[$post_type_name]))
            {
                if (empty ($posts_required[$post_type_name]))
                {
                    $tmp_posts[$post_type_name] = $posts_selected[$post_type_name];
                }
                else 
                {
                    foreach ($posts_required[$post_type_name] as $post_req)
                    {
                        foreach ($posts_selected[$post_type_name] as $post_sel)
                        {
                            if ($post_req->ID == $post_sel->ID)
                            {
                                $tmp_posts[$post_type_name][$tmp_count] = $post_req;
                                $tmp_count++;
                            }
                        }
                    }
                }

            }
            else if (!empty ($posts_required[$post_type_name]))
            {
                $tmp_posts[$post_type_name] = $posts_required[$post_type_name];
            }
        }
        
        
        $rtnPosts = $tmp_posts;
        $tmp_posts = array();
        foreach($post_type_names as $post_type_name)
        {
            $tmp_count = 0;
            if (!empty($posts_parents[$post_type_name]))
            {
                if(empty($rtnPosts[$post_type_name]))
                {
                    $tmp_posts[$post_type_name] = $posts_parents[$post_type_name];
                }
                else
                {
                    foreach($rtnPosts[$post_type_name] as $post_rtn)
                    {
                        foreach($posts_parents[$post_type_name] as $post_par)
                        {
                            if ($post_par->ID == $post_rtn->ID)
                            {
                                $tmp_posts[$post_type_name][$tmp_count] = $parent_post->ID;
                                $tmp_count++;
                            }
                        }
                    }
                }
            }
            
            else if (!empty($rtnPosts[$post_type_name]))
            {
                $tmp_posts[$post_type_name] = $rtnPosts[$post_type_name];
            }
            
        }
        
        $rtnPosts = $tmp_posts;
        
        //COMBINE POSTS FROM OTHER POST TYPES
        $tmp_posts = array();
        $tmp_count = 0;
        $sort_post_type_array = array();
        foreach ($rtnPosts as $post_type_name => $post_type_posts)
        {
            $sort_post_type_array[count($sort_post_type_array)] = $post_type_name;
            foreach ($post_type_posts as $post)
            {
                $tmp_posts[$tmp_count] = $post;
                $tmp_count++;
            }
        }
        $rtnPosts = $tmp_posts;
        
        //// SORT
        //THIS IS SIMPLE BUT EFFECTIVE WAY TO SORT ALL THE POSTS
        wp_reset_postdata();
        wp_reset_query();
        $tmp_posts = array();
        $tmp_count = 0;
        
        $ex_arg_query = array();
        $ex_arg_query['post_type'] = $sort_post_type_array;
        $ex_arg_query['post_status'] = $presetObj->_postStatus;
        $ex_arg_query['nopaging'] = true;
        $ex_arg_query['order'] = $presetObj->_listOrder;
        $ex_arg_query['orderby'] = $presetObj->_listOrderBy;
        
        
        
        
        
        //$ex_arg_query['ignore_sticky_posts'] = $presetObj->_ignoreStickyPosts;
        
        
        wp_reset_postdata();
        wp_reset_query();
        ////wp_reset_postdata();
        ////wp_reset_postdata();
        ////wp_reset_postdata();
        $APL_Query = new WP_Query($ex_arg_query);
        ////var_dump($APL_Query);
        
        foreach ($APL_Query->posts as $post)
        {
            foreach ($rtnPosts as $rtnPost)
            {
                if ($post->ID === $rtnPost->ID)
                {
                    $tmp_posts[$tmp_count] = $post;
                    $tmp_count++;
                }
            }
        }
        $rtnPosts = $tmp_posts;
        
        wp_reset_postdata();
        wp_reset_query();
        unset($APL_Query);
        ////wp_reset_postdata();
        ////wp_reset_query();
        
        if ($presetObj->_listAmount == -1)
        {
            $rtnPosts = array_slice($rtnPosts,
                                    0,
                                    count($rtnPosts));
        }
        else
        {
            $rtnPosts = array_slice($rtnPosts,
                                    0,
                                    $presetObj->_listAmount);
        }
        
        $this->_posts = $rtnPosts;
        //return $rtnPosts;
    }
}

?>
