<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elkuku
 * Date: 20.04.12
 * Time: 14:59
 * To change this template use File | Settings | File Templates.
 */
?>

<div class="ecr_floatbox">

    <div class="infoHeader img icon-24-ecr_deploy">
        <?php echo jgettext('Deploy') ?>
    </div>

    <fieldset class="adminform">

        <legend><?php echo jgettext('Ftp'); ?></legend>

        <label for="ftpHost"><?php echo jgettext('Server'); ?></label>
        <input type="text" name="ftpHost" id="ftpHost"
               value="<?php echo $this->project->deployOptions->get('ftp.host'); ?>" />

        <label for="ftpPort"><?php echo jgettext('Port'); ?></label>
        <input type="text" name="ftpPort" id="ftpPort" value="<?php echo $this->project->deployOptions->get('ftp.port', '21'); ?>" size="5"/>

        <label for="ftpBasedir"><?php echo jgettext('Base Directory'); ?></label>
        <input type="text" name="ftpBasedir" id="ftpBasedir" value="<?php echo $this->project->deployOptions->get('ftp.basedir'); ?>" />

        <label for="ftpDownloads"><?php echo jgettext('Downloads directory'); ?></label>
        <input type="text" name="ftpDownloads" id="ftpDownloads" value="<?php echo $this->project->deployOptions->get('ftp.downloads'); ?>" />

        <label for="ftpUser"><?php echo jgettext('User'); ?></label>
        <input type="text" name="ftpUser" id="ftpUser" value="<?php echo $this->project->deployOptions->get('ftp.user'); ?>" />

        <label for="ftpPass"><?php echo jgettext('Password'); ?></label>
        <input type="password" name="ftpPass" id="ftpPass" value="<?php echo $this->project->deployOptions->get('ftp.pass'); ?>" />

    </fieldset>

    <fieldset class="adminform">

        <legend><?php echo jgettext('GitHub'); ?></legend>

        <label for="githubRepoOwner"><?php echo jgettext('Repository Owner'); ?></label>
        <input type="text" name="githubRepoOwner" id="githubRepoOwner" value="<?php echo $this->project->deployOptions->get('github.repoowner'); ?>">

        <label for="githubRepoName"><?php echo jgettext('Repository'); ?></label>
        <input type="text" name="githubRepoName" id="githubRepoName" value="<?php echo $this->project->deployOptions->get('github.reponame'); ?>">

        <label for="githubUser"><?php echo jgettext('User'); ?></label>
        <input type="text" name="githubUser" id="githubUser" value="<?php echo $this->project->deployOptions->get('github.user'); ?>">

        <label for="githubPass"><?php echo jgettext('Password'); ?></label>
        <input type="password" name="githubPass" id="githubPass" value="<?php echo $this->project->deployOptions->get('github.pass'); ?>">

    </fieldset>

</div>
