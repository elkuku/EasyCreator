<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 12-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

ecrStylesheet('config');

?>
<div class="ecr_easy_toolbar" style="float: right;">
    <ul>
        <li>
            <a href="javascript:;" onclick="submitform('save_config');">
                <span class="icon-32-save" title="<?php echo jgettext('Save'); ?>"></span>
                <?php echo jgettext('Save'); ?>
            </a>
        </li>
    </ul>
</div>

<div align="center">
    <h1>
        <span class="img32c icon-32-ecr_config"></span>
        <?php echo sprintf(jgettext('%s Configuration'), 'EasyCreator'); ?>
    </h1>
</div>

<div style="clear: both;"></div>

<?php if(! class_exists('g11n')) : ?>
<div style="background-color: #ffc; border: 1px solid orange; padding: 0.5em;">
    EasyCreator is in "English ONLY" mode ! If you want a localized version, please install the g11n library. -
    <a href="http://joomlacode.org/gf/project/elkuku/frs/?action=FrsReleaseBrowse&frs_package_id=5915">
        Download lib_g11n
    </a>
</div>
<?php endif; ?>

<?php foreach($this->form->getFieldSets() as $fieldSet) : ?>
<?php if('Debug' == $fieldSet->name && ! ECR_DEV_MODE) continue; ?>
<div class="ecr_floatbox">

    <div class="infoHeader imgbarleft icon-24-<?php echo $fieldSet->name; ?>">
        <?php echo jgettext($fieldSet->label); ?>
    </div>

    <fieldset class="adminform">
        <ul class="adminformlist">
            <?php foreach($this->form->getFieldset($fieldSet->name) as $field): ?>
            <li>
                <?php echo $field->label; ?>
                <?php echo $field->input; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </fieldset>
</div>
<?php endforeach;
