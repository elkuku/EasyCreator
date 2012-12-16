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
     * @return EcrProjectTemplateInfo
     */
    public function info()
    {
        $info = new EcrProjectTemplateInfo;

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
       // $project = EcrProjectHelper::getProject();

        $ecr_project = JFactory::getApplication()->input->get('ecr_project');
        $basePathDest = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$ecr_project;

        if(JFile::exists($basePathDest.DS.'install'.DS.'script.php'))
        {
            EcrHtml::message(jgettext('This project already has an install file - consider removing it'), 'error');

            return false;
        }

        EcrHtmlOptions::logging();

        EcrHtmlButton::submitParts();

        return $this;
    }//function

    /**
     * Inserts the part into the project.
     *
     * @param EcrProjectBase $project The project.
     * @param array $options Insert options.
     * @param EcrLogger $logger The EcrLogger.
     *
     * @return boolean
     */
    public function insert(EcrProjectBase $project, $options, EcrLogger $logger)
    {
        $project->addSubstitute('ECR_SUBPACKAGE', 'Installer');

        JFactory::getApplication()->input->set('element_scope', 'admin');

        return $project->insertPart($options, $logger);
    }//function
}//class
