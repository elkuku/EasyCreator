<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 06-Apr-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$InstallFile = null;
$unInstallFile = null;
?>

<div class="ecr_floatbox">
    <div class="infoHeader img icon24-install"><?php echo jgettext('Install and Uninstall SQL') ?></div>
<table>
    <?php if(count($this->installFiles['sql'])) :?>
        <tr>
            <th><?php echo jgettext('Folder'); ?></th>
            <th><?php echo jgettext('Name'); ?></th>
        </tr>
        <?php foreach($this->installFiles['sql'] as $file) : ?>
            <?php
            if(strpos($file->name, 'install') === 0)
            $InstallFile = $file;

            if(strpos($file->name, 'uninstall') === 0)
            $unInstallFile = $file;
            ?>
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

    <?php if( ! $InstallFile) : ?>
<div class="btn block"
    onclick="createFile('sql', 'install');">
    <i class="img icon16-add"></i>
    <?php echo jgettext('Create install file'); ?>
</div>
    <?php else : ?>
<div class="btn block"
    onclick="createFile('sql', 'install');">
    <i class="img icon16-update"></i>
    <?php echo jgettext('Update install file'); ?>
</div>
    <?php endif; ?>

    <?php if( ! $unInstallFile) : ?>
<div class="btn block" onclick="createFile('sql', 'uninstall')">
    <i class="img icon16-add"></i>
    <?php echo jgettext('Create uninstall file'); ?>
</div>
    <?php else : ?>
<div class="btn block" onclick="createFile('sql', 'uninstall')">
    <i class="img icon16-update"></i>
    <?php echo jgettext('Update uninstall file'); ?>
</div>
    <?php endif; ?>

</div>

<input type="hidden" name="type1" id="type1" />
<input type="hidden" name="type2" id="type2" />

