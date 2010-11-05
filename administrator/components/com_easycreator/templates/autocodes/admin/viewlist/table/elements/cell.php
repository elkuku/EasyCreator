<?php
/**
 * @version $Id$
 * @package		EasyCreator
 * @subpackage	AutoCodes
 * @author		EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author		Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 07-Mar-2010
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class description.
 *
 */
class AutoCodeAdminViewlistTableElementCell
{
    /**
     * Gets the HTML code.
     *
     * @param EasyTable $table A EasyTable object
     * @param string $indent Indentation string
     *
     * @return string HTML
     */
    public function getCode(EasyTable $table, $indent = '')
    {
        $ret = '';

        foreach($table->getFields() as $field)
        {
            if( ! $field->display
            || $field->display === 'off')
            {
                continue;
            }

            $ret .= $indent.'<td>'.NL;
            $ret .= $indent.'    <?php echo $row->'.$field->name.'; ?>'.NL;
            $ret .= $indent.'</td>'.NL;
        }//foreach

        return $ret;
    }//function
}//class
