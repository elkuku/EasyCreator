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
 * Part class for Changelog.
 */
class PartVariousChangelog
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
            EcrHtml::displayMessage(jgettext('This project already has a changelog'), 'error');

            return false;
        }

        EcrHtml::drawLoggingOptions();

        EcrHtml::drawSubmitParts();
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
        JRequest::setVar('element_scope', 'admin');

        return $project->insertPart($options, $logger);
    }//function
}//class
