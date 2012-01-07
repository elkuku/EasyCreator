<?php
/**
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
class PartControllersSimple
{
    public $group = 'controllers';

    /**
     * Info about the thing.
     *
     * @return EasyTemplateInfo
     */
    public function info()
    {
        $info = new EasyTemplateInfo;

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
        ecrHTML::drawSelectScope();
        ecrHTML::drawSelectName();

        ecrHTML::drawLoggingOptions();

        $requireds = array('element_name', 'element_scope');
        ecrHTML::drawSubmitParts($requireds);
    }//function

    /**
     * Inserts the part into the project.
     *
     * @param EasyProject $easyProject The project.
     * @param array $options Insert options.
     * @param EasyLogger $easyLogger The EasyLogger.
     *
     * @return boolean
     */
    public function insert(EasyProject $easyProject, $options, EasyLogger $easyLogger)
    {
        $easyProject->addSubstitute('_ECR_SUBPACKAGE_', 'Controllers');

        return $easyProject->insertPart($options, $easyLogger);
    }//function
}//class
