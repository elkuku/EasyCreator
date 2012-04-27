<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */
?>
<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('ftp'); ?>

    <div class="infoHeader img icon-24-ftp">
        <?php echo jgettext('Status') ?>
    </div>

    <div id="syncList"></div>

    <div class="buttons">
        <a title="<?php echo jgettext('Check all'); ?>" href="javascript:;"
           class="ecr_button img icon-16-ok" onclick="EcrDeploy.checkAll();"></a>

        <a title="<?php echo jgettext('Check all new'); ?>" href="javascript:;"
           class="ecr_button img icon-16-greenled" onclick="EcrDeploy.checkAll('new');"></a>

        <a title="<?php echo jgettext('Check all modified'); ?>" href="javascript:;"
           class="ecr_button img icon-16-yellowled" onclick="EcrDeploy.checkAll('changed');"></a>

        <a title="<?php echo jgettext('Check all deleted'); ?>" href="javascript:;"
           class="ecr_button img icon-16-redled" onclick="EcrDeploy.checkAll('deleted');"></a>

        <a title="<?php echo jgettext('Uncheck all'); ?>" href="javascript:;"
           class="ecr_button img icon-16-notok" onclick="EcrDeploy.uncheckAll();"></a>
    </div>

    <hr/>

    <div id="ftpMessage"></div>
    <div id="ftpDebug"></div>

    <div id="ftpDisplay"></div>

    <div class="buttons">
        <a href="javascript:;" class="ecr_button img icon-16-update" onclick="EcrDeploy.syncFiles('ftp');">
            <?php echo jgettext('Synchronize remote'); ?>
        </a>
        <a href="javascript:;" class="ecr_button img icon-16-update" onclick="EcrDeploy.getSyncList('ftp');">
            <?php echo jgettext('Reload local'); ?>
        </a>
        <a href="javascript:;" class="ecr_button img icon-16-export" onclick="EcrDeploy.deployFiles('ftp');">
            <?php echo jgettext('Deploy'); ?>
        </a>
    </div>
</div>

<div class="clr"></div>

<?php echo EcrHtml::drawDebugConsole(); ?>
<div style="height: 75px;"></div>

<script type="text/javascript">EcrDeploy.getSyncList('ftp');</script>
