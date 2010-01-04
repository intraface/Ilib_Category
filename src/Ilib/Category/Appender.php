<?php
/**
 * Handles appending categories to a given object
 *
 * PHP version 5
 *
 * @category Ilib
 * @package  Ilib_Category
 * @author   Kasper Broegaard Simonsen <kasper@broegaard.com>
 * @author   Mads
 *
 */

/**
 * Handles appending categories to a given object
 *
 * @category Ilib
 * @package  Ilib_Category
 * @author   Kasper Broegaard Simonsen <kasper@broegaard.com>
 * @author   Mads Thorsted Nielsen <mads@masowich.com>
 *
 */
class Ilib_Category_Appender
{
    /**
     * @var object MDB2
     */
    private $db;

    /**
     * @var integer object id to which the categories are appended
     */
    private $object_id;

    /**
     * @var string extra conditions for select sql queries
     */
    private $extra_condition_select;

    /**
     * @var string extra conditions for delete sql queries
     */
    private $extra_condition_delete;

    /**
     * @var string extra conditions for update and insert sql queries
     */
    private $extra_condition_update;

    /**
     * Constructor (private because of singleton)
     *
     * @param Object $db database
     *
     * @return void
     */
    public function __construct($db, $type, $object_id, $options = array())
    {
        $this->db = $db;
        $this->object_id = intval($object_id);
        $this->type = $type;


        if (!is_array($options)) {
            throw new Exception('Options must be an array!');
        }
        $this->extra_condition_delete = '';
        $this->extra_condition_select = '';
        $this->extra_condition_update = '';
        if (isset($options['extra_condition']) && is_array($options['extra_condition'])) {
            foreach($options['extra_condition'] AS $condition) {
                $this->extra_condition_delete .= ' AND '.$condition;
                $this->extra_condition_select .= ' AND ilib_category.'.$condition.' AND ilib_category_append.'.$condition;
                $this->extra_condition_update .= ', '.$condition;
            }
        }
    }

    /**
     * add
     *
     * @param Object $category category
     *
     * @return void
     */
    public function add($category)
    {
        $result = $this->db->query(
                "SELECT `ilib_category_append`.id FROM `ilib_category_append` " .
                "INNER JOIN `ilib_category` ON `ilib_category`.id = `ilib_category_append`.category_id " .
                "WHERE ilib_category.belong_to = " . $this->getType()->getBelongTo() . " " .
                    "AND ilib_category.belong_to_id = " . $this->getType()->getBelongToId()." ".
                    "AND object_id = ".$this->db->quote($this->object_id, 'integer')." " .
                    "AND category_id = ".$this->db->quote($category->getId(), 'integer').
        $this->extra_condition_select);

        if (PEAR::isError($result)) {
            throw new Exception("Error in query: " . $result->getUserInfo());
            exit;
        }

        if ($result->numRows() != 0) {
            return true;
        }

        $result = $this->db->exec(
        		"INSERT INTO `ilib_category_append` " .
                "SET " .
                "object_id = ".$this->db->quote($this->object_id, 'integer').", " .
                "category_id = ".$this->db->quote($category->getId(), 'integer').
        $this->extra_condition_update);

        if (PEAR::isError($result)) {
            throw new Exception("Error in query: " . $result->getUserInfo());
            exit;
        }

        return true;
    }

    /**
     * delete
     *
     * @param Object $category category
     *
     * @return void
     */
    public function delete($category)
    {
        $result = $this->db->exec(
        		"DELETE FROM `ilib_category_append` " .
                "WHERE object_id = ".$this->db->quote($this->object_id, 'integer')." " .
                "AND category_id = ".$this->db->quote($category->getId(), 'integer').
        $this->extra_condition_delete);
        if (PEAR::isError($result)) {
            throw new Exception("Error in query: " . $result->getUserInfo());
            exit;
        }

        return true;
    }

    /**
     * Returns the categories to the object
     *
     *
     */
    public function getCategories()
    {
        $result = $this->db->query(
                "SELECT ilib_category.id, ilib_category.belong_to, ilib_category.belong_to_id, ilib_category.parent_id, ilib_category.name, ilib_category.identifier FROM ilib_category " .
                "INNER JOIN ilib_category_append " .
                "ON ilib_category_append.category_id = ilib_category.id " .
                "WHERE ilib_category.belong_to = " . $this->getType()->getBelongTo() . " " .
                    "AND ilib_category.active = 1 " .
                    "AND ilib_category.belong_to_id = " . $this->getType()->getBelongToId()." ".
                    "AND ilib_category_append.object_id = " . $this->object_id .
        $this->extra_condition_select.";");

        if (PEAR::isError($result)) {
            throw new Exception("Error in query: " . $result->getUserInfo());
            exit;
        }
        $sub = array();
        while($row = $result->fetchRow(MDB2_FETCHMODE_ASSOC)) {
            $sub[] = $row;

        }
        return $sub;
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


    /*
     * get objects
     *
     * @param Object $category category
     *
     * @return void

     Not used anymore
     public function getObjects($category)
     {
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
     */
}
?>