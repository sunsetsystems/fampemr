<?php
/* vim: expandtab sw=4 ts=4 sts=4: */
/**
 * tests for environment like OS, PHP, modules, ...
 *
 * @version $Id: Environment_test.php,v 1.1.1.1 2009/05/12 21:27:29 bradymiller Exp $
 * @package phpMyAdmin-test
 */

/**
 *
 */
require_once 'PHPUnit/Framework.php';

class Environment_test extends PHPUnit_Framework_TestCase
{
    public function testPhpVersion()
    {
        $this->assertTrue(version_compare('4.1', phpversion(), '<='),
            'phpMyAdmin requires PHP 4.1 or above');
    }

    public function testMySQL()
    {
        $this->markTestIncomplete();
    }

    public function testSession()
    {
        $this->markTestIncomplete();
    }
}
?>