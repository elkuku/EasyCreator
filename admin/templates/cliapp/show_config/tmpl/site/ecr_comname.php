#!/usr/bin/php
<?php
// We are a valid Joomla entry point.
// This is required to load the Joomla Platform import.php file.
define('_JEXEC', 1);

?>
<?php
##*HEADER*##

// Increase error reporting to that any errors are displayed.
// Note, you would not use these settings in production.
error_reporting(- 1);
ini_set('display_errors', true);

/**
 * An example command line application built on the Joomla Platform.
 *
 * To run this example, adjust the executable path above to suite your operating system,
 * make this file executable and run the file.
 *
 * Alternatively, run the file using:
 *
 * php -f run.php
 *
 * @package    Joomla.Examples
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// Setup the base path related constant.
define('JPATH_BASE', dirname(__FILE__));

// Bootstrap the application.
require getenv('JOOMLA_PLATFORM_PATH').'/libraries/import.php';

/**
 * An example command line application class.
 *
 * This application shows how to access configuration file values.
 *
 * @package  ECR_COM_NAME
 */
class ECR_COM_NAME extends JApplicationCli
{
    /**
     * Execute the application.
     *
     * @return  void
     */
    public function doExecute()
    {
        // Print a blank line and new heading.
        $this->out();
        $this->out('Configuration settings loaded from configuration.php:');

        // JApplicationCli will automatically look for and load 'configuration.php'.
        // Use the 'get' method to access any configuration properties.
        $this->out(
            sprintf(
                '%-25s = %2d', 'Default weapon strength',
                $this->get('weapons')
            )
        );

        $this->out(
            sprintf(
                '%-25s = %2d', 'Default armour rating',
                $this->get('armour')
            )
        );

        $this->out(
            sprintf(
                '%-25s = %4.1f', 'Default health level',
                $this->get('health')
            )
        );

        // Print a blank line and new heading.
        $this->out();
        $this->out('System settings:');

        // There are also a number of built in properties available, for example:
        $this->out(
            sprintf(
                '%-25s = %s', 'cwd',
                $this->get('cwd')
            )
        );

        $this->out(
            sprintf(
                '%-25s = %s', 'execution.timestamp',
                $this->get('execution.timestamp')
            )
        );

        $this->out(
            sprintf(
                '%-25s = %s', 'execution.timestamp',
                $this->get('execution.timestamp')
            )
        );

        // Print a blank line and new heading.
        $this->out();
        $this->out('Custom settings:');

        // We can also make custom settings during the execution of the the application using the 'set' method.
        $this->set('race', 'elf');

        $this->out(
            sprintf(
                '%-25s = %s', 'Race',
                $this->get('race')
            )
        );

        // Finish up.
        $this->out();
        $this->out('Thanks for playing!');
        $this->out();
    }
}

// Wrap the execution in a try statement to catch any exceptions thrown anywhere in the script.
try
{
    // Instantiate the application object, passing the class name to JApplicationCli::getInstance
    // and use chaining to execute the application.
    JApplicationCli::getInstance('ECR_COM_NAME')->execute();
}
catch(Exception $e)
{
    // An exception has been caught, just echo the message.
    fwrite(STDOUT, $e->getMessage()."\n");

    exit($e->getCode());
}
