<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elkuku
 * Date: 19.04.12
 * Time: 02:13
 * To change this template use File | Settings | File Templates.
 */
?>

<div class="infoHeader img icon-24-ftp">
    <?php echo jgettext('FTP') ?>
</div>

<?php echo $this->loadTemplate('ftpcredentials'); ?>

<div id="ftpDeployMessage"></div>
<div id="ftpDeployDebug"></div>

<div id="ftpDeployDisplay"></div>

<div class="buttons">
    <a href="javascript:;" class="ecr_button img icon-16-export" onclick="EcrDeploy.deployPackage('ftp');">
        <?php echo jgettext('Deploy'); ?>
    </a>
</div>

<div class="infoHeader img icon-16-installfolder"><?php echo jgettext('Manage') ?></div>

<div id="ajaxftpMessage"></div>
<div id="ajaxftpDebug"></div>

<div id="ftpDisplay"></div>

<div class="buttons">
    <a href="javascript:;" class="ecr_button img icon-16-import" onclick="EcrDeploy.getPackageList('ftp');">
        <?php echo jgettext('Get List'); ?>
    </a>
</div>
