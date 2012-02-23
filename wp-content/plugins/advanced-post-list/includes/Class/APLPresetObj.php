<?php

class APLPresetObj
{
  //Varibles
  //Holds the content that the user can modify
  //TODO Create a better method for varible types. All varibles are 
  // recieving string values when used. Nothing bad, just good practice.
  /**
   * @var string
   * @since 0.1.0
   */
  var $_before = '';
  /**
   * @var string
   * @since 0.1.0
   */
  var $_content = '';
  /**
   * @var string
   * @since 0.1.0
   */
  var $_after = '';
  
  //Holds all the category & tag data that is used for filtering
  /**
   * @var string
   * @since 0.1.0
   */
  var $_catsSelected = '';//All//(int) array
  /**
   * @var string
   * @since 0.1.0
   */
  var $_tagsSelected = '';//All
  /**
   * @var string
   * @since 0.1.0
   */
  var $_catsInclude = 'false';//Boolean Unchecked
  /**
   * @var string
   * @since 0.1.0
   */
  var $_tagsInclude = 'false';//Boolean Unchecked
  /**
   * @var string
   * @since 0.1.0
   */
  var $_catsRequired = 'false';//Boolean Unchecked
  /**
   * @var string
   * @since 0.1.0
   */
  var $_tagsRequired = 'false';//Boolean Unchecked
  
  //Settings that will be used for how this is displayed in the list
  /**
   * @var string
   * @since 0.1.0
   */
  var $_listOrder = '';//Desc
  
  /**
   * @var string
   * @since 0.1.0
   */
  var $_listOrderBy = '';//(string) Type
  /**
   * @var string
   * @since 0.1.0
   */
  var $_listAmount = '';//(int) howmany to display
  
  //Post attributes
  /**
   * @var string
   * @since 0.1.0
   */
  var $_postType = '';//(string) post or page
  /**
   * @var string
   * @since 0.1.0
   */
  var $_postParent = '';
  //leave out the current page/post that the plugin is displaying on.
  /**
   * @var string
   * @since 0.1.0
   */
  var $_postExcludeCurrent = 'false';//Boolean Unchecked
  
  
  function __construct()
  {
    
  }
  
}
?>
