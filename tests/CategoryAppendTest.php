<?php
require_once dirname(__FILE__) . '/config.test.php';
require_once 'PHPUnit/Framework.php';

require_once '../src/Ilib/Category.php';
require_once '../src/Ilib/Category/Appender.php';
require_once '../src/Ilib/Category/Type.php';

require_once 'MDB2.php';

class CategoryAppendTest extends PHPUnit_Framework_TestCase
{
	private $table = 'ilib_category';
	private $db;
	
    /////////////////////////////////////////////////////////////

    function setUp()
    {
        $this->db = MDB2::factory(DB_DSN);
        if (PEAR::isError($this->db)) {
            die($this->db->getUserInfo());
        }
        
        $result = $this->db->exec('DROP TABLE ' . $this->table);
        /*
         TODO: DROP THE TABLE IF IT EXISTS

        $result = $this->db->exec('DROP TABLE ' . $this->table);

        if (PEAR::isError($result)) {
            die($result->getUserInfo());
        }
        */

        $result = $this->db->exec(
			'CREATE TABLE IF NOT EXISTS `' . $this->table . '` (
			  `id` int(11) NOT NULL auto_increment,
			  `belong_to` int(11) NOT NULL,
			  `belong_to_id` int(11) NOT NULL,
			  `parent_id` int(11) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `identifier` varchar(255) NOT NULL,
			  PRIMARY KEY  (`id`)
			);');

        if (PEAR::isError($result)) {
            die($result->getUserInfo());
        }
		$webshop_id = 2;
		$type = new Ilib_Category_Type('webshop', $webshop_id);
		
		$category = new Ilib_Category($this->db, $type);  
		$category->save('Min kategori', 'min-kategori', 0);
		
		$product_id = 5;
		$appender = new Ilib_Category_Appender($type, $product_id);  
		$appender->add($category);
	}
    function tearDown()
    {
//        $result = $this->db->exec('DROP TABLE ' . $this->table);
    }

    function test()
    {
        $this->assertEquals('test', 'test');
    }


}
