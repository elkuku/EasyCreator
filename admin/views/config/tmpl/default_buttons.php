<?php
/**
 * User: elkuku
 * Date: 10.06.12
 * Time: 11:02
 */

?>
<div class="btn-group">

    <a class="btn<?php echo ECR_TBAR_SIZE; ?> btn-success" href="javascript:;"
       onclick="EcrConfig.submitForm('save_config', this);">
        <?php if(ECR_TBAR_ICONS) : ?>
        <i class="img icon16-ecr_save"></i>
        <?php endif; ?>
        <?php echo jgettext('Save'); ?>
    </a>
    <a class="btn<?php echo ECR_TBAR_SIZE; ?> btn-inverse" href="javascript:;"
       onclick="EcrConfig.maintain('cleanJoomlaTemp', this);">
        <?php if(ECR_TBAR_ICONS) : ?>
        <i class="img icon16-trash"></i>
        <?php endif; ?>
        Clean Joomla! Temp
    </a>
    <a class="btn<?php echo ECR_TBAR_SIZE; ?> btn-inverse" href="javascript:;"
       onclick="EcrConfig.maintain('cleanJoomlaCache', this);">
        <?php if(ECR_TBAR_ICONS) : ?>
        <i class="img icon16-trash"></i>
        <?php endif; ?>
        Clean Joomla! Cache
    </a>
    <a class="btn<?php echo ECR_TBAR_SIZE; ?> btn-warning" href="javascript:;"
       onclick="EcrConfig.maintain('cleanEcrLogs', this);">
        <?php if(ECR_TBAR_ICONS) : ?>
        <i class="img icon16-trash"></i>
        <?php endif; ?>
        Clean EasyCreator log files
    </a>

</div>
<div id="maintainResponse"></div>

