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
class AutoCodeSiteViewCategoryTableElementHeader
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

            $width =($field->width) ? ' width="'.$field->width.'"' : '';

            $ret .= $indent.'<td'.$width.' height="20" class="sectiontableheader'
            .'<?php echo $this->escape($this->params->get(\'pageclass_sfx\')); ?>">'.NL;
            $ret .= $indent.'    <?php echo JHTML::_(\'grid.sort\', \''.$field->label.'\', \''
            .$field->name.'\', $this->lists[\'order_Dir\'], $this->lists[\'order\']); ?>'.NL;
            $ret .= $indent.'</td>'.NL;
        }//foreach

        return $ret;
    }//function
}//class
