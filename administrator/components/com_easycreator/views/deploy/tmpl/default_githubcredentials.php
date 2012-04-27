<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
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

