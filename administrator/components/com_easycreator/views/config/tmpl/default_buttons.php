<?php
/**
 * User: elkuku
 * Date: 10.06.12
 * Time: 11:02
 */

?>
<div class="btn-group">
    <a class="btn" href="javascript:;" onclick="EcrConfig.maintain('cleanJoomlaTemp', this);">
        Clean Joomla! Temp
    </a>
    <a class="btn" href="javascript:;" onclick="EcrConfig.maintain('cleanJoomlaCache', this);">
        Clean Joomla! Cache
    </a>
    <a class="btn" href="javascript:;" onclick="EcrConfig.maintain('cleanEcrLogs', this);">
        Clean EasyCreator log files
    </a>

</div>
<div id="maintainResponse"></div>

<div class="btn-group" style="float: right; margin-left: 50px;">
    <a class="btn<?php echo ECR_TBAR_SIZE; ?>" href="javascript:;" onclick="EcrConfig.submitForm('save_config', this);">
        <?php if(ECR_TBAR_ICONS) : ?>
        <i class="img icon16-ecr_save"></i>
        <br/>
        <?php endif; ?>
        <?php echo jgettext('Save'); ?>
    </a>

</div>


