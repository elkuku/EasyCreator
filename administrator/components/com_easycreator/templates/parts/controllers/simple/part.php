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
 *
 */
class PartControllersSimple
{
    public $group = 'controllers';

    /**
     * Info about the thing.
     *
     * @return EcrTemplateInfo
     */
    public function info()
    {
        $info = new EcrProjectTemplateInfo;

        $info->group = ucfirst($this->group);
        $info->title = 'Simple';
        $info->description = jgettext('A simple, empty, controller');

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
     * @param EcrLogger $easyLogger The EcrLogger.
     *
     * @return boolean
     */
    public function insert(EcrProject $easyProject, $options, EcrLogger $easyLogger)
    {
        $easyProject->addSubstitute('_ECR_SUBPACKAGE_', 'Controllers');

        return $easyProject->insertPart($options, $easyLogger);
    }//function
}//class
