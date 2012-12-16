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
 * Note, this application requires configuration.php and the connection details
 * for the database may need to be changed to suit your local setup.
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
 * This application shows how to override the constructor
 * and connect to the database.
 *
 * @package  ECR_COM_NAME
 */
class ECR_COM_NAME extends JApplicationCli
{
    /**
     * A database object for the application to use.
     *
     * @var    JDatabase
     * @since  11.3
     */
    protected $dbo = null;

    /**
     * Class constructor.
     *
     * This constructor invokes the parent JApplicationCli class constructor,
     * and then creates a connector to the database so that it is
     * always available to the application when needed.
     *
     */
    public function __construct()
    {
        // Call the parent __construct method so it bootstraps the application class.
        parent::__construct();

        jimport('joomla.database.database');

        // Note, this will throw an exception if there is an error
        // creating the database connection.
        $this->dbo = JDatabase::getInstance(
            array(
                'driver' => $this->get('dbDriver'),
                'host' => $this->get('dbHost'),
                'user' => $this->get('dbUser'),
                'password' => $this->get('dbPass'),
                'database' => $this->get('dbName'),
                'prefix' => $this->get('dbPrefix'),
            )
        );
    }

    /**
     * Execute the application.
     *
     * @return  void
     */
    public function doExecute()
    {
        // Get the query builder class from the database and set it up
        // to select everything in the 'db' table.
        $query = $this->dbo->getQuery(true)
            ->select('*')
            ->from($this->dbo->qn('db'));

        // Push the query builder object into the database connector.
        $this->dbo->setQuery($query);

        // Get all the returned rows from the query as an array of objects.
        $rows = $this->dbo->loadObjectList();

        // Just dump the value returned.
        var_dump($rows);
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
