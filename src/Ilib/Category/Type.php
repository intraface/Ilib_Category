<?php
/**
 * Handles what categories are used for.
 * 
 * php 5
 * 
 * @category Ilib
 * @package  Ilib_Category
 * @author   Kasper Broegaard Simonsen <kasper@broegaard.com>
 * @author   Mads
 */
 
/**
 * Handles what categories are used for.
 * 
 * @category Ilib
 * @package  Ilib_Category
 * @author   Kasper Broegaard Simonsen <kasper@broegaard.com>
 * @author   Mads Thorsted Nielsen <mads@masowich.com>
 */
class Ilib_Category_Type 
{
	
    /**
     * @var integer numeric represention of what the category belongs to
     */
    protected $belong_to;
    
    /**
     * @var integer id of what the category belongs to
     */
	protected $id;
	
	/**
     * Constructor
     *
     * @param String $type type of category. Eg 'shop'
     * @param Integer $id type id. If there are more than one shop.
     *
     * @return void
     */
		
	public function __construct($type, $id = 0) 
    {	
		switch($type) {
			case 'default':
				$this->belong_to = 1;
                $this->id = $id;
				break;
				
			default:
				throw new Exception('invalid type');
            	exit;
		}
	}
	
	/**
     * get belong to
     *
     * @return Integer
     */
	public function getBelongTo() 
    {
		return $this->belong_to;
	}

	/**
     * get belong to id
     *
     * @return Integer
     */
	public function getBelongToId() 
    {
		return $this->id;
	}
}
?>