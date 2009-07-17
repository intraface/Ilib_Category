<?php
/**
 * Handles recursive categories.
 *
 * php 5
 *
 * @category Ilib
 * @package  Ilib_Category
 * @author Kasper Broegaard Simonsen <kasper@broegaard.com>
 * @author Mads Thorsted Nielsen <mads@masowich.com>
 */

/**
 * Category
 *
 * @category Ilib
 * @package  Ilib_Category
 * @author Kasper Broegaard Simonsen <kasper@broegaard.com>
 * @author Mads Thorsted Nielsen <mads@masowich.com>
 */
class Ilib_Category
{
	/**
     * @var object MDB2
	 */
    protected $db = null;

    /**
     * @var object Ilib_Category_Type instance
     */
	private $type = null;

    /**
     * @var string name of catogory
     */
	private $name = null;

    /**
     * @var string identifier of category
     */
	private $identifier = null;

    /**
     * @var integer parent id if child of other category
     */
	private $parent_id = null;

    /**
     * @var id id of category
     */
	private $id = null;

    /**
     * @var string extra conditions for select sql queries
     */
    private $extra_condition_select;

    /**
     * @var string extra conditions for update and insert sql queries
     */
    private $extra_condition_update;

    /**
     * @var array options
     */
    protected $options;

    /**
     * Constructor
     *
     * @param object $db database object
     * @param object $type type of category
     * @param array options. Possibe options:
     *              string 'extra_condition' extra conditions to use in sql eg. "intranet_id = 1"
     *
     * @return void
     */
	public function __construct($db, $type, $id = 0, $options = array())
    {
		$this->db = $db;
		$this->type = $type;
        $this->id = $id;
        if (!is_array($options)) {
            throw new Exception('Options must be an array!');
        }
        $this->options = $options;

        $this->extra_condition_select = '';
        $this->extra_condition_update = '';
        if (isset($this->options['extra_condition']) && is_array($this->options['extra_condition'])) {
            foreach ($this->options['extra_condition'] AS $condition) {
                $this->extra_condition_select .= ' AND '.$condition;
                $this->extra_condition_update .= ', '.$condition;
            }
        }

        if ($this->id > 0) {
            $this->load();
        }
	}

    /**
     * get category type
     *
     * @return Category_Type
     */
	public function getType()
    {
		return $this->type;
	}

    /**
     * set category name
     *
     * @param String $name name of category
     *
     * @return void
     */
	public function setName($name)
    {
		$this->name = $name;
	}

	/**
     * get category name
     *
     * @return String
     */
	public function getName()
    {
		return $this->name;
	}


    /**
     * set category identitier
     *
     * @param String $identifier identifier of category
     *
     * @return void
     */
	public function setIdentifier($identifier)
    {
		$this->identifier = $identifier;
	}

	/**
     * get category identifier
     *
     * @return String
     */
	public function getIdentifier()
    {
		return $this->identifier;
	}

    /**
     * set category parent id
     *
     * @param Integer $parent_id id of parent category
     *
     * @return void
     */
	public function setParentId($parent_id)
    {
		$this->parent_id = $parent_id;
	}

	/**
     * get parent id
     *
     * @return Integer
     */
	public function getParentId()
    {
		return $this->parent_id;
	}

	/**
     * get id
     *
     * @return Integer
     */
	public function getId()
    {
		return $this->id;
	}

	/**
     * load from db
     *
     * @return void
     */
	private function load()
    {
        $result = $this->db->query(
        		"SELECT * FROM ilib_category " .
        		"WHERE id = " . intval($this->id) . " " .
                "AND belong_to = ".$this->db->quote($this->type->getBelongTo(), 'integer')." " .
                "AND belong_to_id = ".$this->db->quote($this->type->getBelongToId(), 'integer')." " .
                $this->extra_condition_select .
        		";");
        if (PEAR::isError($result)) {
        	throw new Exception("Error in query: " . $result->getUserInfo());
        }

        if ($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$this->name = $row['name'];
			$this->identifier = $row['identifier'];
			$this->parent_id = $row['parent_id'];
			$this->id = $row['id'];
        } else {
        	throw new Exception('wrong id or type "'.$this->id.'"');
        }
	}

