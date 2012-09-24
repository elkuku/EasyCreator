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

defined('JPATH_SITE') || define('JPATH_SITE', 'x');

/**
 * An example command line application class.
 *
 * This application shows how to access command line arguments.
 *
 * @package    EasyCreator
 * @subpackage Scripts
 */
class EcrBuildhelper extends JApplicationCli
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

        $this->out();
        $this->out('EasyCreator BuildHelper');
        $this->out('=======================');
        $this->out();

        $dir = $this->input->get('dir', false, 'string');

        $ecrBase = $dir.'/admin';

        $this->out('Base: '.$ecrBase);

        $folders = array('data/builds', 'data/deploy', 'data/exports', 'data/logs', 'data/results'
        , 'data/projects', 'data/sync', 'tests');

        $cntAll = 0;

        foreach($folders as $folder)
        {
            $cnt = 0;

            if(false == JFolder::exists($ecrBase.'/'.$folder))
                continue;

            $files = JFolder::files($ecrBase.'/'.$folder, '.', true, true, array('readme.md'));

            foreach($files as $file)
            {
                if(false == JFile::delete($file))
                    throw new Exception('Can not delete file: '.$file, 2);

                $cnt ++;
            }

            $cntAll += $cnt;

            $this->out(sprintf('%4d EasyCreator files have been deleted in %s', $cnt, $folder));
        }

        $this->out(sprintf('%4d files total', $cntAll));
    }
}

try
{
    JApplicationCli::getInstance('EcrBuildhelper')->execute();
}
catch(Exception $e)
{
    fwrite(STDOUT, $e->getMessage()."\n");

    $code = $e->getCode() ?: 1;

    exit($code);
}
