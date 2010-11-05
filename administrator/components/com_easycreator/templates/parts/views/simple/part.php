<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Parts
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
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
     * @return object ecrTemplateInfo
     */
    public function info()
    {
        $info = new EasyTemplateInfo();

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
        ecrHTML::drawSelectScope();
        ecrHTML::drawSelectName();

        ecrHTML::drawLoggingOptions();

        $requireds = array('element_name', 'element_scope');
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
        $project->addSubstitute('_ECR_SUBPACKAGE_', 'Views');

        return $project->insertPart($options, $logger);
    }//function
}//class
