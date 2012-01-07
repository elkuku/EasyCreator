<?php
/**
 * @package    EasyCreator
 * @subpackage Paramelements
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 12-Aug-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Renders a text element.
 *
 * Extended to provide g11n translations.
 *
 * @package 	EasyCreator
 * @subpackage	Parameter
 *
 * @deprecated when support for J 1.5 is dropped
 */

class JElementText extends JElement
{
    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'Text';

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
        $size = ($node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '');
        $class = ($node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"');

        // Required to avoid a cycle of encoding &
        // html_entity_decode was used in place of htmlspecialchars_decode because
        // htmlspecialchars_decode is not compatible with PHP 4

        $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES, 'UTF-8');

        return '<input type="text" name="'.$control_name.'['.$name.']"'
        .' id="'.$control_name.$name.'" value="'.$value.'" '.$class.' '.$size.' />';
    }//function
}//class
