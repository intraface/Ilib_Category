<?php
/**
 * Category
 *
 * @author Kasper Broegaard Simonsen <kasper@broegaard.com>
 * @author Mads Thorsted Nielsen <mads@masowich.com>
 */

require_once 'Category/Appender.php';
require_once 'Category/Type.php';


/**
 * Category 
 */

class Ilib_Category {
	private $db = null;
	
	private $type = null;
	private $name = null;
	private $identifier = null;
	private $parent_id = null;
	
	private $id = null;
	
    /**
     * Constructor
     *
     * @param object $db database object
     * @param object $type type of category
     *
     * @return void
     */
		
	public function __construct($db, $type) {
		$this->db = $db;
		$this->type = $type;
	}
	
    /**
     * get category type
     *
     * @return Category_Type
     */
	
	public function getType() {
		return $this->type;
	}

    /**
     * set category name
     * 
     * @param String $name name of category
     *
     * @return void
     */
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
     * get category name
     * 
     * @return String
     */
	
	public function getName() {
		return $this->name;
	}
	

    /**
     * set category identitier
     * 
     * @param String $identifier identifier of category
     *
     * @return void
     */
	
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}
	
	/**
     * get category identifier
     * 
     * @return String
     */
	
	public function getIdentifier() {
		return $this->identifier;
	}
	
    /**
     * set category parent id
     * 
     * @param Integer $parent_id id of parent category
     *
     * @return void
     */
	public function setParentId($parent_id) {
		$this->parent_id = $parent_id;
	}
    
	/**
     * get parent id
     * 
     * @return Integer
     */
	public function getParentId() {
		return $this->parent_id;
	}
	
	/**
     * get id
     * 
     * @return Integer
     */
	public function getId() {
		return $this->id;
	}
	
	/**
     * load from db
     * 
     * @param Integer $id id of category
     * 
     * @return void
     */
	public function load($id) {
        $result = $this->db->query(
        		"SELECT * FROM ilib_category " .
        		"WHERE id = " . $id . " " . 
                "AND belong_to = ".$this->db->quote($this->type->getBelongTo(), 'integer')." " .
                "AND belong_to_id = ".$this->db->quote($this->type->getBelongToId(), 'integer')." " .
        		";");
        if (PEAR::isError($result)) {
        	throw new Exception("Error in query: " . $result->getUserInfo());
        	exit;
        }
        
        $sub = array();
        if ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$this->name = $row['name'];
			$this->identifier = $row['identifier'];
			$this->parent_id = $row['parent_id'];
			$this->id = $row['id'];
        } else {
        	throw new Exception('wrong id or type');
        	exit;
        }
	}
	
	/**
     * save to db
     * 
     * @return void
     */
	public function save() { 
		
		if ($this->type === null ||
			$this->name === null ||
			$this->identifier === null ||
			$this->parent_id === null) {
				throw new Exception('one of the parameters is not set');
				exit;
		}
		
		$sql =	" ilib_category " .
				"SET " .
				"belong_to = ".$this->db->quote($this->type->getBelongTo(), 'integer').", " .
				"belong_to_id = ".$this->db->quote($this->type->getBelongToId(), 'integer').", " .
				"parent_id = ".$this->db->quote($this->parent_id, 'integer').", " .
				"name = ".$this->db->quote($this->name, 'text').", " .
				"identifier = ".$this->db->quote($this->identifier, 'text');
		 
		if ($this->id === null) {
	        $result = $this->db->exec("INSERT INTO" . $sql . ";");
	        if (PEAR::isError($result)) {
	        	throw new Exception("Error in query: " . $result->getUserInfo());
	        	exit;
	        }
		    $this->id = $this->db->lastInsertID();
		} else {
	        $result = $this->db->exec("UPDATE " . $sql . " WHERE id = " . $this->db->quote($this->id, 'text') . ";");
	        if (PEAR::isError($result)) {
	        	throw new Exception("Error in query: " . $result->getUserInfo());
	        	exit;
	        }
		}
	}
	
	
	/**
     * get sub categories 
     * 
     * @return array (id as key, identifier as value)
     */
	public function getSubCategories() {
        $result = $this->db->query(
        		"SELECT * FROM ilib_category " .
        		"WHERE parent_id = " . $this->id . ";");
        if (PEAR::isError($result)) {
        	throw new Exception("Error in query: " . $result->getUserInfo());
        	exit;
        }
        $sub = array();
        while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        	$sub[$row['id']] = $row['identifier'];
		}
		return $sub;
	}
	
	
}
?>