	/**
     * save to db
     *
     * @return void
     */
	public function save()
    {
		if ($this->type === null ||
			$this->name === null ||
			$this->identifier === null ||
			$this->parent_id === null) {
				throw new Exception('one of the parameters is not set');
		}

		$sql =	" ilib_category " .
				"SET " .
				"belong_to = ".$this->db->quote($this->type->getBelongTo(), 'integer').", " .
				"belong_to_id = ".$this->db->quote($this->type->getBelongToId(), 'integer').", " .
				"parent_id = ".$this->db->quote($this->parent_id, 'integer').", " .
				"name = ".$this->db->quote($this->name, 'text').", " .
				"identifier = ".$this->db->quote($this->identifier, 'text').
                $this->extra_condition_update;

		if ($this->id == 0) {
	        $result = $this->db->exec("INSERT INTO" . $sql . ";");
	        if (PEAR::isError($result)) {
	        	throw new Exception("Error in query: " . $result->getUserInfo());
	        }
		    $this->id = $this->db->lastInsertID();
		} else {
	        $result = $this->db->exec("UPDATE " . $sql . " WHERE id = " . $this->db->quote($this->id, 'text') . ";");
	        if (PEAR::isError($result)) {
	        	throw new Exception("Error in query: " . $result->getUserInfo());
	        }
		}
        return true;
	}
	
	/**
	 * Delete a loaded category
	 * @return void
	 */
	public function delete()
	{
	    if($this->id == 0) {
	        throw new Exception('You cannot delete if no id is set');
	    }
	    
	    $result = $this->db->exec("UPDATE ilib_category SET active = 0 WHERE id = ".$this->id.$this->extra_condition_select);
        if (PEAR::isError($result)) {
            throw new Exception("Error in delete: " . $result->getUserInfo());
        }
	    
	}

	/**
     * get sub categories
     *
     * @return array (id as key, identifier as value)
     */
	public function getSubCategories()
    {
        $result = $this->db->query(
        		"SELECT * FROM ilib_category " .
        		"WHERE belong_to = " . $this->getType()->getBelongTo() . 
                    " AND belong_to_id = " . $this->getType()->getBelongToId() .
                    " AND parent_id = " . $this->id .
                    " AND active = 1" .
                $this->extra_condition_select);
        if (PEAR::isError($result)) {
        	throw new Exception("Error in query: " . $result->getUserInfo());
        }
        $sub = array();
        while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        	$sub[$row['id']] = $row['identifier'];
		}
		return $sub;
	}


    /**
     * Returns an recursive array with all categories
     *
     * @return array With categories
     */
    public function getAllCategories()
    {
        // We get all categories with one sql call then we organize the categories later.
        $result = $this->db->query( "SELECT * FROM ilib_category " .
                "WHERE belong_to = " . $this->getType()->getBelongTo() . 
                    " AND belong_to_id = " . $this->getType()->getBelongToId() .
                    " AND active = 1 " .  
                    $this->extra_condition_select);

        if (PEAR::isError($result)) {
            throw new Exception("Error in query: " . $result->getUserInfo());
        }

        return $this->getCategoriesByParentId(0, $result->fetchAll(MDB2_FETCHMODE_ASSOC));
    }

    /**
     * Returns all categories with subcategories to a given parent id
     *
     * @param integer $parent_id id on parent category to find subcategories from
     * @param array $categories One dimensional array with all categories
     *
     * @return array Recursiv array with categories and subcategories
     */
    private function getCategoriesByParentId($parent_id, $categories)
    {
        $return = array();
        foreach ($categories AS $category) {
            if ($category['parent_id'] == $parent_id) {
                $return[$category['id']] = array_merge(
                    $category,
                    array('categories' => $this->getCategoriesByParentId($category['id'], $categories))
                );

            }
        }
        return $return;
    }

    /**
     * Returns appender object
     *
     * @return object Ilib_Category_Appender instance
     */
    public function getAppender($object_id)
    {
        require_once 'Ilib/Category/Appender.php';
        return new Ilib_Category_Appender($this->db, $this->type, $object_id, $this->options);
    }
}