<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

class UserTest extends UnitTestCase {

    function setUp()
    {
        require_once '../lib/user.php';
		User::set_db(new PDO("sqlite2:markkit-test.db"));
		$this->User = User::fromBookmarklet('testbookmarklet');
    }

    function tearDown()
    {
    }

	function test_fromBookmarklet()
	{
		$result = $this->User->id;
		$expected = "testuser";
        $this->assertEqual($expected, $result);
		$this->User->load();
		$result = $this->User->password;
		$expected = "testpassword";
        $this->assertEqual($expected, $result);
	}

}
// Running the test.
$test =& new UserTest;
$test->run(new HtmlReporter());
?>
