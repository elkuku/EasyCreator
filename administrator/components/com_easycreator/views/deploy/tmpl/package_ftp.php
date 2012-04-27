<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
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
