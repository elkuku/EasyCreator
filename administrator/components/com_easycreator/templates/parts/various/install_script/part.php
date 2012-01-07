<?php
/**
 * @package    EasyCreator
 * @subpackage	Parts
 * @author		Nikolai Plath (elkuku)
 * @author		Created on 30-Jun-2011
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Part Class for Package installer.
 */
class PartVariousInstall_Script
{
    public $group = 'various';

    /**
     * Info about the thing.
     *
     * @return EasyTemplateInfo
     */
    public function info()
    {
        $info = new EasyTemplateInfo;

        $info->group = ucfirst($this->group);
        $info->title = jgettext('Common install Script');
        $info->description = jgettext('Script containing functions to execute during install, update and uninstall');

        return $info;
    }//function

    /**
     * Get insert options.
     *
     * @return void|boolean false on error
     */
    public function getOptions()
    {
        $project = EasyProjectHelper::getProject();

        if('1.5' == $project->JCompat)
        {
            ecrHTML::displayMessage(jgettext('Install scripts are avilable from Joomla! 1.6 + projects'), 'error');

            return false;
        }

//        var_dump($project);
        $ecr_project = JRequest::getCmd('ecr_project');
        $basePathDest = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$ecr_project;

        if(JFile::exists($basePathDest.DS.'install'.DS.'script.php'))
        {
            ecrHTML::displayMessage(jgettext('This project already has an install file - consider removing it'), 'error');

            return false;
        }

        ecrHTML::drawLoggingOptions();

        ecrHTML::drawSubmitParts();
    }//function

    /**
     * Inserts the part into the project.
     *
     * @param EasyProject $project The project.
     * @param array $options Insert options.
     * @param EasyLogger $logger The EasyLogger.
     *
     * @return boolean
     */
    public function insert(EasyProject $project, $options, EasyLogger $logger)
    {
        $project->addSubstitute('_ECR_SUBPACKAGE_', 'Installer');

        JRequest::setVar('element_scope', 'admin');

        return $project->insertPart($options, $logger);
    }//function
}//class
