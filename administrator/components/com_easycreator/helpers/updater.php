<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 01-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//--No direct access
defined('_JEXEC') || die('=;)');

/**
 * Update Joomla! extensions.
 *
 * @status First draft =;)
 *
 */
class EcrUpdater
{
    /**
     * @var EasyProject
     */
    private $project = null;

    private $hasUpdates = false;

    private $tmpPath = '';

    private $logger = null;

    public function __construct(EasyProject $project, EasyLogger $logger = null)
    {
        if($project instanceof EasyProject)
        {
            $this->project = $project;
            $this->logger = $logger;

            $this->hasUpdates = $this->prepareUpdate();
        }
    }//function

    public function __get($what)
    {
        if(in_array($what, array('hasUpdates', 'tmpPath')))
        {
            return $this->$what;
        }

        EcrHtml::displayMessage(get_class($this).' - Undefined property: '.$what, 'error');
    }//function

    private function log($message)
    {
        if( ! $this->logger)
        return;

        $this->logger->log($message);
    }//function

    private function prepareUpdate()
    {
        jimport('joomla.filesystem.archive');

        $buildsPath = $this->project->getZipPath();

        if( ! JFolder::exists($buildsPath))
        return false;

        $folders = JFolder::folders($buildsPath);

        if( ! $folders)
        return false;

        $tmpPath = '';
        $tmpPath .= JFactory::getConfig()->get('tmp_path');
        $tmpPath .= '/'.$this->project->comName.'_update_'.time();

        $this->log('Temp path is set to '.$tmpPath);

        if( ! JFolder::create($tmpPath))
        {
            EcrHtml::displayMessage('Unable to create temp folder for update', 'error');
            $this->log('Can not create the temp folder '.$tmpPath);

            return false;
        }

        foreach($folders as $folder)
        {
            JFolder::create($tmpPath.'/'.$folder);
            $this->log('Processing version '.$folder);

            $files = JFolder::files($buildsPath.'/'.$folder);

            if( ! $files)
            continue;

            $this->log(sprintf('Found %d package(s) ', count($files)));

            if(1 == count($files))
            {
                //-- Only one file found in folder - take it
                $source = $buildsPath.'/'.$folder.'/'.$files[0];
            }
            else
            {
                //@todo tmp solution for multiple file :-?
                //echo 'more files found....picking '.$files[0];
                $source = $buildsPath.'/'.$folder.'/'.$files[0];//temp
            }

            $this->log('Processing package: '.$source);

            $destination = $tmpPath.'/'.$folder;

            if( ! JArchive::extract($source, $destination))
            {
                EcrHtml::displayMessage(sprintf('Unable to extract the package %s to %s'
                , $source, $destination), 'error');

                return false;
            }
        }//foreach

        $this->tmpPath = $tmpPath;

        return true;
    }//function
}//class
