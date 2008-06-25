<?php
require_once 'Category/Appender.php';
require_once 'Category/Type.php';


class Ilib_Category {
	var $db;
	var $type;
	public function __construct($db, $type) {
		$this->db = $db;
		$this->type = $type;
	}
	
	public function save($name, $identifier, $parent_id) {
        $result = $this->db->exec(
        		"INSERT INTO ilib_category " .
                "SET " .
                "belong_to = ".$this->db->quote($this->type->getBelongTo(), 'integer').", " .
                "belong_to_id = ".$this->db->quote($this->type->getBelongToId(), 'integer').", " .
        		"parent_id = ".$this->db->quote($parent_id, 'integer').", " .
                "name = ".$this->db->quote($name, 'text').", " .
                "identifier = ".$this->db->quote($identifier, 'text').";");
        
       	print_r($result);
	}
}
?>