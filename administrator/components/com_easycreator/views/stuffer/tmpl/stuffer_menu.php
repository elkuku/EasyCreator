<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 29-Dec-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');
?>

<div class="ecr_floatbox">
<table>
    <tr valign="top">
        <td>
            <table style="border-bottom: 1px solid black;">
                <tr>
                    <th colspan="2" class="infoHeader imgbarleft icon-24-menu">
                        <?php echo jgettext('Menu') ?>
                    </th>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="hidden" name="menu[menuid]" value="<?php echo $this->project->menu['menuid'] ?>" />
                        <span class="ecr_label2"><?php echo jgettext('Text'); ?></span>
                        <input type="text" name="menu[text]" size="15" style="font-size: 1.3em;"
                        value="<?php echo $this->project->menu['text'] ?>" />
                        <span class="ecr_label2"><?php echo jgettext('Link'); ?></span>
                        <input type="text" name="menu[link]" size="25" value="<?php echo $this->project->menu['link'] ?>" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="ecr_label2"><?php echo jgettext('Image'); ?></span>
                        <div id="menuPic-" style="display: inline;">
                            <!-- To be filled by javascript -->
                        </div>
                        <div id="prev-" style="display: inline;"></div>
                        <input type="text" name="menu[img]" id="img-" size="35"
                        value="<?php echo $this->project->menu['img'] ?>" />
                    </td>
                </tr>
            </table>
            <table width="100%">
                <tr>
                    <th colspan="2" class="infoHeader imgbarleft icon-24-menu">
                        <?php echo jgettext('Submenu') ?>
                    </th>
                </tr>
                <tr>
                    <td colspan="2">
                    </td>
                </tr>
            </table>
            <span style="float: right;" class="ecr_button img icon-16-add"
            onclick="newSubmenu('', '', '', '', '', '<?php echo $this->project->menu['menuid']; ?>');">
                <?php echo jgettext('Add Submenu'); ?>
            </span>
        </td>
    </tr>
</table>
<?php
$class =('1.5' != ECR_JVERSION) ? 'class="sortable"' : '';
?>
<ul id="divSubmenu" <?php echo $class; ?>>
    <!--                            -->
    <!--          Submenu            -->
    <!-- To be filled by javascript -->
    <!--                            -->
</ul>
</div>
