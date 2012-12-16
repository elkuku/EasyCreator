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
class AutoCodeSiteViewCategoryTableElementCell
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
            if( ! $field->display)
            {
                continue;
            }

            $ret .= $indent.'<td>'.NL;
            $ret .= $indent.'    <?php echo $item->'.$field->name.'; ?>'.NL;
            $ret .= $indent.'</td>'.NL;
        }//foreach

        return $ret;
    }//function
}//class
