<?php
// Set the Joomla execution flag.
define('_JEXEC', 1);

##*HEADER*##
version_compare(PHP_VERSION, '5.3', '>=') || die('This script requires PHP >= 5.3');

try
{
    require_once realpath(__DIR__.'/../code/bootstrap.php');

    // Set all loggers to echo.
    JLog::addLogger(array('logger' => 'echo'), JLog::ALL);

    // Instantiate the application.
    $application = JApplicationWeb::getInstance('ECR_CLASS_PREFIXApplicationWeb');

    // Store the application.
    JFactory::$application = $application;

    // Execute the application.
    $application->execute();
}
catch(Exception $e)
{
    // An exception has been caught, just echo the message.
    echo '<p style="color: red">'.$e->getMessage().'</p>';

    debug_print_backtrace();

    echo '<pre>'.print_r($e, 1).'</pre>';

    JLog::add($e->getMessage(), JLog::ERROR);

    exit($e->getCode());
}
