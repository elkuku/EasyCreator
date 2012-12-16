<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 02-Apr-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');
?>

<div class="ecr_floatbox">
    <div class="infoHeader imgbarleft icon24-personal">
        <?php echo jgettext('Credits') ?>
    </div>
    <ul>
        <li>
            <label for="buildvars-author" class="creditsLabel"><?php echo jgettext('Author'); ?></label>
            <input type="text" id="buildvars-author" name="buildvars[author]" size="30"
                   value="<?php echo $this->project->author; ?>"/>
        </li>
        <li>
            <label for="buildvars-authorEmail" class="creditsLabel"><?php echo jgettext('Author e-mail'); ?></label>
            <input type="text" id="buildvars-authorEmail" name="buildvars[authorEmail]" size="30"
                   value="<?php echo $this->project->authorEmail; ?>"/>
        </li>
        <li>
            <label for="buildvars-authorUrl" class="creditsLabel"><?php echo jgettext('Autor-URL'); ?></label>
            <input type="text" id="buildvars-authorUrl" name="buildvars[authorUrl]" size="30"
                   value="<?php echo $this->project->authorUrl; ?>"/>
        </li>
        <li>
            <label for="buildvars-license" class="creditsLabel"><?php echo jgettext('License'); ?></label>
            <input type="text" id="buildvars-license" name="buildvars[license]" size="30"
                   value="<?php echo $this->project->license; ?>"/>
        </li>
        <li>
            <label for="buildvars-copyright" class="creditsLabel"><?php echo jgettext('Copyright'); ?></label>
            <input type="text" id="buildvars-copyright" name="buildvars[copyright]" size="30"
                   value="<?php echo $this->project->copyright; ?>"/>
        </li>
    </ul>
</div>
