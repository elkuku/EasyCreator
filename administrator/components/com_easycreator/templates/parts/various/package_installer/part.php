<?php
/**
 * @package    EasyCreator
 * @subpackage	Parts
 * @author		Nikolai Plath (elkuku)
 * @author		Created on 20-Apr-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Part Class for Package installer.
 */
class PartVariousPackage_installer
{
    public $group = 'various';

    /**
     * Info about the thing.
     *
     * @return EcrTemplateInfo
     */
    public function info()
    {
        $info = new EcrProjectTemplateInfo;

        $info->group = ucfirst($this->group);
        $info->title = 'Package Installer';
        $info->description = jgettext('Install and Uninstall routine for packages');

        return $info;
    }//function

    /**
     * Get insert options.
     *
     * @return void|boolean false on error
     */
    public function getOptions()
    {
        $ecr_project = JRequest::getCmd('ecr_project');
        $basePathDest = JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$ecr_project;

        if(JFile::exists($basePathDest.DS.'install'.DS.'install.php')
        || JFile::exists($basePathDest.DS.'install'.DS.'install.package.php')
        || JFile::exists($basePathDest.DS.'install'.DS.'uninstall.php')
        || JFile::exists($basePathDest.DS.'install'.DS.'uninstall.package.php')
        )
        {
            EcrHtml::displayMessage(jgettext('This project already has an install file - consider removing it'), 'error');

            return false;
        }

        EcrHtml::drawLoggingOptions();

        $requireds = array();
        EcrHtml::drawSubmitParts($requireds);
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

        JRequest::setVar('element_scope', 'admin');

        return $project->insertPart($options, $logger);
    }//function
}//class
