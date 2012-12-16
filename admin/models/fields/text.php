<?php
/**
 * @package    EasyCreator
 * @subpackage Paramelements
 * @author     Nikolai Plath
 * @author     Created on 12-Aug-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.form.formfield');

/**
 * Renders a text element.
 *
 * Extended to provide g11n translations.
 *
 * @package 	EasyCreator
 * @subpackage	Parameter
 */
class JFormFieldText extends EcrFormField
{
    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    protected $type = 'Text';

    protected function getInput()
    {
        //-- Initialize some field attributes.
        $size		= $this->element['size'] ? ' size="'.(int)$this->element['size'].'"' : '';
        $maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int)$this->element['maxlength'].'"' : '';
        $class		= $this->element['class'] ? ' class="'.(string)$this->element['class'].'"' : '';
        $readonly	= ((string)$this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
        $disabled	= ((string)$this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

        //-- Initialize JavaScript field attributes.
        $onchange	= $this->element['onchange'] ? ' onchange="'.(string)$this->element['onchange'].'"' : '';

        return '<input type="text" name="'.$this->name.'" id="'.$this->id.'"'
        .' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"'
        .$class.$size.$disabled.$readonly.$onchange.$maxLength.'/>';
    }//function
}//class
