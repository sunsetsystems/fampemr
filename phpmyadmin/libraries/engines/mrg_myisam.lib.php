<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * @version $Id: mrg_myisam.lib.php,v 1.1.1.1 2009/05/12 21:26:58 bradymiller Exp $
 */

/**
 *
 */
include_once './libraries/engines/merge.lib.php';

/**
 *
 */
class PMA_StorageEngine_mrg_myisam extends PMA_StorageEngine_merge
{
    /**
     * returns string with filename for the MySQL helppage
     * about this storage engne
     *
     * @return  string  mysql helppage filename
     */
    function getMysqlHelpPage()
    {
        return 'merge';
    }
}

?>
