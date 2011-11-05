<?php
/* vim: expandtab sw=4 ts=4 sts=4: */
/**
 * tests for PMA_get_real_size()
 *
 * @version $Id: FailTest.php,v 1.1.1.1 2009/05/12 21:27:30 bradymiller Exp $
 * @package phpMyAdmin-test
 */

/**
 *
 */
require_once 'PHPUnit/Framework.php';

class FailTest extends PHPUnit_Framework_TestCase
{
    public function testFail()
    {
        $this->assertEquals(0, 1);
    }
}
?>