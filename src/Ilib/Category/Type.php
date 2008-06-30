<?php
class Ilib_Category_Type {
	var $belong_to;
	var $id;
	
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
	
	public function getBelongTo() {
		return $this->belong_to;
	}
	public function getBelongToId() {
		return $this->id;
	}
}
?>