<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
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
class extensionUpdater
{
    private $project = null;

    private $hasUpdates = false;

    private $tmpPath = '';

    public function __construct(EasyProject $project)
    {
        if($project instanceof EasyProject)
        {
            $this->project = $project;

            $this->hasUpdates = $this->prepareUpdate();
        }
    }//function

    public function __get($what)
    {
        if(in_array($what, array('hasUpdates', 'tmpPath')))
        {
            return $this->$what;
        }

        ecrHTML::displayMessage(get_class($this).' - Undefined property: '.$what, 'error');
    }

    private function prepareUpdate()
    {
        jimport('joomla.filesystem.archive');

        $buildsPath = ECRPATH_BUILDS.'/'.$this->project->comName;

        if( ! JFolder::exists($buildsPath))
        return false;

        $folders = JFolder::folders($buildsPath);

        if( ! $folders)
        return false;

        $tmpPath = '';
        $tmpPath .= JFactory::getConfig()->get('tmp_path');
        $tmpPath .= '/'.$this->project->comName.'_update_'.time();

        if( ! JFolder::create($tmpPath))
        {
            ecrHTML::displayMessage('Unable to create temp folder for update', 'error');

            return false;
        }

        foreach($folders as $folder)
        {
            JFolder::create($tmpPath.'/'.$folder);

            $files = JFolder::files($buildsPath.'/'.$folder);

            var_dump($files);

            if(1 == count($files))
            {
                //-- Only one file found in folder - take it
                $source = $buildsPath.'/'.$folder.'/'.$files[0];
            }
            else
            {
                //@todo tmp solution for multiple file :-?
                echo 'more files found....picking '.$files[0];
                $source = $buildsPath.'/'.$folder.'/'.$files[0];//temp
            }

            $destination = $tmpPath.'/'.$folder;

            if( ! JArchive::extract($source, $destination))
            {
                ecrHTML::displayMessage(sprintf('Unable to extract the package %s to %s'
                , $source, $destination), 'error');

                return false;
            }
        }//foreach

        $this->tmpPath = $tmpPath;

        return true;
    }//function
}//class
