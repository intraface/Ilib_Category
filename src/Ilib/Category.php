<?php
require_once 'Category/Appender.php';
require_once 'Category/Type.php';


class Ilib_Category {
	private $db;
	private $type;
	private $id;
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
        $this->id = $this->db->lastInsertID();
	}
	
	public function getSubCategories(){
        $result = $this->db->query(
        		"SELECT * FROM ilib_category " .
        		"WHERE parent_id = " . $this->id . ";");
        $sub = array();
        while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        	$sub[$row['id']] = $row['identifier'];
		}
		return $sub;
	}
	public function getSubObjects(){
        $result = $this->db->query(
        		"SELECT * FROM ilib_category_append " .
        		"WHERE category_id = " . $this->id . ";");
        $sub = array();
        while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        	$sub[$row['id']] = $row['object_id'];
		}
		return $sub;
	}
	
	public function getId() {
		return $this->id;
	}
	
}
?>