#!/usr/bin/php
<?php
// We are a valid Joomla entry point.
// This is required to load the Joomla Platform import.php file.
define('_JEXEC', 1);

?>
<?php
##*HEADER*##

// Setup the base path related constant.
// This is one of the few, mandatory constants needed for the Joomla Platform.
define('JPATH_BASE', dirname(__FILE__));

// Bootstrap the application.
require getenv('JOOMLA_PLATFORM_PATH').'/libraries/import.php';

// Import the JCli class from the platform.
jimport('joomla.application.cli');

/**
 * A "hello world" command line application class.
 *
 * Simple command line applications extend the JApplicationCli class.
 *
 * @package _ECR_COM_NAME_
 */
class _ECR_COM_NAME_ extends JApplicationCli
{
    /**
     * Execute the application.
     *
     * The 'execute' method is the entry point for a command line application.
     *
     * @return void
     */
    public function execute()
    {
        // Send a string to standard output.
        $this->out('Hello world!');
    }
}

// Instantiate the application object and execute the application.
JApplicationCli::getInstance('_ECR_COM_NAME_')->execute();
