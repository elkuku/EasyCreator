<?php
/**
 * @package    EasyCreator
 * @subpackage AutoCodes
 * @author     Nikolai Plath (elkuku)
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
     * @param EcrTable $table A EcrTable object
     * @param string $indent Indentation string
     *
     * @return string HTML
     */
    public function getCode(EcrTable $table, $indent = '')
    {
        $ret = '';

        $started = false;

        foreach($table->getFields() as $field)
        {
            $ret .=($started) ? $indent.', ' : $indent.'  ';
            $started = true;
            $ret .= EcrTableHelper::formatSqlField($field);
            $ret .= NL;
        }//foreach

        return $ret;
    }//function
}//class
