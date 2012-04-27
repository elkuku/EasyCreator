<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

?>
<div class="infoHeader img icon-24-github">
    <?php echo jgettext('GitHub') ?>
</div>

<?php echo $this->loadTemplate('githubcredentials'); ?>

<div id="githubDeployMessage"></div>
<div id="githubDeployDebug"></div>

<div id="githubDeployDisplay"></div>

<div class="buttons">
    <a href="javascript:;" class="ecr_button img icon-16-export" onclick="EcrDeploy.deployPackage('github');">
        <?php echo jgettext('Deploy'); ?>
    </a>
</div>

<div class="infoHeader img icon-16-installfolder"><?php echo jgettext('Manage') ?></div>

<div id="ajaxgithubMessage"></div>
<div id="ajaxgithubDebug"></div>

<div id="githubDisplay"></div>

<div class="buttons">
    <a href="javascript:;" class="ecr_button img icon-16-import" onclick="EcrDeploy.getPackageList('github');">
        <?php echo jgettext('Get List'); ?>
    </a>
</div>
