<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers.HTML
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 16-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML context menu class.
 *
 * @package EasyCreator
 */
abstract class EcrHtmlContextmenu
{
    /**
     * Context menu
     */
    public static function display()
    {
        $input = JFactory::getApplication()->input;

        //--Add css and javascript
        ecrLoadMedia('contextmenu');

        $ajaxLink = 'index.php?option=com_easycreator';
        $ajaxLink .= '&controller=ajax&tmpl=component';
        $ajaxLink .= '&old_task='.$input->get('task');
        $ajaxLink .= '&old_controller='.$input->get('controller');
        $ajaxLink .= '&ecr_project='.$input->get('ecr_project');

        ?>
    <script type="text/javascript">
        SimpleContextMenu.setup({'preventDefault' : true, 'preventForms' : false});
        SimpleContextMenu.attach('pft-file', 'CM1');
        SimpleContextMenu.attach('pft-directory', 'CM2');
    </script>

    <!-- Context menu files -->
    <?php
        $menuEntries = array(
            array(jgettext('New folder'), 'new_folder', 'add')
        , array(jgettext('New file'), 'new_file', 'add')
        , array(jgettext('Rename'), 'rename_file', 'rename')
        , array(jgettext('Delete'), 'delete_file', 'delete')
        );
        ?>
    <ul id="CM1" class="SimpleContextMenu">
        <li class="title"><?php echo jgettext('File'); ?></li>
        <?php
        foreach($menuEntries as $menuEntry)
        {
            self::addEntry($ajaxLink, $menuEntry[0], $menuEntry[1], $menuEntry[2]);
        }//foreach
        ?>
    </ul>

    <!-- Context menu folders -->
    <?php
        $menuEntries = array(
            array(jgettext('New folder'), 'new_folder', 'add')
        , array(jgettext('New file'), 'new_file', 'add')
        , array(jgettext('Rename'), 'rename_folder', 'rename')
        , array(jgettext('Delete'), 'delete_folder', 'delete')
        );
        ?>
    <ul id="CM2" class="SimpleContextMenu">
        <li class="title"><?php echo jgettext('Folder'); ?></li>
        <?php
        foreach($menuEntries as $menuEntry)
        {
            self::addEntry($ajaxLink, $menuEntry[0], $menuEntry[1], $menuEntry[2]);
        }//foreach
        ?>
    </ul>

    <input
        type="hidden" name="act_folder" id="act_folder"/>
    <input
        type="hidden" name="act_file" id="act_file"/>
    <?php
    }

    /**
     * @static
     *
     * @param $ajaxLink
     * @param $title
     * @param $task
     * @param $icon
     */
    private static function addEntry($ajaxLink, $title, $task, $icon)
    {
        ?>
    <li><a class="ecr_modal" onclick="SimpleContextMenu._hide();"
           rel="{handler: 'iframe', size: {x: 600, y: 180}}"
           href="<?php echo $ajaxLink.'&task='.$task; ?>"> <span
        class="img icon16-<?php echo $icon; ?>">
        <?php echo $title; ?> </span>
    </a></li>
    <?php
    }
}
