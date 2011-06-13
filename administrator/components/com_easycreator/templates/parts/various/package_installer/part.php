<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage	Parts
 * @author		Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
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
     * @return object ecrTemplateInfo
     */
    public function info()
    {
        $info = new EasyTemplateInfo;

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
            ecrHTML::displayMessage(jgettext('This project already has an install file - consider removing it'), 'error');

            return false;
        }

        ecrHTML::drawLoggingOptions();

        $requireds = array();
        ecrHTML::drawSubmitParts($requireds);
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
