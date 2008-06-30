<?php
require_once 'Category/Appender.php';
require_once 'Category/Type.php';


class Ilib_Category {
	private $db = null;
	
	private $type = null;
	private $name = null;
	private $identifier = null;
	private $parent_id = null;
	
	private $id = null;
	
	public function __construct($db) {
		$this->db = $db;
		$this->type = $type;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	public function getType() {
		return $this->type;
	}

	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}
	public function getIdentifier() {
		return $this->identifier;
	}
	
	public function setParentId($parent_id) {
		$this->parent_id = $parent_id;
	}
	public function getParentId() {
		return $this->parent_id;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function load($id, $expectedType) {
        $result = $this->db->query(
        		"SELECT * FROM ilib_category " .
        		"WHERE id = " . $id . " " . 
                "AND belong_to = ".$this->db->quote($expectedType->getBelongTo(), 'integer')." " .
                "AND belong_to_id = ".$this->db->quote($expectedType->getBelongToId(), 'integer')." " .
        		";");
        $sub = array();
        if ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$this->type = $expectedType;
			$this->name = $row['name'];
			$this->identifier = $row['identifier'];
			$this->parent_id = $row['parent_id'];
			$this->id = $row['id'];
        } else {
        	// error
        }
	}
	
	public function save() {
		if ($this->type === null ||
			$this->name === null ||
			$this->identifier === null ||
			$this->parent_id === null) {
				echo "ERROR LINE " . __LINE__;
				// error not all paremeters set
			return;
		}
		
		$sql = 	    " ilib_category " .
	                "SET " .
	                "belong_to = ".$this->db->quote($this->type->getBelongTo(), 'integer').", " .
	                "belong_to_id = ".$this->db->quote($this->type->getBelongToId(), 'integer').", " .
	        		"parent_id = ".$this->db->quote($this->parent_id, 'integer').", " .
	                "name = ".$this->db->quote($this->name, 'text').", " .
	                "identifier = ".$this->db->quote($this->identifier, 'text'); 
		if ($this->id == null) {
	        $result = $this->db->exec("INSERT INTO" . $sql . ";");
	        $this->id = $this->db->lastInsertID();
		} else {
	        $result = $this->db->exec("UPDATE " . $sql . " WHERE id = " . $this->db->quote($this->id, 'text') . ";");
		}
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
	
	
}
?>