<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 06-Apr-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$hasInstall = false;
$hasUnInstall = false;
?>

<div class="ecr_floatbox">
<table>
    <tr>
        <th colspan="2" class="infoHeader imgbarleft icon24-install">
            <?php echo jgettext('Install and Uninstall PHP') ?>
        </th>
    </tr>
    <?php if(count($this->installFiles['php'])) :?>
        <tr>
            <th><?php echo jgettext('Folder'); ?></th>
            <th><?php echo jgettext('Name'); ?></th>
        </tr>
        <?php foreach($this->installFiles['php'] as $file) : ?>
        <?php
        if(strpos($file->name, 'install') === 0
        || strpos($file->name, 'script') === 0)
        $hasInstall = true;

        if(strpos($file->name, 'uninstall') === 0)
        $hasUnInstall = true;
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
<?php if( ! $hasInstall) : ?>
	<div class="btn block" onclick="createFile('php', 'install')">
        <i class="img icon16-add"></i>
	    <?php echo jgettext('Create install file'); ?>
    </div>
    <?php endif; ?>
    <?php if( ! $hasUnInstall) : ?>
    <div class="btn block" onclick="createFile('php', 'uninstall')">
        <i class="img icon16-add"></i>
        <?php echo jgettext('Create uninstall file'); ?>
	</div>
<?php endif; ?>
</div>
