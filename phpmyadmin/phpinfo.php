<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 *
 * @version $Id: phpinfo.php,v 1.1.1.1 2009/05/12 21:26:40 bradymiller Exp $
 */

/**
 * Gets core libraries and defines some variables
 */
define('PMA_MINIMUM_COMMON', true);
require_once './libraries/common.inc.php';


/**
 * Displays PHP information
 */
if ($GLOBALS['cfg']['ShowPhpInfo']) {
    phpinfo();
}
?>
