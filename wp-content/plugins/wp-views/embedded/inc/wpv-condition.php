<?php

/**
 * Views-Shortcode: wpv-if
 *
 * Description: Conditional shortcode to be used to display a specific area
 * based on a custom field condition. 
 * 
 * Supported actions and symbols:
 * 
 * Integer and floating-point numbers
 * Math operators: +, -, *, /
 * Comparison operators: &lt;, &gt;, =, &lt;=, &gt;=, !=
 * Boolean operators: AND, OR, NOT
 * Nested expressions - several levels of parentheses
 * Variables defined as shortcode parameters starting with a dollar sign
 * empty() function that checks for empty or non-existing fields
 * 
 *
 * Example usage:
 * 
 * [wpv-if evaluate="boolean condition"]
 *    Execute code for true
 * [/wpv-if]
 * 
 * using a variable and comparing its value to a constant
 * 
 * [wpv-if f1="wpcf-condnum1" evaluate="$f1 = 1" debug="true"]Number1=1[/wpv-if]
 * 
 * Two numeric variables in a mathematical expression with boolean operators
 * 
 * [wpv-if f1="wpcf-condnum1" f2="wpcf-condnum2" evaluate="(2 < 3 AND (((3+$f2)/2) > 3 OR NOT($f1 > 3)))" debug="true"]Visible block[/wpv-if]
 * 
 * Compare custom field with a value
 * 
 * [wpv-if f1="wpcf-condstr1" evaluate="$f1 = 'My text'" debug="true"]Text1='My text' [/wpv-if]
 * 
 * Display condition if evaluates to false (use instead of else-if)
 * 
 * [wpv-if condition="false" evaluate="2 > 3"] 2 > 3 [/wpv-if]
 * 
 *
 * Parameters:
 * 'condition' => Define expected result from evaluate - either true or false
 * 'evaluate' => Evaluate expression with fields involved, sample use: "($field1 > $field2) AND !empty($field3)"
 * 'debug' => Enable debug to display error messages in the shortcode 
 * 'fieldX' => Define fields to be taken into account during evaluation 
 *
 */

function wpv_shortcode_wpv_if($args, $content) {
    $result = wpv_condition($args);
    
    extract(
        shortcode_atts( array('evaluate' => FALSE, 'debug' => FALSE, 'condition' => TRUE), $args)
    );
    $condition = ($condition == 'true' || $condition === TRUE) ? true : false;
    
 	// show the view area if condition corresponds to the evaluate returned result 1=1 or 0=0
    if(($result === true && $condition) || ($result === false && !$condition)) {
    	return wpv_do_shortcode($content);
    }
    else { 
    	// output empty string or the error message if debug is true
    	// empty for different condition and evaluate result
    	if(($result === false && $condition) || ($result === true && !$condition) ) {
    		return '';
    	}
    	else {
    		if($debug) {
    			return $result;
    		}
    	}
    }
}


add_shortcode('wpv-if', 'wpv_shortcode_wpv_if');