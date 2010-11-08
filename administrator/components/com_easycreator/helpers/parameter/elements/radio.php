<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Paramelements
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 12-Aug-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Renders a radio element.
 *
 * @package 	EasyCreator
 * @subpackage	Parameter
 */

class JElementRadio extends JElement
{
    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'Radio';

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
        $options = array ();

        foreach($node->children() as $option)
        {
            $val	= $option->attributes('value');
            $text	= jgettext($option->data());
            $options[] = JHtml::_('select.option', $val, $text);
        }//foreach

        return JHtml::_('select.radiolist', $options, ''.$control_name.'['.$name.']'
        , '', 'value', 'text', $value, $control_name.$name, false);
    }//function
}//class
