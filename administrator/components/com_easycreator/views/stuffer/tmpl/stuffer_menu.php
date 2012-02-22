<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 29-Dec-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */
?>

<div class="ecr_floatbox">
    <div class="infoHeader img icon-24-menu"><?php echo jgettext('Menu'); ?></div>

    <input type="hidden" name="menu[menuid]" value="<?php echo $this->project->menu['menuid']; ?>"/>
    <span class="ecr_label2"><?php echo jgettext('Text'); ?></span>

    <input type="text" name="menu[text]" size="15" style="font-size: 1.3em;"
           value="<?php echo $this->project->menu['text']; ?>"/>
    <span class="ecr_label2"><?php echo jgettext('Link'); ?></span>

    <input type="text" name="menu[link]" size="25"
           value="<?php echo $this->project->menu['link']; ?>"/>
    <br/>

    <span class="ecr_label2"><?php echo jgettext('Image'); ?></span>
    <div id="menuPic-" style="display: inline;">
        <!-- To be filled by javascript -->
    </div>
    <div id="prev-" style="display: inline;"></div>
    <input type="text" name="menu[img]" id="img-" size="35"
           value="<?php echo $this->project->menu['img']; ?>"/>

    <div class="infoHeader img icon-24-menu"><?php echo jgettext('Submenu'); ?></div>

    <span style="float: right;" class="ecr_button img icon-16-add"
          onclick="newSubmenu('', '', '', '', '', '<?php echo $this->project->menu['menuid']; ?>');">
        <?php echo jgettext('Add Submenu'); ?>
    </span>

    <div style="clear: both"></div>

    <ul id="divSubmenu" class="sortable">
        <!--                            -->
        <!--          Submenu           -->
        <!-- To be filled by javascript -->
        <!--                            -->
    </ul>
</div>

