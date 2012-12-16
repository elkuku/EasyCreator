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
 * Simple view part.
 *
 * @package    EasyCreator
 * @subpackage Templates.Parts
 */
class PartViewsSimple
{
    public $group = 'views';

    /**
     * Info about the thing.
     *
     * @return EcrProjectTemplateInfo
     */
    public function info()
    {
        $info = new EcrProjectTemplateInfo;

        $info->group = ucfirst($this->group);
        $info->title = 'Simple View';
        $info->description = jgettext('A simple, empty, view');

        return $info;
    }

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
    }

    /**
     * Inserts the part into the project.
     *
     * @param EcrProjectBase $project The project.
     * @param array          $options Insert options.
     * @param EcrLogger      $logger  The EcrLogger.
     *
     * @return boolean
     */
    public function insert(EcrProjectBase $project, $options, EcrLogger $logger)
    {
        $project->addSubstitute('ECR_SUBPACKAGE', 'Views');

        return $project->insertPart($options, $logger);
    }
}
