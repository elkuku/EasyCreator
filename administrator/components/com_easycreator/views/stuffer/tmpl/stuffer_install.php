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

return;
?>

<div class="ecr_floatbox">
    <table>
        <tr>
            <th colspan="2"
                class="infoHeader imgbarleft icon-24-install"><?php echo jgettext('Install and Uninstall') ?></th>
        </tr>
        <tr>
            <th colspan="2"><?php echo jgettext('PHP'); ?></th>
        </tr>
        <?php if (count($this->installFiles['php'])) : ?>
        <tr>
            <th><?php echo jgettext('Folder'); ?></th>
            <th><?php echo jgettext('Name'); ?></th>
        </tr>
        <?php foreach ($this->installFiles['php'] as $file) : ?>
            <tr>
                <td style="background-color: #cce5ff;"><?php echo $file->folder; ?></td>
                <td><?php echo $file->name; ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else : ?>
        <tr>
            <td colspan="2" style="color: orange;"><?php echo jgettext('Not found'); ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th colspan="2"><?php echo jgettext('SQL'); ?></th>
        </tr>
        <?php if (count($this->installFiles['sql'])) : ?>
        <tr>
            <th><?php echo jgettext('Folder'); ?></th>
            <th><?php echo jgettext('Name'); ?></th>
        </tr>
        <?php foreach ($this->installFiles['sql'] as $file) : ?>
            <tr>
                <td style="background-color: #cce5ff;"><?php echo $file->folder; ?></td>
                <td><?php echo $file->name; ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else : ?>
        <tr>
            <td colspan="2" style="color: orange;"><?php echo jgettext('Not found'); ?></td>
        </tr>
        <?php endif; ?>
    </table>
</div>
