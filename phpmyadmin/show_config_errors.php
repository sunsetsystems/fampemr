<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Simple wrapper just to enable error reporting and include config
 *
 * @version $Id: show_config_errors.php,v 1.1.1.1 2009/05/12 21:26:42 bradymiller Exp $
 */

/**
 *
 */
echo "Starting to parse config file...\n";

error_reporting(E_ALL);
require './config.inc.php';

?>
