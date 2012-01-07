<?php
/**
 * @package		EasyCreator
 * @subpackage	AutoCodes
 * @author		Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 07-Mar-2010
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class AutoCodeSiteTableclassClassvarElementVar
{
    /**
     * Gets the HTML code.
     *
     * @param EasyTable $table A EasyTable object
     *
     * @return string HTML
     */
    public function getCode(EasyTable $table)
    {
        $ret = '';

        foreach($table->getFields() as $field)
        {
            $ret .= EasyTableHelper::formatTableVar($field->name, $field->type, array($field->label));
            $ret .= NL;
        }//foreach

        return $ret;
    }//function
}//class
