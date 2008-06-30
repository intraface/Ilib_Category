<?php
require_once dirname(__FILE__) . '/config.test.php';
require_once 'PHPUnit/Framework.php';

require_once '../src/Ilib/Category.php';
require_once '../src/Ilib/Category/Appender.php';
require_once '../src/Ilib/Category/Type.php';

require_once 'MDB2.php';

class CategoryAppendTest extends PHPUnit_Framework_TestCase
{
	private $db;
	
    /////////////////////////////////////////////////////////////
    

    function setUp()
    {
        $this->db = MDB2::factory(DB_DSN);
        if (PEAR::isError($this->db)) {
            die($this->db->getUserInfo());
        }
        
        $result = $this->db->exec('DROP TABLE `ilib_category`');
        /*
         TODO: DROP THE TABLE IF IT EXISTS

        $result = $this->db->exec('DROP TABLE ' . $this->table);

        if (PEAR::isError($result)) {
            die($result->getUserInfo());
        }
        */

        $result = $this->db->exec(
			'CREATE TABLE IF NOT EXISTS `ilib_category` (
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
        
        $result = $this->db->exec(
			"INSERT INTO `ilib_category` (`id`, `belong_to`, `belong_to_id`, `parent_id`, `name`, `identifier`) VALUES
			(1, 1, 4, 0, 'Min kategori', 'min-kategori'),
			(2, 1, 4, 1, 'Hest', 'hest');");
        if (PEAR::isError($result)) {
            die($result->getUserInfo());
        }
        
        $result = $this->db->exec('DROP TABLE `ilib_category_append`');
        
        $result = $this->db->exec(
			'CREATE TABLE IF NOT EXISTS `ilib_category_append` (
			  `id` int(11) NOT NULL auto_increment,
			  `object_id` int(11) NOT NULL,
			  `category_id` int(11) NOT NULL,
			  PRIMARY KEY  (`id`)
			);');
        if (PEAR::isError($result)) {
            die($result->getUserInfo());
        }
	}
    function tearDown()
    {
		$result = $this->db->exec('TRUNCATE TABLE `ilib_category`');
		if (PEAR::isError($result)) {
            die($result->getUserInfo());
        }
		$result = $this->db->exec('TRUNCATE TABLE `ilib_category_append`');
		if (PEAR::isError($result)) {
            die($result->getUserInfo());
        }
        
    }

    function getWebshopType() {
    	return new Ilib_Category_Type('webshop', 4);
    }
    function testCreateType() {
		$type = $this->getWebshopType();
		$this->assertEquals(1, $type->getBelongTo());
    	
    }
    
    function testCreateCategory()
    {
    	$type = $this->getWebshopType();
		
		$category_hest = new Ilib_Category($this->db, $type);
		$category_hest->setIdentifier('hest');
		$category_hest->setName('Hest');
		$category_hest->setParentId(2);
		
		$category_hest->save();	// test INSERT query
		$category_hest->save();	// test UPDATE query
		
//		$category_hest->delete();	// to be implemented
    }
    
    function testLoadCategory() {
    	
		$category = new Ilib_Category($this->db, $this->getWebshopType());
		$category->load(1);
		
		$this->assertEquals(1, $category->getId());
		$this->assertEquals('min-kategori', $category->getIdentifier());
		$this->assertEquals('Min kategori', $category->getName());
		$this->assertEquals(0, $category->getParentId());
    }
    
    function testLoadSubCategory() {
		
		$category = new Ilib_Category($this->db, $this->getWebshopType());
		$category->load(1);
    	
		$subCategories = $category->getSubCategories();
		
    	$this->assertEquals(count($subCategories), 1);
		
    	foreach($subCategories as $key=>$value) {
			$category_hest = new Ilib_Category($this->db, $this->getWebshopType());
			$category_hest->load($key);
			
			$this->assertEquals(2, $category_hest->getId());
			$this->assertEquals('hest', $category_hest->getIdentifier());
			$this->assertEquals('Hest', $category_hest->getName());
			$this->assertEquals(1, $category_hest->getParentId());
    	}
    }
    
    function testAppender() {
		$category = new Ilib_Category($this->db, $this->getWebshopType());
		$category->load(1);
    	
    	
		$category_hest = new Ilib_Category($this->db, $this->getWebshopType());
		$category_hest->load(2);
    	
		$object_id = 5;
		$appender = Ilib_Category_Appender::getInstance($this->db);
		  
		$appender->add($category, $object_id);
		$appender->add($category_hest, $object_id);
		
		
		$objects = $appender->getObjects($category);
		$this->assertEquals(count($objects), 1);
		
		foreach($objects as $key=>$value) {
			$this->assertEquals($value, 5);
		}
		
		$appender->delete($category, $object_id);
		
		$objects = $appender->getObjects($category);
		$this->assertEquals(count($objects), 0);
		
		$objects = $appender->getObjects($category_hest);
		$this->assertEquals(count($objects), 1);
		
		foreach($objects as $key=>$value) {
			$this->assertEquals($value, 5);
		}
		
		$appender->delete($category_hest, $object_id);
		
		$objects = $appender->getObjects($category_hest);
		$this->assertEquals(count($objects), 0);
		
    }


}
