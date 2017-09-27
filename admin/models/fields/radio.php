<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') || die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * Extended  to provide g11n translations.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldRadio extends EcrFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    protected $type = 'Radio';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     * @since   11.1
     */
    protected function getInput()
    {
        //-- Initialize variables.
        $html = array();

        //-- Initialize some field attributes.
        $class = $this->element['class']
        ? ' class="radio '.(string)$this->element['class'].'"'
        : ' class="radio"';

        //-- Start the radio field output.
        $html[] = '<fieldset id="'.$this->id.'"'.$class.'>';

        //-- Get the field options.
        $options = $this->getOptions();

        //-- Build the radio field output.
        foreach($options as $i => $option)
        {
            //-- Initialize some option attributes.
            $checked	= ((string)$option->value == (string)$this->value) ? ' checked="checked"' : '';
            $class		= !empty($option->class) ? ' class="'.$option->class.'"' : '';
            $disabled	= !empty($option->disable) ? ' disabled="disabled"' : '';

            //-- Initialize some JavaScript option attributes.
            $onclick	= !empty($option->onclick) ? ' onclick="'.$option->onclick.'"' : '';

            $html[] = '<input type="radio" id="'.$this->id.$i.'" name="'.$this->name.'"'
            .' value="'.htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8').'"'
            .$checked.$class.$onclick.$disabled.'/>';

            $html[] = '<label for="'.$this->id.$i.'"'.$class.'>'.jgettext($option->text).'</label>';
        }

        //-- End the radio field output.
        $html[] = '</fieldset>';

        return implode("\n", $html);
    }

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     * @since   11.1
     */
    protected function getOptions()
    {
        //-- Initialize variables.
        $options = array();

        foreach($this->element->children() as $option)
        {
            //-- Only add <option /> elements.
            if($option->getName() != 'option')
            continue;

            //-- Create a new option object based on the <option /> element.
            $tmp = JHtml::_('select.option', (string)$option['value'], trim((string)$option)
            , 'value', 'text', ((string)$option['disabled'] == 'true'));

            //-- Set some option attributes.
            $tmp->class = (string)$option['class'];

            //-- Set some JavaScript option attributes.
            $tmp->onclick = (string)$option['onclick'];

            //-- Add the option object to the result set.
            $options[] = $tmp;
        }

        reset($options);

        return $options;
    }
}
