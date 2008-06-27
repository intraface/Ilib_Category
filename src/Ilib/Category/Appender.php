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
	}
	public function delete($category, $object_id) {
        $result = $this->db->exec(
        		"DELETE FROM `ilib_category_append` " .
                "WHERE object_id = ".$this->db->quote($object_id, 'integer')." " .
                "AND category_id = ".$this->db->quote($category->getId(), 'integer'). ";");
	}
}
?>