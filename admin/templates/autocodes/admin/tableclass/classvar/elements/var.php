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
 * Enter description here ...@todo class doccomment.
 *
 */
class AutoCodeAdminTableclassClassvarElementVar
{
    /**
     * Gets the HTML code.
     *
     * @param EcrTable $table A EcrTable object
     *
     * @return string HTML
     */
    public function getCode(EcrTable $table)
    {
        $ret = '';

        foreach($table->getFields() as $field)
        {
            $ret .= EcrTableHelper::formatTableVar($field->name, $field->type, array($field->label));
            $ret .= NL;
        }//foreach

        return $ret;
    }//function
}//class
