<?php

require_once dirname(__FILE__) . '/config.test.php';

require_once '../src/Ilib/Category.php';
require_once '../src/Ilib/Category/Appender.php';
require_once '../src/Ilib/Category/Type.php';

require_once 'MDB2.php';

$test = new CategoryAppendTest();
$test->setUp();
$test->test();
$test->tearDown();


class CategoryAppendTest
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

		//        $result = $this->db->exec('DROP TABLE ' . $this->table);
    }

    function test()
    {
    	$webshop_id = 4;
		$type = new Ilib_Category_Type('webshop', $webshop_id);
		
		$category = new Ilib_Category($this->db);
		
		$category->setType($type);
		$category->setIdentifier('min-kategori');
		$category->setName('Min kategori');
		$category->setParentId(0);
		$category->save();
		
		$category_hest = new Ilib_Category($this->db);
		
		$category_hest->setType($type);
		$category_hest->setIdentifier('hest');
		$category_hest->setName('Hest');
		$category_hest->setParentId($category->getId());
		$category_hest->save();
		$category_hest->save();
		
		
		$category_ko = new Ilib_Category($this->db);
		$category_ko->load($category_hest->getId(), $type);
		
		print_r($category->getSubCategories());
		
		
		
		
		$object_id = 5;
		$appender = Ilib_Category_Appender::getInstance($this->db);  
		$appender->add($category, $object_id);
		
		print_r($category->getSubObjects());

		$appender->delete($category, $object_id);
		
		print_r($category->getSubObjects());
		
		
		
    }


}
