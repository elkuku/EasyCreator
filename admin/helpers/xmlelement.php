<?php defined('JPATH_BASE') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 29-Feb-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Wrapper class for php SimpleXMLElement.
 */
class EcrXMLElement extends SimpleXMLElement
{
    /**
     * Return a well-formed XML string based on SimpleXML element.
     *
     * @param boolean $compressed Should we use indentation and newlines ?
     * @param string $indent String used to indenting
     * @param integer $level Indentaion level.
     *
     * @return string
     */
    public function asFormattedXML($compressed = false, $indent = '  ', $level = 0)
    {
        $out = '';

        //-- Start a new line, indent by the number indicated in $level
        $out .= ($compressed) ? '' : "\n".str_repeat($indent, $level);

        //-- Add a <, and add the name of the tag
        $out .= '<'.$this->getName();

        //-- For each attribute, add attr="value"
        foreach($this->attributes() as $attr)
        {
            $out .= ' '.$attr->getName().'="'.htmlspecialchars((string)$attr, ENT_COMPAT, 'UTF-8').'"';
        }//foreach

        //-- If there are no children and it contains no data, end it off with a />
        if( ! count($this->children())
        && ! (string)$this)
        {
            $out .= " />";
        }
        else
        {
            //-- If there are children
            if(count($this->children()))
            {
                //-- Close off the start tag
                $out .= '>';

                $level ++;

                //-- For each child, call the asFormattedXML function
                //-- (this will ensure that all children are added recursively)
                foreach($this->children() as $child)
                {
                    $out .= $child->asFormattedXML($compressed, $indent, $level);
                }//foreach

                $level --;

                //-- Add the newline and indentation to go along with the close tag
                $out .=($compressed) ? '' : "\n".str_repeat($indent, $level);
            }
            else if((string)$this)
            {
                //-- If there is data, close off the start tag and add the data
                $out .= '>'.htmlspecialchars((string)$this, ENT_COMPAT, 'UTF-8');
            }

            //-- Add the end tag
            $out .= '</'.$this->getName().'>';
        }

        return $out;
    }//function

    /**
     * Add one simplexml to another.
     *
     * @param object &$xml The XML element to append
     *
     * @return void
     * @author Boris Korobkov
     * @link http://www.ajaxforum.ru/
     */
    public function append(&$xml)
    {
        foreach($xml->children() as $simplexml_child)
        {
            $simplexml_temp = $this->addChild($simplexml_child->getName(), (string)$simplexml_child);

            foreach($simplexml_child->attributes() as $attr_key => $attr_value)
            {
                $simplexml_temp->addAttribute($attr_key, $attr_value);
            }//foreach

            $this->append($simplexml_child);
        }//foreach
    }//function
}//class
