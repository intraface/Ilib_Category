<?php
require_once dirname(__FILE__) . '/config.test.php';
require_once 'PHPUnit/Framework.php';

require_once '../src/Ilib/Category.php';
require_once '../src/Ilib/Category/Appender.php';
require_once '../src/Ilib/Category/Type.php';

require_once 'MDB2.php';

class CategoryTest extends PHPUnit_Framework_TestCase
{
	private $db;

    function setUp()
    {
        $this->db = MDB2::factory(DB_DSN);
        if (PEAR::isError($this->db)) {
            die($this->db->getUserInfo());
        }
        
        $result = $this->db->exec('TRUNCATE TABLE `ilib_category`');
        if (PEAR::isError($result)) {
            die($result->getUserInfo());
        }
        $result = $this->db->exec('TRUNCATE TABLE `ilib_category_append`');
        if (PEAR::isError($result)) {
            die($result->getUserInfo());
        }
        
        
	}
    
    function tearDown()
    {
		
    }

    function getDefaultType() {
    	return new Ilib_Category_Type('default', 4);
    }
    
    //////////////////////////////////////////////
    
    
    function testCreateType() {
		$type = $this->getDefaultType();
		$this->assertEquals(1, $type->getBelongTo());
    	
    }
    
    function testCreateCategory()
    {
        $category_hest = new Ilib_Category($this->db, $this->getDefaultType());
		$category_hest->setIdentifier('hest');
		$category_hest->setName('Hest');
		$category_hest->setParentId(2);
		
		$this->assertTrue($category_hest->save());	// test INSERT query
        $this->assertTrue($category_hest->save());	// test UPDATE query
		
    }
    
    function testLoadCategory() {
    	
		$category = new Ilib_Category($this->db, $this->getDefaultType());
        $category->setIdentifier('min-kategori');
        $category->setName('Min kategori');
        $category->setParentId(0);
        $category->save();
        
        $category = new Ilib_Category($this->db, $this->getDefaultType(), $category->getId());
		
		$this->assertEquals(1, $category->getId());
		$this->assertEquals('min-kategori', $category->getIdentifier());
		$this->assertEquals('Min kategori', $category->getName());
		$this->assertEquals(0, $category->getParentId());
    }
    
    function testLoadSubCategory() {
		
		$category = new Ilib_Category($this->db, $this->getDefaultType());
        $category->setIdentifier('min-kategori');
        $category->setName('Min kategori');
        $category->setParentId(0);
        $category->save();
        
        $subcategory = new Ilib_Category($this->db, $this->getDefaultType());
        $subcategory->setIdentifier('test2');
        $subcategory->setName('Test2');
        $subcategory->setParentId($category->getId());
        $subcategory->save();
        
        $subcategory = new Ilib_Category($this->db, $this->getDefaultType());
        $subcategory->setIdentifier('test3');
        $subcategory->setName('Test3');
        $subcategory->setParentId($category->getId());
        $subcategory->save();
        
        $category = new Ilib_Category($this->db, $this->getDefaultType(), $category->getId());
    	
        
        $expected = array(
            2 => 'test2',
            3 => 'test3');
        
        $this->assertEquals($expected, $category->getSubCategories());
		
    }
}
