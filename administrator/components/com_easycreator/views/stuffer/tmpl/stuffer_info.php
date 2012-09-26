<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 02-Apr-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

?>

<div class="ecr_floatbox">
    <div class="infoHeader imgbarleft icon24-info">
        <?php echo jgettext('Info') ?>
    </div>

    <fieldset>
        <div class="control-group">

            <label class="creditsLabel">
                <i class="img icon16-joomla">
                    <?php echo jgettext('Joomla! version'); ?>
                </i>
            </label>

            <input type="radio" id="jversion16" name="jcompat" value="2.5"
                <?php echo ('2.5' == $this->project->JCompat)
                ? ' checked="checked"' : ''; ?>
                />
            <label style="display: inline;" class="inline" for="jversion16">
                <i class="img32b iconjoomla-compat-25"></i>
            </label>

            <input class="inline" type="radio" id="jversion30" name="jcompat" value="3.0"
                <?php echo ('3.0' == $this->project->JCompat) ? ' checked="checked"' : ''; ?>
                />
            <label style="display: inline;" class="inline" for="jversion30">
                <i class="img32b iconjoomla-compat-30"></i>
            </label>

        </div>

        <div class="control-group">
            <label class="creditsLabel">
                <?php echo jgettext('Extension type'); ?>
            </label>

            <div class="controls">

            <span class="img icon12-<?php echo $this->project->type ?>">
            <?php echo $this->project->type ?>
        </span>
            </div>
        </div>
        <div class="control-group">
            <label class="creditsLabel">
                <?php echo jgettext('Name'); ?>
            </label>

            <div class="controls">
                <?php echo $this->project->name; ?>
            </div>
        </div>
        <div class="control-group">
            <label class="creditsLabel">
                <?php echo jgettext('Extension name'); ?>
            </label>
            <?php echo $this->project->comName; ?>
        </div>
        <?php if($this->project->scope): ?>
        <div class="control-group">
            <label class="creditsLabel">
                <?php echo jgettext('Component scope'); ?>
            </label>
            <?php echo $this->project->scope ?>
        </div>
        <?php endif; ?>
        <div class="control-group">

            <label class="creditsLabel" for="buildvars_version">
                <?php echo jgettext('Version'); ?>
            </label>
            <input type="text" id="buildvars_version" name="buildvars[version]"
                   size="10" value="<?php echo $this->project->version; ?>"/>
        </div>
        <div class="control-group">

            <label class="creditsLabel" for="buildvars_description">
                <?php echo jgettext('Description'); ?>
            </label>
            <textarea rows="2" cols="25" id="buildvars_description"
                      name="buildvars[description]"><?php echo $this->project->description; ?></textarea>
        </div>
        <div class="control-group">

            <label class="creditsLabel">
                <?php echo jgettext('From template'); ?>
            </label>
            <?php echo ($this->project->fromTpl) ? $this->project->fromTpl : '?'; ?>
        </div>
    </fieldset>
</div>
