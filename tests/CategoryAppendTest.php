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
	
    
//	function createKeyword($id = '1', $keyword = 'test')
//    {
//        return new FakeKeywordAppendKeyword($id, $keyword);
//    }
//
//    ///////////////////////////////////////////////////////////////
//
    function testAddKeyword()
    {
//        $this->assertTrue($this->keyword->addKeyword($this->createKeyword()));
//        $keywords = $this->keyword->getConnectedKeywords();
//        $this->assertEquals(1, $keywords[0]['id']);
        $this->assertEquals('test', 'test');
    }

//    function testAddKeywords()
//    {
//        $keyword = $this->createKeyword();
//        $keyword2 = $this->createKeyword(2, 'test 2');
//        $keywords = array($keyword, $keyword2);
//        $this->assertTrue($this->keyword->addKeywords($keywords));
//        $keywords_connected = $this->keyword->getConnectedKeywords();
//        $this->assertEquals(2, count($keywords_connected));
//    }
//
//    function testGetConnectedKeywords()
//    {
//        $this->keyword->addKeyword($this->createKeyword());
//        $keywords = $this->keyword->getConnectedKeywords();
//        $this->assertEquals(1, $keywords[0]['id']);
//        $this->assertEquals('test', $keywords[0]['keyword']);
//    }
//
//    function testGetUsedKeywords()
//    {
//        $this->keyword->addKeyword($this->createKeyword());
//        $keywords = $this->keyword->getUsedKeywords();
//        $this->assertEquals(1, $keywords[0]['id']);
//        $this->assertEquals('test', $keywords[0]['keyword']);
//    }
//
//    function testDeleteConnectedKeywords()
//    {
//        $this->keyword->addKeyword($this->createKeyword());
//        $this->assertTrue($this->keyword->deleteConnectedKeywords());
//        $keywords = $this->keyword->getConnectedKeywords();
//        $this->assertTrue(empty($keywords));
//    }
//
//    function testGetConnectedKeywordsAsString()
//    {
//        $this->keyword->addKeyword($this->createKeyword());
//        $keyword2 = $this->createKeyword(2, 'test 2');
//        $this->keyword->addKeyword($keyword2);
//        $string = $this->keyword->getConnectedKeywordsAsString();
//        $this->assertEquals('test, test 2', $string);
//    }


    /*
    function testAddKeywordsByString()
    {
        $this->assertTrue($this->keyword->addKeywordsByString('tester, test'));
        $string = $this->keyword->getConnectedKeywordsAsString();
        $this->assertEquals('test, tester', $string);
    }


    */
}
