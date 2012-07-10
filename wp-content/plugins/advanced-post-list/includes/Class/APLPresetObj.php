<?php

class APLPresetObj
{
    //Varibles
    //Holds the content that the user can modify
    //TODO Create a better method for varible types. All varibles are 
    // recieving string values when used. Nothing bad, just good practice.
    
    
    /**
     * @var array => string
     * @since 0.1.0
     * @version 0.3.0 - changed (string) to (array) => (string)
     */
    public $_postParent;
    
    /**
     * @var object
     * @since 0.3.0
     */
    public $_postTax;
    
    /**
     * @var int
     * @since 0.1.0
     * @version 0.3.0  - changed (string) to (int)
     */
    public $_listAmount;
    
    /**
     * @var string
     * @since 0.1.0
     */
    public $_listOrderBy;
    
    /**
     * @var string
     * @since 0.1.0
     */
    public $_listOrder;
    
    /**
     * @var string
     * @since 0.3.0
     */
    public $_postStatus;
    
    /**
     * @var boolean
     * @since 0.1.0
     * @version 0.3.0 - changed (string) to (boolean)
     */
    public $_postExcludeCurrent; 
    
    /**
     * @var string
     * @since 0.1.0
     */
    public $_before;

    /**
     * @var string
     * @since 0.1.0
     */
    public $_content;

    /**
     * @var string
     * @since 0.1.0
     */
    public $_after;
    
    

    function __construct()
    {
        $this->_postParent = (array) array();
        $this->_postTax = (object) new stdClass();
        
        $this->_listAmount = (int) 5;
        
        $this->_listOrderBy = (string)'';
        $this->_listOrder = (string) '';
        
        $this->_postStatus = (string) '';
        
        $this->_postExcludeCurrent = (bool) true;
        
        $this->_before = (string) '';
        $this->_content = (string) '';
        $this->_after = (string) '';
        
    }
}

?>