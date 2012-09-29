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
class PartControllersSimple30
{
    public $group = 'controllers';

    /**
     * Info about the thing.
     *
     * @return EcrProjectTemplateInfo
     */
    public function info()
    {
        $info = new EcrProjectTemplateInfo;

        $info->group = ucfirst($this->group);
        $info->title = 'Simple 3L';
        $info->description = jgettext('A simple, empty, controller').' - J! 3.0 Legacy';

        return $info;
    }//function

    /**
     * Get insert options.
     *
     * @return void
     */
    public function getOptions()
    {
        EcrHtmlSelect::scope();
        EcrHtmlSelect::name();

        EcrHtmlOptions::logging();

        $requireds = array('element_name', 'element_scope');
        EcrHtmlButton::submitParts($requireds);
    }//function

    /**
     * Inserts the part into the project.
     *
     * @param EcrProjectBase $easyProject The project.
     * @param array $options Insert options.
     * @param EcrLogger $easyLogger The EcrLogger.
     *
     * @return boolean
     */
    public function insert(EcrProjectBase $easyProject, $options, EcrLogger $easyLogger)
    {
        $easyProject->addSubstitute('ECR_SUBPACKAGE', 'Controllers');

        return $easyProject->insertPart($options, $easyLogger);
    }//function
}//class
