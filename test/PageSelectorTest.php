<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

class PageSelectorTest extends UnitTestCase {
	function setUp() {
        require_once '../lib/pageselector.php';
		$this->PageSelector =& new PageSelector("http://localhost/?p=", 2, 10);
		$this->PageSelector2 =& new PageSelector("http://localhost/?p=", 2, 30);
		$this->PageSelector3 =& new PageSelector("http://localhost/?p=", 2, 11);
		$this->PageSelector4 =& new PageSelector("http://localhost/?p=", 0, 11);
	}

	function test_getFirstItem()
	{
		$this->assertEqual(4, $this->PageSelector->getFirstItem(3));
		$this->assertEqual(0, $this->PageSelector->getFirstItem(1));
	}

	function test_getLastPage()
	{
		$this->assertEqual(5, $this->PageSelector->getLastPage());
		$this->assertEqual(6, $this->PageSelector3->getLastPage());
		$this->assertEqual(1, $this->PageSelector4->getLastPage());
	}

	function test_getLinks() {
		$result = $this->PageSelector->getLinks(4);
		$expected = array('next' => 'http://localhost/?p=5',
						  '5' => 'http://localhost/?p=5',
						  '4' => '#',
						  '3' => 'http://localhost/?p=3',
						  '2' => 'http://localhost/?p=2',
						  '1' => 'http://localhost/?p=1',
						  'previous' => 'http://localhost/?p=3');
		$this->assertEqual( $expected, $result );
		$first_page = current($result);
		$this->assertEqual( 'http://localhost/?p=5', $first_page);

		$result = $this->PageSelector2->getLinks(10);
		$expected = array('next' => 'http://localhost/?p=11',
						  '15' => 'http://localhost/?p=15',
						  '14' => 'http://localhost/?p=14',
						  '13' => 'http://localhost/?p=13',
						  '12' => 'http://localhost/?p=12',
						  '11' => 'http://localhost/?p=11',
						  '10' => '#',
						  '9' => 'http://localhost/?p=9',
						  '8' => 'http://localhost/?p=8',
						  '7' => 'http://localhost/?p=7',
						  '6' => 'http://localhost/?p=6',
						  '5' => 'http://localhost/?p=5',
						  'previous' => 'http://localhost/?p=9');
		$this->assertEqual( $expected, $result );
		$result = $this->PageSelector2->getLinks(3);
		$expected = array('next' => 'http://localhost/?p=4',
						  '11' => 'http://localhost/?p=11',
						  '10' => 'http://localhost/?p=10',
						  '9' => 'http://localhost/?p=9',
						  '8' => 'http://localhost/?p=8',
						  '7' => 'http://localhost/?p=7',
						  '6' => 'http://localhost/?p=6',
						  '5' => 'http://localhost/?p=5',
						  '4' => 'http://localhost/?p=4',
						  '3' => '#',
						  '2' => 'http://localhost/?p=2',
						  '1' => 'http://localhost/?p=1',
						  'previous' => 'http://localhost/?p=2');
		$this->assertEqual( $expected, $result );
		$result = $this->PageSelector2->getLinks(14);
		$expected = array('next' => 'http://localhost/?p=15',
						  '15' => 'http://localhost/?p=15',
						  '14' => '#',
						  '13' => 'http://localhost/?p=13',
						  '12' => 'http://localhost/?p=12',
						  '11' => 'http://localhost/?p=11',
						  '10' => 'http://localhost/?p=10',
						  '9' => 'http://localhost/?p=9',
						  'previous' => 'http://localhost/?p=13');
		$this->assertEqual( $expected, $result );
	}
}
// Running the test.
$test = &new PageSelectorTest;
$test->run(new HtmlReporter());
