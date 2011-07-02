<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 29-Dec-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$upgradeChecked =($this->project->method == 'upgrade') ? ' checked="checked"' : '';
?>

<div class="ecr_floatbox">
    <div class="infoHeader imgbarleft icon-24-update"><?php echo jgettext('Update') ?></div>
    <strong><?php echo jgettext('Method'); ?></strong>
    <input type="checkbox" <?php echo $upgradeChecked; ?>
    name="buildvars[method]" id="buildvars_method" value="upgrade" />

    <label for="buildvars_method" class="hasEasyTip"
    title="method=upgrade::<?php echo jgettext('This will perform an upgrade on installing your extension'); ?>">
        <?php echo jgettext('Upgrade'); ?>
    </label>
</div>
