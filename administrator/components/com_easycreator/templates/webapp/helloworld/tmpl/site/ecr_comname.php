<?php
// We are a valid Joomla entry point.
define('_JEXEC', 1);

?>
<?php
##*HEADER*##

// Setup the base path related constant.
define('JPATH_BASE', dirname(__FILE__));

// Increase error reporting to that any errors are displayed.
// Note, you would not use these settings in production.
error_reporting(- 1);
ini_set('display_errors', true);

// Bootstrap the application.
require getenv('JOOMLA_PLATFORM_PATH').'/libraries/import.php';

/**
 * An example Joomla! web application class.
 *
 * @package ECR_COM_NAME
 */
class ECR_COM_NAME extends JApplicationWeb
{
    /**
     * Overrides the parent doExecute method to run the web application.
     *
     * This method should include your custom code that runs the application.
     *
     * @return  void
     */
    protected function doExecute()
    {
        // This application will just output a simple HTML document.
        // Use the setBody method to set the output.
        // JApplicationWeb will take care of all the headers and such for you.

        $this->setBody('
            <html>
                <head>
                    <title>Hello WWW</title>
                </head>
                <body style="font-family:verdana;">
                    <p>Hello WWW!</p>
                </body>
            </html>'
        );
    }
}

// Instantiate the application object, passing the class name to JApplicationWeb::getInstance
// and use chaining to execute the application.
JApplicationWeb::getInstance('ECR_COM_NAME')->execute();
