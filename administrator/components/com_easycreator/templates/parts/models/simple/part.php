<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage	Parts
 * @author		EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author		Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 20.04.2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 */
class PartModelsSimple
{
    public $group = 'models';

    /**
     * Info about the thing.
     *
     * @return object ecrTemplateInfo
     */
    public function info()
    {
        $info = new EasyTemplateInfo();

        $info->group = ucfirst($this->group);
        $info->title = 'Simple';
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

        $requireds[] = ecrHTML::drawSelectScope('scope');
        $requireds[] = ecrHTML::drawSelectName('element_name');

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
