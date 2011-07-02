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

class extensionUpdater
{
    private $project = null;

    private $hasUpdates = false;

    public function __construct(EasyProject $project)
    {
        if($project instanceof EasyProject)
        {
            $this->project = $project;

            $this->hasUpdates = $this->prepareUpdate();
        }
    }//function

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

        foreach ($folders as $folder)
        {
            JFolder::create($tmpPath.'/'.$folder);

            $files = JFolder::files($buildsPath.'/'.$folder);

            var_dump($files);

            if(1 == count($files))
            {
                $source = $buildsPath.'/'.$folder.'/'.$files[0];
                //-- Only one file found in folder - take it

            }
            else
            {
                echo 'more files found....';
                $source = $buildsPath.'/'.$folder.'/'.$files[0];//temp
            }

            $destination = $tmpPath.'/'.$folder;

            if( ! JArchive::extract($source, $destination))
            return false;


            $this->fileList[$folder] = $buildsPath.'/'.$folder.'/install.sql';
        }//foreach
        ;
    }//function

    private function unpack($source, $destination)
    {
        		// Path to the archive
		$archivename = $p_filename;

		// Temporary folder to extract the archive into
		$tmpdir = uniqid('install_');

		// Clean the paths to use for archive extraction
		$extractdir = JPath::clean(dirname($p_filename) . '/' . $tmpdir);
		$archivename = JPath::clean($archivename);

		// Do the unpacking of the archive
		$result = JArchive::extract($source, $destination);

		if ($result === false) {
			return false;
		}
        ;
    }
}//class
