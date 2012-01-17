<?php
/**
 * @package    EasyCreator
 * @subpackage Parts
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 20-Apr-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class PartViewsSimple
{
    public $group = 'views';

    /**
     * Info about the thing.
     *
     * @return EasyTemplateInfo
     */
    public function info()
    {
        $info = new EasyTemplateInfo;

        $info->group = ucfirst($this->group);
        $info->title = 'Simple View';
        $info->description = jgettext('A simple, empty, view');

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
     * @param EcrProject $project The project.
     * @param array $options Insert options.
     * @param EcrLogger $logger The EcrLogger.
     *
     * @return boolean
     */
    public function insert(EcrProject $project, $options, EcrLogger $logger)
    {
        $project->addSubstitute('_ECR_SUBPACKAGE_', 'Views');

        return $project->insertPart($options, $logger);
    }//function
}//class
