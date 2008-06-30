<?php
class Ilib_Category_Appender {
	
	public function getInstance($db) {
		static $instance = null;
		if ($instance == null) {
			$instance = new Ilib_Category_Appender($db); 
		}
		return $instance;
	}
	
	private $db;
	private function __construct($db) {
		$this->db = $db;
	}
	
	public function add($category, $object_id) {
        $result = $this->db->exec(
        		"INSERT INTO `ilib_category_append` " .
                "SET " .
                "object_id = ".$this->db->quote($object_id, 'integer').", " .
                "category_id = ".$this->db->quote($category->getId(), 'integer'). ";");
        
        if (PEAR::isError($result)) {
        	throw new Exception("Error in query: " . $result->getUserInfo());
        	exit;
        }
	}
	public function delete($category, $object_id) {
        $result = $this->db->exec(
        		"DELETE FROM `ilib_category_append` " .
                "WHERE object_id = ".$this->db->quote($object_id, 'integer')." " .
                "AND category_id = ".$this->db->quote($category->getId(), 'integer'). ";");
        if (PEAR::isError($result)) {
        	throw new Exception("Error in query: " . $result->getUserInfo());
        	exit;
        }
	}
	
	public function getSubObjects($category){
        $result = $this->db->query(
        		"SELECT * FROM ilib_category_append " .
        		"WHERE category_id = " . $category->getId() . ";");
        if (PEAR::isError($result)) {
        	throw new Exception("Error in query: " . $result->getUserInfo());
        	exit;
        }
        $sub = array();
        while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        	$sub[$row['id']] = $row['object_id'];
		}
		return $sub;
	}
	
}
?>