<?php
/**
 * @package		EasyCreator
 * @subpackage	AutoCodes
 * @author		Nikolai Plath (elkuku)
 * @author		Created on 07-Mar-2010
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class description.
 *
 */
class AutoCodeAdminViewlistTableElementHeader
{
    /**
     * Gets the HTML code.
     *
     * @param EcrTable $table A EcrTable object
     * @param string $indent Indentation string
     *
     * @return string HTML
     */
    public function getCode(EcrTable $table, $indent = '')
    {
        $ret = '';

        foreach($table->getFields() as $field)
        {
            if( ! $field->display
            || $field->display === 'off')
            {
                continue;
            }

            $width =($field->width) ? ' width="'.$field->width.'"' : '';

            $ret .= $indent.'<th'.$width.'>'.NL;
            $ret .= $indent.'    <?php echo JText::_(\''.$field->label.'\'); ?>'.NL;
            $ret .= $indent.'</th>'.NL;
        }//foreach

        return $ret;
    }//function
}//class
