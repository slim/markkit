<?php
/**
 * PHPUnit test case for PageCopy
 * 
 * The method skeletons below need to be filled in with 
 * real data so that the tests will run correctly. Replace 
 * all EXPECTED_VAL and PARAM strings with real data. 
 * 
 * Created with PHPUnit_Skeleton on 2007-04-09
 */
require_once 'PHPUnit.php';
class PageCopyTest extends PHPUnit_TestCase {

    var $PageCopy;

    function PageCopyTest($name)
    {
        $this->PHPUnit_TestCase($name);
    }

    function setUp()
    {
        require_once '../lib/pagecopy.php';
		$url  = "http://localhost/slim/markkit/test";
		$file = "/home/slim/markkit-glassjoe/markkit/test";
		$copydir = new LocalResource($url, $file);
		$test_url_wf = "$url/test_page_well_formed.html";
		$test_url_ts = "$url/test_page_tag_soup.html";
        $this->PageCopy =& new PageCopy("testcopy.html",$test_url_wf, $copydir);
        $this->PageCopyTS =& new PageCopy("testcopy_ts.html",$test_url_ts, $copydir);
		PageCopy::set_db(new PDO("sqlite2:markkit-test.db"));
		PageCopy::set_root(new LocalResource("http://localhost/dokuwiki", $file));
    }

    function tearDown()
    {
        unset($this->PageCopy);
        unset($this->PageCopyTS);
		@unlink("testcopy.html");
		@unlink("testcopy_ts.html");
    }

	function test_select()
	{
		$pages = PageCopy::select();
		$this->assertTrue(is_a($pages[1], 'PageCopy'));
		$this->assertTrue($pages[1]->file === "/home/slim/markkit-glassjoe/markkit/test/x", "le nom de fichier n'est pas bon : ". $pages[1]->file );
	}

    function testupdate()
    {
        $this->PageCopy->update();
        $this->assertTrue(is_file("testcopy.html"));
    }

	function testadd_to_html()
	{
		$this->PageCopy->add_to_html("<br id='testaddtohtml' />");
		$this->PageCopy->update();
		$c = file_get_contents("testcopy.html");
		$this->assertTrue(ereg("id='testaddtohtml' />\s*</html>", $c));
		$this->assertFalse(ereg("id='testaddtohtml'.*id='testaddtohtml'", $c));
		$this->PageCopyTS->add_to_html("<br id='testaddtohtml' />");
		$this->PageCopyTS->update();
		$c = file_get_contents("testcopy_ts.html");
		$this->assertTrue(ereg("id='testaddtohtml' />", $c));
	}

	function testadd_to_head()
	{
		$this->PageCopy->add_to_head("<br id='testaddtohtml' />");
		$this->PageCopy->update();
		$c = file_get_contents("testcopy.html");
		$this->assertTrue(ereg("id='testaddtohtml' />\s*</head>", $c));
		$this->assertFalse(ereg("id='testaddtohtml'.*id='testaddtohtml'", $c));
		$this->PageCopyTS->add_to_head("<br id='testaddtohtml' />");
		$this->PageCopyTS->update();
		$c = file_get_contents("testcopy_ts.html");
		$this->assertTrue(ereg("id='testaddtohtml' />", $c));
	}

	function testadd_to_head_start()
	{
		$this->PageCopy->add_to_head_start("<br id='testaddtohtml' />");
		$this->PageCopy->update();
		$c = file_get_contents("testcopy.html");
		$this->assertTrue(ereg("<head>\s*<br id='testaddtohtml' />", $c));
		$this->assertFalse(ereg("id='testaddtohtml'.*id='testaddtohtml'", $c));
		$this->PageCopyTS->add_to_head_start("<br id='testaddtohtml' />");
		$this->PageCopyTS->update();
		$c = file_get_contents("testcopy_ts.html");
		$this->assertTrue(ereg("<br id='testaddtohtml' />", $c));
	}
}
// Running the test.
$suite  = new PHPUnit_TestSuite('PageCopyTest');
$result = PHPUnit::run($suite);
echo $result->toHtml();
?>
