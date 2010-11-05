<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Paramelements
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 12-Aug-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Renders a list element.
 *
 * @package 	EasyCreator
 * @subpackage	Parameter
 */
class JElementList extends JElement
{
    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'List';

    /**
     * Get the options for the element.
     *
     * @param object &$node The current XML node.
     *
     * @return	array
     */
    protected function _getOptions(&$node)
    {
        $options = array ();

        foreach($node->children() as $option)
        {
            $val	= $option->attributes('value');
            $text	= $option->data();
            $options[] = JHtml::_('select.option', $val, jgettext($text));
        }//foreach

        return $options;
    }//function

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
        $ctrl	= $control_name.'['.$name.']';
        $attribs	= ' ';

        if($v = $node->attributes('size'))
        {
            $attribs	.= 'size="'.$v.'"';
        }

        if($v = $node->attributes('class'))
        {
            $attribs	.= 'class="'.$v.'"';
        }
        else
        {
            $attribs	.= 'class="inputbox"';
        }

        if($m = $node->attributes('multiple'))
        {
            $attribs	.= 'multiple="multiple"';
            $ctrl		.= '[]';
        }

        return JHtml::_('select.genericlist',
        $this->_getOptions($node),
        $ctrl,
        array(
            'id' => $control_name.$name,
            'list.attr' => $attribs,
            'list.select' => $value
        )
        );
    }//function
}//class
