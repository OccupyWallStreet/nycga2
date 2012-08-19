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
        unset($value);
        unset($skip_post_types);
        
        //// Pre-set Filters
        $author_filter = '';
        if ($presetObj->_postAuthorOperator != 'none' && !empty($presetObj->_postAuthorIDs))
        {
            $author_operator = '';
            if ($presetObj->_postAuthorOperator === 'exclude')
            {
                $author_operator = '-';
            }
            foreach ($presetObj->_postAuthorIDs as $i => $author_id)
            {
                $author_filter .= $author_operator . $author_id;
                if ($i < (count($presetObj->_postAuthorIDs) - 1))
                {
                    $author_filter .= ',';
                }
            }
        }
        
        
        $public_posts = array();
        $private_posts = array();
        foreach ($presetObj->_postVisibility as $visible)
        {
            if ($visible === 'private')
            {
                $private_presetObj = $presetObj;
                $private_presetObj->_postStatus[] = 'private';
                $private_arg_query = $this->APLQ_set_query($private_presetObj, $author_filter, $post_type_names);
                $private_posts = $this->APLQ_get_posts(
                        $private_arg_query['arg_query_reqSel'], 
                        $private_arg_query['arg_query_parents'], 
                        $post_type_names);
            }
            else
            {
                $arg_query = $this->APLQ_set_query($presetObj, $author_filter, $post_type_names);
                $public_posts = $this->APLQ_get_posts(
                        $arg_query['arg_query_reqSel'], 
                        $arg_query['arg_query_parents'], 
                        $post_type_names);
            }
            
            
            
        }
        
        $post_types_used = array();
        $rtnPosts = array();
        if (!empty($private_posts) && !empty($public_posts))
        {
            $post_types_used = $private_posts['post_types_used'];
            foreach($public_posts['post_types_used'] as $post_type_value)
            {
                $post_types_used[] = $post_type_value;
            }
            $post_types_used  = array_unique($post_types_used);
            $rtnPosts = $this->APLQ_merge_private_public($private_posts['posts'], $public_posts['posts']);
        }
        else if (!empty($private_posts))
        {
            $post_types_used = $private_posts['post_types_used'];
            $rtnPosts = $private_posts['posts'];
        }
        else
        {
            $post_types_used = $public_posts['post_types_used'];
            $rtnPosts = $public_posts['posts'];
        }
        
        
         
        
        
        
        
        //// SORT
        //THIS IS SIMPLE BUT EFFECTIVE WAY TO SORT ALL THE POSTS
        $tmp_posts = array();
        $tmp_count = 0;
        
        $ex_arg_query = array();
        $ex_arg_query['post_type'] = $post_types_used;
        $ex_arg_query['post_status'] = 'any';
        $ex_arg_query['nopaging'] = true;
        $ex_arg_query['order'] = $presetObj->_listOrder;
        $ex_arg_query['orderby'] = $presetObj->_listOrderBy;
        
        $ex_arg_query['post__not_in'] = $presetObj->_listExcludePosts;
        //$ex_arg_query['suppress_filter'] = TRUE;
        $ex_arg_query['ignore_sticky_posts'] = $presetObj->_listIgnoreSticky;
        $ex_arg_query['perm'] = 'readable';
        
        
        

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
                    break;
                }
            }
        }
        $rtnPosts = $tmp_posts;
        
        //wp_reset_postdata();
        
        unset($APL_Query);

        
        if ($presetObj->_listCount == -1)
        {
            $rtnPosts = array_slice($rtnPosts,
                                    0,
                                    count($rtnPosts));
        }
        else
        {
            $rtnPosts = array_slice($rtnPosts,
                                    0,
                                    $presetObj->_listCount);
        }
        
        $this->_posts = $rtnPosts;
        //var_dump($this->_posts);
        //return $rtnPosts;
    }
    
    
    
    
    
    private function APLQ_set_query($presetObj, $author_filter, $post_type_names)
    {
        $tmp_postTax = (array) $presetObj->_postTax;
        if (empty($presetObj->_postParents) && empty($tmp_postTax))
        {
            //// DEFAULT IF POSTTAX AND PARENT IS EMPTY
            foreach ($post_type_names as $post_type_name)
            {
                $arg_query_parents = array();
                $arg_query_reqSel[$post_type_name]['selected_taxonomy'] = array(
                    'post_type' => $post_type_name,
                    'post_status' => $presetObj->_postStatus,
                    'post__not_in' => $presetObj->_listExcludePosts,
                    'nopaging' => true,
                    'order' => $presetObj->_listOrder,
                    'orderby' => $presetObj->_listOrderBy,
                    
                    //'suppress_filters' => TRUE,
                    'author' => $author_filter,
                    'ignore_sticky_posts' => $presetObj->_listIgnoreSticky,
                    'perm' => $presetObj->_userPerm
                );
                $arg_query_reqSel[$post_type_name]['required_taxonomy'] = array();
            }
        
        }
        else
        {




            //// POST PARENTS
            //TODO Add category and tag capabilities
            $arg_query_parents = array();
            foreach ($presetObj->_postParents as $parent_index => $parentID)
            {
                $arg_query_parents[$parent_index] = array(
                    'post_type' => get_post_type($parentID),
                    'post_parent' => $parentID,
                    'post_status' => $presetObj->_postStatus,
                    'post__not_in' => $presetObj->_listExcludePosts,
                    'nopaging' => true,
                    'order' => $presetObj->_listOrder,
                    'orderby' => $presetObj->_listOrderBy,
                    
                    //'suppress_filters' => TRUE,
                    'author' => $author_filter,
                    'ignore_sticky_posts' => $presetObj->_listIgnoreSticky,
                    'perm' => $presetObj->_userPerm
                
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
                foreach ($post_type_value->taxonomies as $taxonomy_name => $taxonomy_value)
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


                            if ($taxonomy_value->terms[0] != 0)
                            {
                                $arg_required['tax_query'][$count_req]['field'] = 'id';
                                $arg_required['tax_query'][$count_req]['terms'] = $taxonomy_value->terms;
                                $arg_required['tax_query'][$count_req]['include_children'] = false;
                                $arg_required['tax_query'][$count_req]['operator'] = 'IN';
                            }




                            if ($taxonomy_value->require_terms == true)
                            {
                                $arg_required['tax_query'][$count_req]['operator'] = 'AND';
                            }

                            $arg_required['post__not_in'] = $presetObj->_listExcludePosts;
                            $arg_required['nopaging'] = true;


                            $arg_required['author'] = $author_filter;
                            //$arg_required['suppress_filters'] = TRUE;
                            $arg_required['ignore_sticky_posts'] = $presetObj->_listIgnoreSticky;
                            $arg_required['perm'] = $presetObj->_userPerm;
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


                            if ($taxonomy_value->terms[0] != 0)
                            {
                                $arg_selected['tax_query'][$count_sel]['field'] = 'id';
                                $arg_selected['tax_query'][$count_sel]['terms'] = $taxonomy_value->terms;
                                $arg_selected['tax_query'][$count_sel]['include_children'] = false;
                                $arg_selected['tax_query'][$count_sel]['operator'] = 'IN';
                            }


                            if ($taxonomy_value->require_terms == true)
                            {
                                $arg_selected['tax_query'][$count_sel]['operator'] = 'AND';
                            }

                            $arg_selected['post__not_in'] = $presetObj->_listExcludePosts;
                            $arg_selected['nopaging'] = true;


                            $arg_selected['author'] = $author_filter;
                            //$arg_selected['suppress_filters'] = true;
                            $arg_selected['ignore_sticky_posts'] = $presetObj->_listIgnoreSticky;
                            $arg_selected['perm'] = $presetObj->_userPerm;
                            $count_sel++;
                        }
                    }
                

                }
                unset($taxonomy_name);
                unset($taxonomy_value);
                unset($count_req);
                unset($count_sel);

                $arg_query_reqSel[$post_type_name]['required_taxonomy'] = $arg_required;
                $arg_query_reqSel[$post_type_name]['selected_taxonomy'] = $arg_selected;


                unset($arg_required);
                unset($arg_selected);
            }
            unset($post_type_name);
            unset($post_type_value);
        
        }
        $arg_query = array(
            'arg_query_parents' => $arg_query_parents,
            'arg_query_reqSel' => $arg_query_reqSel
        );
        
        return $arg_query;
    }
    private function APLQ_get_posts($arg_query_reqSel, $arg_query_parents, $post_type_names)
    {
        //// GET WP_QUERIES
        
        $posts_selected = array();
        $posts_required = array();
        foreach ($arg_query_reqSel as $post_type_name => $post_type_query)
        {
            //$a1 = $post_type_query['selected_taxonomy'];
            
            
            $APL_Query_selected = new WP_Query($post_type_query['selected_taxonomy']);
            if (isset($APL_Query_selected->posts))
            {
                $posts_selected[$post_type_name] = $APL_Query_selected->posts;
            }
            //wp_reset_postdata();
            unset($APL_Query_selected);
            
            $APL_Query_required = new WP_Query($post_type_query['required_taxonomy']);
            if (isset($APL_Query_required->posts))
            {
                $posts_required[$post_type_name] = $APL_Query_required->posts;
            }
            unset($APL_Query_required);
        }
        foreach ($arg_query_parents as $index => $arg_query_parent)
        {
            //$count = count($APL_Query_parents[$arg_query_parent['post_type']]);
            $APL_Query_parents = new WP_Query($arg_query_parent);
            
            //$query = new WP_Query( array( 'post_status' => array( 'publish' ) ) );
            $count = count($posts_parents[$arg_query_parent['post_type']]);
            foreach ($APL_Query_parents->posts as $post_parent)
            {
                
                $posts_parents[$arg_query_parent['post_type']][$count] = $post_parent;

                $count++;
                
            }

            unset($APL_Query_parents);
            
            
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
        $post_types_used = array();
        foreach ($rtnPosts as $post_type_name => $post_type_posts)
        {
            $post_types_used[count($post_types_used)] = $post_type_name;
            foreach ($post_type_posts as $post)
            {
                $tmp_posts[$tmp_count] = $post;
                $tmp_count++;
            }
        }
        $rtnPosts['post_types_used'] = $post_types_used;
        $rtnPosts['posts'] = $tmp_posts;
        return $rtnPosts;
    }
    private function APLQ_merge_private_public($private_posts, $public_posts)
    {
        foreach ($private_posts as $private_post)
        {
            $public_posts[] = $private_post;
        }
        return $public_posts;
    }
}

?>
