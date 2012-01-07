<?php
/**
 * @package    EasyCreator
 * @subpackage Paramelements
 * @author     Nikolai Plath
 * @author     Created on 12-Aug-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Draws a checkbox.
 *
 * Extended to provide g11n translations.
 *
 * @package 	EasyCreator
 * @subpackage	Parameter
 *
 * @deprecated when support for J 1.5 is dropped
 */
class JElementCheckbox extends JElement
{
    /**
     * Element name
     *
     * @access   protected
     * @var      string
     */
    protected $_name = 'Checkbox';

    /**
     * Fetch the HTML code for the parameter element.
     *
     * @param string $name The field name.
     * @param mixed $value The value of the field.
     * @param object &$node The current XML node.
     * @param string $control_name The name of the HTML control.
     *
     * @return string
     * @see JElement::fetchElement()
     */
    public function fetchElement($name, $value, &$node, $control_name)
    {
        $class =($node->attributes('class')) ? ' class="'.$node->attributes('class').'"' : ' class="text_area"';
        $checked =($value == 'on') ? ' checked="checked"' : '';

        return '<input type="checkbox" name="'.$control_name.'['.$name.']"'
        .' id="'.$control_name.$name.'"'.$class.$checked.' />';
    }//function
}//class
