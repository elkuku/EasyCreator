<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elkuku
 * Date: 20.04.12
 * Time: 18:30
 * To change this template use File | Settings | File Templates.
 */

/* @var JRegistry $options */
$options = $this->project->deployOptions;
?>

<fieldset class="adminform">

    <legend><?php echo jgettext('Credentials'); ?></legend>

    <label for="githubRepoOwner"><?php echo jgettext('Repository Owner'); ?></label>
    <input type="text" name="githubRepoOwner" id="githubRepoOwner"
           value="<?php echo $options->get('github.repoowner'); ?>">

    <label for="githubRepoName"><?php echo jgettext('Repository'); ?></label>
    <input type="text" name="githubRepoName" id="githubRepoName"
           value="<?php echo $options->get('github.reponame'); ?>">

    <label for="githubUser"><?php echo jgettext('User'); ?></label>
    <input type="text" name="githubUser" id="githubUser" value="<?php echo $options->get('github.user'); ?>">

    <label for="githubPass"><?php echo jgettext('Password'); ?></label>
    <input type="password" name="githubPass" id="githubPass" value="<?php echo $options->get('github.pass'); ?>">

</fieldset>

