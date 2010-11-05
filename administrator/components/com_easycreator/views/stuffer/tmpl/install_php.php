<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 06-Apr-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$hasInstall = false;
$hasUnInstall = false;
?>

<?php echo ecrHTML::floatBoxStart(); ?>
<table>
    <tr>
        <th colspan="2" class="infoHeader imgbarleft icon-24-install">
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
        if(strpos($file->name, 'install') === 0) $hasInstall = true;
        if(strpos($file->name, 'uninstall') === 0) $hasUnInstall = true;
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
	<div class="ecr_button img icon-16-add" onclick="createFile('php', 'install')">
	    <?php echo jgettext('Create install file'); ?>
	</div>
<?php endif; ?>
<?php if( ! $hasUnInstall) : ?>
	<div class="ecr_button img icon-16-add" onclick="createFile('php', 'uninstall')">
	    <?php echo jgettext('Create uninstall file'); ?>
	</div>
<?php endif; ?>

<?php echo ecrHTML::floatBoxEnd();
