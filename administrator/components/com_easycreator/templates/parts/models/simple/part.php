<?php
/**
 * @package    EasyCreator
 * @subpackage	Parts
 * @author		Nikolai Plath (elkuku)
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
     * @return EcrTemplateinfo
     */
    public function info()
    {
        $info = new EcrTemplateinfo;

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
        EcrHtml::drawSelectScope();
        EcrHtml::drawSelectName();

        EcrHtml::drawLoggingOptions();

        $requireds = array('element_name', 'element_scope');
        EcrHtml::drawSubmitParts($requireds);
    }//function

    /**
     * Inserts the part into the project.
     *
     * @param EcrProject $easyProject The project.
     * @param array $options Insert options.
     * @param EcrLogger $logger The EcrLogger.
     *
     * @return boolean
     */
    public function insert(EcrProject $easyProject, $options, EcrLogger $logger)
    {
        $easyProject->addSubstitute('_ECR_SUBPACKAGE_', 'Models');

        return $easyProject->insertPart($options, $logger);
    }//function
}//class
