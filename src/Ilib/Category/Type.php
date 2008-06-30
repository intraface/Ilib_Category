<?php
class Ilib_Category_Type {
	var $belong_to;
	var $id;
	
	/**
     * Constructor
     *
     * @param String $type type of category
     * @param Integer $id type id
     *
     * @return void
     */
		
	public function __construct($type, $id) {
		$this->id = $id;
		switch($type) {
			case 'webshop':
				$this->belong_to = 1;
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
	
	public function getBelongTo() {
		return $this->belong_to;
	}

	/**
     * get belong to id
     *
     * @return Integer
     */
	public function getBelongToId() {
		return $this->id;
	}
}
?>