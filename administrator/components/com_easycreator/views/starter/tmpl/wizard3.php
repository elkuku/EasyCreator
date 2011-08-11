<?php
/**
 * @version $Id$
 * @package     EasyCreator
 * @subpackage  Views
 * @author      Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author      Created on 09-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$requireds = $this->EasyBuilder->customOptions('requireds');
?>
<script type="text/javascript">
function submitbutton(command)
{
    valid = true;

    if(command != 'starterstart')
    {
        $('wizard-loader-back').removeClass('icon-32-wizard');
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
    if( ! valid)
    {
        return false;
    }

    if($('db_table_fields') != null)
    {
        if( ! checkTableEditForm(document.adminForm))
        {
            return false;
        }
    }

    $('wizard-loader').removeClass('icon-32-wizard');
    $('wizard-loader').addClass('ajax-loading-32');
    submitform(command);
}//function
</script>

<div class="ecr_floatbox" style="width: 75%;">
    <div class="buttonBox">
        <a class="ecr_button img icon-16-2leftarrow"
        onclick="submitbutton('wizard2');"
        title="<?php echo jgettext('Back'); ?>">
		    <?php echo jgettext('Back'); ?>
		</a>
    </div>

	<div class="wizard-header">
    	<span id="wizard-loader-back" class="img32 icon-32-wizard"></span>
        <span class="wiz_step">3 / 3</span><?php echo jgettext('Build options'); ?>
	</div>

    <div class="ecr_custom_options">
        <?php $this->EasyBuilder->customOptions('display', $this->project); ?>
    </div>

    <div class="ecr_wiz_desc">
        <p style="font-weight: bold;"><?php echo jgettext('Youre done'); ?></p>
        <?php echo jgettext('Just click on create it below to finish your component'); ?>
    </div>

    <div class="ecr_table">
        <div class="ecr_table-row">
            <div class="ecr_table-cell">
                <input type="checkbox" name="create_changelog" id="create_changelog" checked="checked" />
                <label for="create_changelog"><?php echo jgettext('Create CHANGELOG.php'); ?></label>
            </div>
            <div class="ecr_table-cell">
                    <?php ecrHTML::drawLoggingOptions(); ?>
            </div>
        </div>
    </div>

    <?php if(ECR_DEV_MODE) : ?>
        <input type="checkbox" name="ecr_test_mode" id="ecr_test_mode" value="test" />
        <label for="ecr_test_mode">TEST only</label>
    <?php endif; ?>

    <div class="ecr_button" style="margin-top: 1em; text-align: center;" onclick="submitbutton('starterstart');">
        <p style="padding-bottom: 1em;">
        <span id="wizard-loader" class="img32 icon-32-wizard"></span>
        </p>

        <h1>
            <?php echo jgettext('Create it'); ?>
        </h1>
    </div>
</div>
<div class="ecr_floatbox" style="width: 20%;">
    <?php ecrHTML::displayResult($this->project); ?>
</div>

<div style="clear: both; height: 1em;"></div>
