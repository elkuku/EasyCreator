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

    <div class="infoHeader img icon24-ftp">
        <?php echo jgettext('Status') ?>
    </div>

    <div id="syncList"></div>

    <div class="btn-group">
        <a title="<?php echo jgettext('Check all'); ?>" href="javascript:;"
           class="btn" onclick="EcrDeploy.checkAll();">
            <i class="img icon16-ok"></i>
        </a>

        <a title="<?php echo jgettext('Check all new'); ?>" href="javascript:;"
           class="btn" onclick="EcrDeploy.checkAll('new');">
            <i class="img icon16-greenled"></i>
        </a>

        <a title="<?php echo jgettext('Check all modified'); ?>" href="javascript:;"
           class="btn" onclick="EcrDeploy.checkAll('changed');">
            <i class="img icon16-yellowled"></i>
        </a>

        <a title="<?php echo jgettext('Check all deleted'); ?>" href="javascript:;"
           class="btn" onclick="EcrDeploy.checkAll('deleted');">
            <i class="img icon16-redled"></i>
        </a>

        <a title="<?php echo jgettext('Uncheck all'); ?>" href="javascript:;"
           class="btn" onclick="EcrDeploy.uncheckAll();">
            <i class="img icon16-notok"></i>
        </a>
    </div>

    <hr/>

    <div id="ftpMessage"></div>
    <div id="ftpDebug"></div>

    <div id="ftpDisplay"></div>

    <div class="btn-group">
        <a href="javascript:;" class="btn" onclick="EcrDeploy.syncFiles('ftp');">
            <i class="img icon16-update"></i>
            <?php echo jgettext('Synchronize remote'); ?>
        </a>
        <a href="javascript:;" class="btn" onclick="EcrDeploy.getSyncList('ftp');">
            <i class="img icon16-update"></i>
            <?php echo jgettext('Reload local'); ?>
        </a>
        <a href="javascript:;" class="btn" onclick="EcrDeploy.deployFiles('ftp');">
            <i class="img icon16-export"></i>
            <?php echo jgettext('Deploy'); ?>
        </a>
    </div>
</div>

<div class="clr"></div>

<?php echo EcrHtmlDebug::logConsole(); ?>
<div style="height: 75px;"></div>

<script type="text/javascript">EcrDeploy.getSyncList('ftp');</script>
