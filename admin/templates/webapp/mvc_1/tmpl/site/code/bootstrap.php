<?php
##*HEADER*##
/**
 * Bootstrap file for the ECR_COM_NAME application.
 */

// Allow the application to run as long as is necessary.
ini_set('max_execution_time', 0);

// Note, you would not use these settings in production.
error_reporting(- 1);
ini_set('display_errors', true);

// Define the path for the Joomla Platform.
if(! defined('JPATH_PLATFORM'))
{
    $platform = getenv('JOOMLA_PLATFORM_PATH_12_1');

    if($platform)
    {
        define('JPATH_PLATFORM', realpath($platform.'/libraries'));
    }
    else
    {
        $platform = getenv('JOOMLA_PLATFORM_PATH');

        if($platform)
        {
            define('JPATH_PLATFORM', realpath($platform.'/libraries'));
        }
        else
        {
            define('JPATH_PLATFORM', realpath(__DIR__.'/../../joomla/libraries'));
        }
    }
}

// Ensure that required path constants are defined.
defined('JPATH_BASE') || define('JPATH_BASE', realpath(__DIR__));
defined('JPATH_ROOT') || define('JPATH_ROOT', JPATH_BASE);
defined('APP_PATH_DATA') || define('APP_PATH_DATA', realpath(JPATH_ROOT.'/../data'));
defined('APP_PATH_TEMPLATE') || define('APP_PATH_TEMPLATE', realpath(JPATH_ROOT.'/../www/template'));

// Import the platform(s).
require_once JPATH_PLATFORM.'/import.php';

// Make sure that the Joomla Platform has been successfully loaded.
if(! class_exists('JLoader'))
    throw new Exception('Joomla Platform not loaded.', 1);

// Setup the autoloader for the ECR_COM_NAME application classes.
JLoader::registerPrefix('ECR_CLASS_PREFIX', __DIR__);

JLog::addLogger(
    array(
        'text_file_path' => APP_PATH_DATA
    , 'text_file' => 'log.php'
    , 'text_entry_format' => '{DATETIME}	{PRIORITY}	{MESSAGE}'
//	, 'text_file_no_php' => true
    )
    , JLog::INFO | JLog::WARNING | JLog::ERROR
);
