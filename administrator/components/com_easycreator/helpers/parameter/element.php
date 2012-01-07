<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 18-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('JPATH_BASE') || die('=;)');

/**
 * Parameter base class.
 *
 * The JElement is the base class for all JElement types
 *
 * Extended to provide g11n translations.
 *
 * @deprecated when support for J 1.5 is dropped
 */
class JElement extends JObject
{
    /**
     * element name
     *
     * This has to be set in the final
     * renderer classes.
     *
     * @access	protected
     * @var		string
     */
    protected $_name = null;

    /**
     * reference to the object that instantiated the element
     *
     * @access	protected
     * @var		object
     */
    protected $_parent = null;

    /**
     * Constructor.
     *
     * @param object $parent The parent
     *
     * @access protected
     */
    public function __construct($parent = null)
    {
        $this->_parent = $parent;
    }//function

    /**
     * Get the element name.
     *
     * @access	public
     * @return	string	type of the parameter
     */
    public function getName()
    {
        return $this->_name;
    }//function

    /**
     * Render the element.
     *
     * @param object &$xmlElement Element to render
     * @param string $value The default value
     * @param string $control_name The name of the control
     *
     * @return return_type
     */
    public function render(&$xmlElement, $value, $control_name = 'params')
    {
        $name = $xmlElement->attributes('name');
        $label = $xmlElement->attributes('label');
        $descr = $xmlElement->attributes('description');
        //make sure we have a valid label
        $label = $label ? $label : $name;
        $result[0] = $this->fetchTooltip($label, $descr, $xmlElement, $control_name, $name);
        $result[1] = $this->fetchElement($name, $value, $xmlElement, $control_name);
        $result[2] = $descr;
        $result[3] = $label;
        $result[4] = $value;
        $result[5] = $name;

        return $result;
    }//function

    /**
     * Fetch the tooltip.
     *
     * @param string $label A
     * @param string $description A
     * @param object &$xmlElement A
     * @param string $control_name A
     * @param string $name A
     *
     * @return string
     */
    public function fetchTooltip($label, $description, &$xmlElement, $control_name = '', $name = '')
    {
        $output = '<label id="'.$control_name.$name.'-lbl" for="'.$control_name.$name.'"';

        if($description)
        {
            $output .= ' class="hasTip" title="'.jgettext($label).'::'.jgettext($description).'">';
        }
        else
        {
            $output .= '>';
        }

        $output .= jgettext($label).'</label>';

        return $output;
    }//function

    /**
     * Fetch the HTML code for the parameter element.
     *
     * @param string $name The field name.
     * @param mixed $value The value of the field.
     * @param object &$xmlElement The current XML node.
     * @param string $control_name The name of the HTML control.
     *
     * @return void
     * @see JElement::fetchElement()
     */
    public function fetchElement($name, $value, &$xmlElement, $control_name)
    {
    }//function
}//class
