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
    
    function createCategory($key = 1)
    {
        $category = new Ilib_Category($this->db, $this->getDefaultType());
        $category->setIdentifier('min-kategori'.$key);
        $category->setName('Min kategori'.$key);
        $category->setParentId(0);
        $category->save();
        return new Ilib_Category($this->db, $this->getDefaultType(), $category->getId());
    }

    /////////////////////////////////////////////////77
    
    function testConstruct()
    {
        $appender = new Ilib_Category_Appender($this->db, $this->getDefaultType(), 1);
        $this->assertTrue(is_object($appender));
    }
    
    function testAdd()
    {
        
        $appender = new Ilib_Category_Appender($this->db, $this->getDefaultType(), 1);
        $this->assertTrue($appender->add($this->createCategory()));
        
    }
    
    function testDelete()
    {
        
        $appender = new Ilib_Category_Appender($this->db, $this->getDefaultType(), 1);
        $category = $this->createCategory();
        $appender->add($category);
        $this->assertTrue($appender->delete($category));
        
    }
    
    
    function testGetCategories() {
		
        $appender = new Ilib_Category_Appender($this->db, $this->getDefaultType(), 1);
        $appender->add($this->createCategory(1));
        $appender->add($this->createCategory(2));
        $appender->add($this->createCategory(3));
        
        $expected = array(
            0 => array(
                'id' => 1,
                'belong_to' => 1,
                'belong_to_id' => 4,
                'parent_id' => 0,
                'name' => 'Min kategori1',
                'identifier' => 'min-kategori1'
            ),
            1 => array(
                'id' => 2,
                'belong_to' => 1,
                'belong_to_id' => 4,
                'parent_id' => 0,
                'name' => 'Min kategori2',
                'identifier' => 'min-kategori2'
            ),
            2 => array(
                'id' => 3,
                'belong_to' => 1,
                'belong_to_id' => 4,
                'parent_id' => 0,
                'name' => 'Min kategori3',
                'identifier' => 'min-kategori3'
            )
        );
        
        $this->assertEquals($expected, $appender->getCategories());
    }


}
