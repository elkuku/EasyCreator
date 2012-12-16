<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers.HTML
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 16-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML checkbox class.
 *
 * @package EasyCreator
 */
abstract class EcrHtmlCheck
{
    /**
     * draws a checkbox
     * select if a backup version should be saved
     *
     * @return string
     */
    public static function versioned()
    {
        $params = JComponentHelper::getParams('com_easycreator');
        $save_versioned = JFactory::getApplication()->input->getInt('save_versioned', $params->get('save_versioned'));
        $checked = ($save_versioned) ? ' checked="checked"' : '';
        $html = '<input type="checkbox" name="save_versioned" id="save_versioned" value="1"'.$checked.'>'
            .'<label class="inline" for="save_versioned">'.jgettext('Save versioned').'</label>';

        return $html;
    }
}
