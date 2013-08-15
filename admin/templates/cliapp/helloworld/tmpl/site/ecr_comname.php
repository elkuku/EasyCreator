#!/usr/bin/env php
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

// Setup the base path related constant.
// This is one of the few, mandatory constants needed for the Joomla Platform.
define('JPATH_BASE', dirname(__FILE__));

// Bootstrap the application.
require getenv('JOOMLA_PLATFORM_PATH').'/libraries/import.php';

/**
 * A "hello world" command line application class.
 *
 * Simple command line applications extend the JApplicationCli class.
 *
 * @package ECR_COM_NAME
 */
class ECR_COM_NAME extends JApplicationCli
{
    /**
     * Execute the application.
     *
     * The 'execute' method is the entry point for a command line application.
     *
     * @return void
     */
    public function doExecute()
    {
        // Send a string to standard output.
        $this->out('Hello world!');
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
