<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 06-Apr-.2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$checked =($this->project->method == 'upgrade') ? ' checked="checked"' : '';

$updater = new dbUpdater($this->project);
//$files = $updater->parseFiles();
$versions = $updater->getVersions();

?>

<div class="ecr_floatbox">
    <div class="infoHeader img icon-24-update"><?php echo jgettext('Update') ?></div>

    <strong>
        <?php echo jgettext('Method'); ?>
    </strong>

    <input type="checkbox" <?php echo $checked; ?> name="buildvars[method]"
    id="buildvars_method" value="upgrade" />

    <label for="buildvars_method" class="hasEasyTip" title="method=upgrade::<?php echo jgettext('This will perform an upgrade on installing your extension'); ?>">
        <?php echo jgettext('Upgrade'); ?>
    </label>

    <h4><?php echo jgettext('Versions'); ?></h4>
    <?php if($versions) : ?>
        <ul>
        <?php foreach($versions as $version) : ?>
            <li><?php echo $version; ?></li>
        <?php endforeach; ?>
         </ul>
         <div class="ecr_button img icon-16-add"
	onclick="createFile('sql', 'update');"><?php echo jgettext('Create database updates'); ?>
</div>

     <?php endif; ?>
</div>
