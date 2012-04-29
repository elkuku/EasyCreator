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
    <div class="infoHeader imgbarleft icon-24-info">
        <?php echo jgettext('Info') ?>
    </div>

    <div>

        <strong class="img icon-16-joomla">
            <?php echo jgettext('Joomla! version'); ?>
        </strong>

        <input type="radio" id="jversion15" name="jcompat" value="1.5"
            <?php echo ($this->project->JCompat == '1.5') ? ' checked="checked"' : ''; ?>
            />
        <label for="jversion15" class="img32b icon-joomla-compat-15"></label>

        <input type="radio" id="jversion16" name="jcompat" value="1.6"
            <?php echo (in_array($this->project->JCompat, array('1.6', '1.7', '2.5'))) ? ' checked="checked"' : ''; ?>
            />
        <label for="jversion16" class="img32b icon-joomla-compat-25"></label>
    </div>

    <div>
        <ul>
            <li>
                <label class="creditsLabel">
                    <?php echo jgettext('Extension type'); ?>
                </label>
            <span class="img icon-12-<?php echo $this->project->type ?>">
                <?php echo $this->project->type ?>
            </span>
            </li>
            <li>
                <label class="creditsLabel">
                    <?php echo jgettext('Name'); ?>
                </label>
                <?php echo $this->project->name; ?>
            </li>
            <li>
                <label class="creditsLabel">
                    <?php echo jgettext('Extension name'); ?>
                </label>
                <?php echo $this->project->comName; ?>
            </li>
            <?php if($this->project->scope): ?>

            <li>
                <label class="creditsLabel">
                    <?php echo jgettext('Component scope'); ?>
                </label>
                <?php echo $this->project->scope ?>
            </li>
            <?php endif; ?>

            <li>
                <label class="creditsLabel" for="buildvars_version">
                    <?php echo jgettext('Version'); ?>
                </label>
                <input type="text" id="buildvars_version" name="buildvars[version]"
                       size="10" value="<?php echo $this->project->version; ?>"/>
            </li>
            <li>
                <label class="creditsLabel" for="buildvars_description">
                    <?php echo jgettext('Description'); ?>
                </label>
                <textarea rows="2" cols="25" id="buildvars_description"
                          name="buildvars[description]"><?php echo $this->project->description; ?></textarea>
            </li>
            <li>
                <label class="creditsLabel">
                    <?php echo jgettext('From template'); ?>
                </label>
                <?php echo ($this->project->fromTpl) ? $this->project->fromTpl : '?'; ?>
            </li>
        </ul>
    </div>
</div>
