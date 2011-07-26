<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage	Parts
 * @author		Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 20.04.2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class PartModelsList
{
    public $group = 'models';

    /**
     * Info about the thing.
     *
     * @return EasyTemplateInfo
     */
    public function info()
    {
        $info = new EasyTemplateInfo;

        $info->group = ucfirst($this->group);
        $info->title = 'Data List';
        $info->description = jgettext('A simple, empty, model');

        return $info;
    }//function

    /**
     * Get insert options.
     *
     * @return void
     */
    public function getOptions()
    {
        /* Array with required fields */
        $requireds = array();

        $requireds[] = ecrHTML::drawSelectScope(JRequest::getCmd('scope'));
        $requireds[] = ecrHTML::drawSelectName(JRequest::getCmd('table_name'));

        ecrHTML::drawLoggingOptions();

        ecrHTML::drawSubmitParts($requireds);
    }//function

    /**
     * Inserts the part into the project.
     *
     * @param EasyProject $easyProject The project.
     * @param array $options Insert options.
     * @param EasyLogger $logger The EasyLogger.
     *
     * @return boolean
     */
    public function insert(EasyProject $easyProject, $options, EasyLogger $logger)
    {
        $easyProject->addSubstitute('_ECR_SUBPACKAGE_', 'Models');

        return $easyProject->insertPart($options, $logger);
    }//function
}//class
