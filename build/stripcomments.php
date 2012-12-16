#!/usr/bin/env php
<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers.Scripts
 * @author     Nikolai Plath {@link https://github.com/elkuku}
 * @author     Created on 25-May-2012
 * @license    GNU/GPL
 */

// We are a valid Joomla entry point.
define('_JEXEC', 1);

// Increase error reporting to that any errors are displayed.
// Note, you would not use these settings in production.
error_reporting(- 1);
ini_set('display_errors', true);

// Bootstrap the application.
require getenv('JOOMLA_PLATFORM_PATH').'/libraries/import.php';

//-- @todo weird_dependency_error :P
defined('JPATH_SITE') || define('JPATH_SITE', 'X');
defined('JPATH_BASE') || define('JPATH_BASE', 'X');

/**
 * An example command line application class.
 *
 * @package     EasyCreator
 * @subpackage  Scripts
 */
class EcrStripWhitespace extends JApplicationCli
{
    /**
     * Execute the application.
     *
     * @throws Exception
     *
     * @return void
     */
    public function doExecute()
    {
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        $this->out()
            ->out('EasyCreator StripWhitespace')
            ->out('=======================')
            ->out();

        $dir = $this->input->get('dir', false, 'string');

        if( ! $dir)
            throw new Exception('Please specify a base directory with \'dir\'', 5);

        $this->out('DIR:'.$dir);

        $files = JFolder::files($dir, '.php', true, true);

        $cnt = 0;

        foreach($files as $file)
        {
            $buffer = php_strip_whitespace($file);

            JFile::write($file, $buffer);

            $cnt ++;
        }

        $this->out(sprintf('%4d files total', $cnt))
            ->out();

        return;
    }
}

try
{
    JApplicationCli::getInstance('EcrStripWhitespace')->execute();
}
catch(Exception $e)
{
    fwrite(STDOUT, $e->getMessage()."\n");

    $code = $e->getCode() ? : 1;

    exit($code);
}
