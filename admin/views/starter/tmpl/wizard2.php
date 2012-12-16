<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 09-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$formFieldNames = array();
$img_base = JURI::root().'media/com_easycreator/admin/images';

ecrScript('wizard2');
?>

<div class="ecr_floatbox left" style="width: 55%;">
    <div class="buttonBox">
        <a class="btn" onclick="submitbutton('wizard');"
           title="<?php echo jgettext('Back'); ?>">
            <i class="img icon16-2leftarrow"></i>
            <?php echo jgettext('Back'); ?>
        </a>
        <a class="btn" onclick="submitbutton('wizard3');"
           title="<?php echo jgettext('Next'); ?>">
            <i class="imgR icon16-2rightarrow"></i>
            <?php echo jgettext('Next'); ?>
        </a>
    </div>
    <div class="wizard-header">
        <span id="wizard-loader" class="img32 icon32-wizard"></span>
        <span class="wiz_step">2 / 3</span><?php echo jgettext('Name for your baby'); ?>
    </div>

    <div class="ecr_wiz_desc">
        <?php echo jgettext('Please choose a name following the standard naming conventions. Use UPPER and lower case'); ?>
    </div>

    <div class="ecr_table-row">
        <div class="ecr_table-cell">
            <label for="com_name"><?php echo jgettext('Name'); ?></label>
            <input type="text" name="com_name" id="com_name" style="font-size: 1.4em;"
                   value="<?php echo $this->project->name; ?>"/>
            <br/>
            <?php $formFieldNames[] = 'com_name' ?>

            <span style="color: orange;"><?php echo jgettext('Required'); ?></span>
            <span id="req_name" style="display: none;"></span>
        </div>
        <div class="ecr_table-cell">
            <label for="version"><?php echo jgettext('Version'); ?></label>
            <input type="text" class="span9" size="5" name="version" id="version"
                   value="<?php echo $this->project->version; ?>"/>
            <br/>
            <?php $formFieldNames[] = 'version' ?>
            <span style="color: orange;"><?php echo jgettext('Required'); ?></span>
            <span id="req_version" style="display: none;"></span>
        </div>
    </div>

    <strong><?php echo jgettext('Description'); ?></strong>
    <textarea name="description" rows="5" style="width: 100%;"><?php echo $this->project->description; ?></textarea>
    <br/>
    <?php $formFieldNames[] = 'description' ?>

    <?php if($this->project->type == 'component') : ?>
    <p>
        <strong class="img icon16-easycreator">AutoCode</strong>
        <br/>
        <?php echo jgettext('List postfix'); ?>
        <input type="text" name="list_postfix" id="list_postfix" size="5"
               value="<?php echo $this->project->listPostfix; ?>"/>
        <span style="color: orange;"><?php echo jgettext('Required'); ?></span>
        <span id="req_list_postfix" style="display: none;"></span>
    </p>
    <?php $formFieldNames[] = 'list_postfix' ?>
    <?php endif; ?>

    <h2><?php echo jgettext('Your Credits'); ?></h2>

    <table>
        <tr>
            <th align="left"><?php echo jgettext('Author name'); ?></th>
            <td>
                <input type="text" name="author" size="30" maxlength="30"
                       value="<?php echo $this->project->author; ?>"/>
                <?php $formFieldNames[] = 'author'; ?>
            </td>
        </tr>
        <tr>
            <th align="left"><?php echo jgettext('Author e-mail'); ?></th>
            <td>
                <input type="text" name="authorEmail" value="<?php echo $this->project->authorEmail; ?>"/>
                <?php $formFieldNames[] = 'authorEmail'; ?>
            </td>
        </tr>
        <tr>
            <th align="left"><?php echo jgettext('Author url'); ?></th>
            <td>
                <input type="text" name="authorUrl" value="<?php echo $this->project->authorUrl; ?>"/>
                <?php $formFieldNames[] = 'authorUrl'; ?>
            </td>
        </tr>
        <tr>
            <th align="left"><?php echo jgettext('License'); ?></th>
            <td>
                <input type="text" name="license" size="60" maxlength="150"
                       value="<?php echo $this->project->license; ?>"/>
                <?php $formFieldNames[] = 'license'; ?>
            </td>
        </tr>
        <tr>
            <th align="left"><?php echo jgettext('Copyright'); ?> &copy;</th>
            <td>
                <input type="text" name="copyright" size="60" maxlength="150"
                       value="<?php echo $this->project->copyright; ?>"/>
                <?php $formFieldNames[] = 'copyright'; ?>
            </td>
        </tr>
    </table>

</div>

<script type="text/javascript">
    document.id('com_name').focus();
</script>

<div class="ecr_floatbox right" style="width: 40%;">
    <?php EcrHtmlWizard::displayResult($this->project, $formFieldNames); ?>
</div>

<div style="clear: both; height: 1em;"></div>
