<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 *
 * @version $Id: chk_rel.php,v 1.1.1.1 2009/05/12 21:26:42 bradymiller Exp $
 */

/**
 * Gets some core libraries
 */
require_once './libraries/common.inc.php';
require_once './libraries/db_common.inc.php';
require_once './libraries/relation.lib.php';


/**
 * Gets the relation settings
 */
$cfgRelation = PMA_getRelationsParam(TRUE);


/**
 * Displays the footer
 */
require_once './libraries/footer.inc.php';
?>
