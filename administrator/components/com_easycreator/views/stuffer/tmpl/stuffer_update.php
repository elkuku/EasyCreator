<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 29-Dec-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$upgradeChecked = ($this->project->method == 'upgrade') ? ' checked="checked"' : '';

$js = '';

foreach($this->project->updateServers as $server) :
    $js .= "   Stuffer.addUpdateServer('$server->name', '$server->url', '$server->type', '$server->priority');\n";
endforeach;

$js = "window.addEvent('domready', function() {\n".$js."\n});";

JFactory::getDocument()->addScriptDeclaration($js);
?>

<div class="ecr_floatbox">
    <div class="infoHeader imgbarleft icon-24-update"><?php echo jgettext('Update') ?></div>
    <strong><?php echo jgettext('Method'); ?></strong>
    <input type="checkbox" <?php echo $upgradeChecked; ?>
           name="buildvars[method]" id="buildvars_method" value="upgrade"/>

    <label for="buildvars_method" class="hasEasyTip"
           title="method=upgrade::<?php echo jgettext('This will perform an upgrade on installing your extension'); ?>">
        <?php echo jgettext('Upgrade'); ?>
    </label>

    <?php //-- @Joomla!-compat 1.5 ?>
    <?php if('1.5' != $this->project->JCompat) : ?>
        <h4><?php echo jgettext('Update server'); ?></h4>
        <div id="updateServers"></div>
        <div onclick="Stuffer.addUpdateServer('<?php echo $this->project->name?> update server', '', 'extension', '1');"
             class="ecr_button img icon-16-add">
            <?php echo jgettext('Add Server');?>
        </div>
    <?php endif; ?>
</div>
