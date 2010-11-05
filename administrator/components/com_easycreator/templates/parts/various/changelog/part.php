<?php
/**
 * @version $Id$
 * @package    EasyCreator
 * @subpackage	Parts
 * @author		EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author		Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 20-Apr-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Part class for Changelog.
 */
class PartVariousChangelog
{
    public $group = 'various';

    /**
     * Info about the thing.
     *
     * @return object ecrTemplateInfo
     */
    public function info()
    {
        $info = new EasyTemplateInfo();

        $info->group = ucfirst($this->group);
        $info->title = 'Changelog';
        $info->description = jgettext('A standard, empty, Changelog');

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

        if(JFile::exists($basePathDest.DS.'CHANGELOG.php'))
        {
            ecrHTML::displayMessage(jgettext('This project already has a changelog'), 'error');

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
        JRequest::setVar('element_scope', 'admin');

        return $project->insertPart($options, $logger);
    }//function
}//class
