<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * @version $Id: binlog.lib.php,v 1.1.1.1 2009/05/12 21:26:58 bradymiller Exp $
 */

/**
 *
 */
class PMA_StorageEngine_binlog extends PMA_StorageEngine
{
    /**
     * returns string with filename for the MySQL helppage
     * about this storage engne
     *
     * @return  string  mysql helppage filename
     */
    function getMysqlHelpPage()
    {
        return 'binary-log';
    }
}

?>
