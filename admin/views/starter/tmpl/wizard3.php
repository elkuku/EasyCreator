<?php defined('_JEXEC') || die('=;)');
/**
 * @package     EasyCreator
 * @subpackage  Views
 * @author      Nikolai Plath (elkuku)
 * @author      Created on 09-Mar-2008
 * @license     GNU/GPL, see JROOT/LICENSE.php
 */

$requireds = $this->builder->customOptions('requireds');
?>

<script type="text/javascript">
    function submitbutton(command) {
        valid = true;

        if(command != 'starterstart') {
            $('wizard-loader-back').removeClass('icon32-wizard');
            $('wizard-loader-back').addClass('ajax-loading-32');
            submitform(command);
            return;
        }

    <?php
    foreach($requireds as $required)
    {
        $js = "if( $('".$required."').value == '' ) {\n";
        $js .= "$('".$required."').focus();\n";
        $js .= "$('".$required."').setStyle('background-color', 'red');\n";
        $js .= "valid = false;\n";
        $js .= "}\n";

        echo $js;
    }//foreach

    ?>
        if(!valid) {
            return false;
        }

        if(document.id('db_table_fields') != null) {
            if(!checkTableEditForm(document.adminForm)) {
                return false;
            }
        }

        $('wizard-loader').removeClass('icon32-wizard');
        $('wizard-loader').addClass('ajax-loading-32');
        submitform(command);
    }//function
</script>

<div class="ecr_floatbox left" style="width: 55%;">
    <div class="buttonBox">
        <a class="btn" onclick="submitbutton('wizard2');"
           title="<?php echo jgettext('Back'); ?>">
            <?php if(ECR_TBAR_ICONS) : ?>
                <i class="img icon16-2leftarrow"></i>
            <?php endif; ?>
            <?php echo jgettext('Back'); ?>
        </a>
    </div>

    <div class="wizard-header">
        <span id="wizard-loader-back" class="img32 icon32-wizard"></span>
        <span class="wiz_step">3 / 3</span><?php echo jgettext('Build options') ?>
    </div>

    <div class="ecr_custom_options">
        <?php $this->builder->customOptions('display', $this->project); ?>
    </div>

    <div class="ecr_wiz_desc">
        <p style="font-weight: bold;"><?php echo jgettext('Youre done') ?></p>
        <?php echo jgettext('Just click on create it below to finish your component') ?>
    </div>

    <div class="ecr_table">
        <div class="ecr_table-row">
            <div class="ecr_table-cell">
                <input type="checkbox" name="create_changelog" id="create_changelog" checked="checked"/>
                <label class="inline" for="create_changelog"><?php echo jgettext('Create CHANGELOG.php'); ?></label>

                <h3><?php echo jgettext('File header template') ?></h3>
                <?php echo EcrHtmlOptions::header() ?>
            </div>
            <div class="ecr_table-cell">
                <?php echo EcrHtmlOptions::logging() ?>
            </div>
        </div>
    </div>

    <?php if(ECR_DEV_MODE) : ?>
        <input type="checkbox" name="ecr_test_mode" id="ecr_test_mode" value="test"/>
        <label class="inline" for="ecr_test_mode">TEST only</label>
    <?php endif; ?>

    <div class="btn" style="clear: both; display: block; margin-top: 1em; text-align: center;"
         onclick="submitbutton('starterstart');">
        <p style="padding-bottom: 1em;">
            <span id="wizard-loader" class="img32 icon32-wizard"></span>
        </p>

        <h1>
            <?php echo jgettext('Create it'); ?>
        </h1>
    </div>
</div>
<div class="ecr_floatbox right" style="width: 40%;">
    <?php EcrHtmlWizard::displayResult($this->project); ?>
</div>

<div style="clear: both; height: 1em;"></div>
