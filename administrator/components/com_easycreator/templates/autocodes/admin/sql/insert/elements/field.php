<?php
/**
 * @version $Id$
 * @package    EasyCreator
 * @subpackage AutoCodes
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 07-Mar-2010
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class AutoCodeAdminSqlInsertElementField
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

        $started = false;

        foreach($table->getFields() as $field)
        {
            $ret .=($started) ? $indent.', ' : $indent.'  ';
            $started = true;
            $ret .= EasyTableHelper::formatSqlField($field);
            $ret .= NL;
        }//foreach

        return $ret;
    }//function
}//class
