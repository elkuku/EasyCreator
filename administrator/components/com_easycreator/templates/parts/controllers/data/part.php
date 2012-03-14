<?php
/**
 * @package     EasyCreator
 * @subpackage  Parts
 * @author		Nikolai Plath
 * @author		Created on 18-Aug-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class PartControllersData
{
    public $group = 'controllers';

    public $name = 'data';

    public $key = '';

    /**
     * Info about the thing.
     *
     * @return EcrTemplateinfo
     */
    public function info()
    {
        $info = new EcrTemplateinfo;

        $info->group = ucfirst($this->group);
        $info->title = ucfirst($this->name);
        $info->description = jgettext('Provides methods to modify data with a specific model');

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

        $requireds[] = EcrHtml::drawSelectScope(JRequest::getCmd('scope'));
        $requireds[] = EcrHtml::drawSelectName(JRequest::getCmd('element'));

        EcrHtml::drawLoggingOptions();

        EcrHtml::drawSubmitParts($requireds);
    }//function

    /**
     * Open the part for edit.
     *
     * @param object $autoCode The AutoCode
     *
     * @return void
     */
    public function edit($autoCode)
    {
        echo 'Nothing to edit..';

        return;
//#        $AutoCode = $EcrProject->autoCodes[$this->key];
//
// #       $var_scope = $AutoCode->options['varScope'];

        /* Array with required fields */
//        $requireds = array();
//
//        $requireds[] = EcrHtml::drawSelectScope($this->_scope);
//        echo '<input type="hidden" name="element" value="'.$this->_element.'" />';
//
//        /* Draws an input box for a name field */
//        $requireds[] = EcrHtml::drawSelectName($this->_element, jgettext('Table'));
//
//        echo '<strong>Var Scope:</strong><br />';
//        foreach($this->_varScopes as $vScope)
//        {
//            $checked =( $vScope == $var_scope ) ? ' checked="checked"' : '';
//            echo '<input type="radio" name="var_scope" value="'.$vScope.'"
//id="vscope-'.$vScope.'"'.$checked.'> <label for="vscope-'.$vScope.'">'.$vScope.'</label><br />';
//        }//foreach
//
//        /* Draws the submit button */
//        EcrHtml::drawSubmitParts($requireds);
//
    }//function

    /**
     * Inserts the part into the project.
     *
     * @param EcrProjectBase $project EcrProject - The project.
     * @param array $options Insert options.
     * @param object $logger EcrLogger.
     *
     * @return boolean
     */
    public function insert(EcrProjectBase $project, $options, $logger)
    {
        $project->addSubstitute('_ECR_SUBPACKAGE_', 'Controllers');

        return $project->insertPart($options, $logger);
    }//function
}//class
