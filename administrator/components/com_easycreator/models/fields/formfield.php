<?php

jimport('joomla.form.formfield');

/**
 * Abstract Form Field class for the Joomla Framework.
 *
 * Extended  to provide g11n translations.
 *
 * Enter description here ...
 *
 */
abstract class EcrFormField extends JFormField
{
    /**
     * Method to get the field label markup.
     *
     * @return  string  The field label markup.
     * @since   11.1
     */
    protected function getLabel()
    {
        //-- Initialise variables.
        $label = '';

        if($this->hidden)
        return $label;

        //-- Get the label text from the XML element, defaulting to the element name.
        $text = $this->element['label'] ? (string)$this->element['label'] : (string)$this->element['name'];
        $text = $this->translateLabel ? jgettext($text) : $text;

        //-- Build the class for the label.
        $class =( ! empty($this->description)) ? 'hasTip' : '';
        $class =($this->required == true) ? $class.' required' : $class;

        //-- Add the opening label tag and main attributes attributes.
        $label .= '<label id="'.$this->id.'-lbl" for="'.$this->id.'" class="'.$class.'"';

        //-- If a description is specified, use it to build a tooltip.
        if( ! empty($this->description))
        {
            $description =($this->translateDescription) ? jgettext($this->description) : $this->description;

            $label .= ' title="'.htmlspecialchars(trim($text, ':').'::'.$description, ENT_COMPAT, 'UTF-8').'"';
        }

        //-- Add the label text and closing tag.
        if($this->required)
        {
            $label .= '>'.$text.'<span class="star">&#160;*</span></label>';
        }
        else
        {
            $label .= '>'.$text.'</label>';
        }

        return $label;
    }//function
}//class
