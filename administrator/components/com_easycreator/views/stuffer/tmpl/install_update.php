<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 06-Apr-.2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

try
{
    $updater = new EcrDbUpdater($this->project);
}
catch(Exception $e)
{
    EcrHtml::message($e);

    return;
}//try

$versions = $updater->versions;

$path = JPATH_ADMINISTRATOR.'/components/'.$this->project->comName.'/install/sql/updates/mysql';
$files = array();

if(JFolder::exists($path))
{
$files = JFolder::files($path);
}

?>
<div class="ecr_floatbox">
    <div class="infoHeader img icon24-update"><?php echo jgettext('Update SQL') ?></div>

    <strong><?php echo jgettext('Versions'); ?></strong>
    <br />
    <?php echo jgettext('Package path'); ?>
    <?php echo JHtml::tooltip($this->project->getZipPath(), jgettext('Path')); ?>
    <?php if($versions) : ?>
        <ul>
        <?php foreach($versions as $version) : ?>
            <li><?php echo $version; ?></li>
        <?php endforeach; ?>

        <?php if( ! in_array($this->project->version, $versions)) : ?>
            <li>
                <?php echo sprintf(jgettext('Current version: %s'), $this->project->version); ?>
            </li>
        <?php endif; ?>
         </ul>

     <?php else : ?>
         <p><?php echo jgettext('No versions found'); ?></p>
     <?php endif; ?>

     <?php if($files) : ?>
         <?php echo jgettext('Found sql update files'); ?>
         <?php echo JHtml::tooltip($path, jgettext('Path')); ?>
         <ul>
         <?php foreach ($files as $file) : ?>
             <li><?php echo $file; ?></li>
         <?php endforeach; ?>
         </ul>
     <?php endif; ?>

     <?php if( ! $files) : ?>
         <div class="btn block" onclick="createFile('sql', 'update');">
             <i class="img icon16-add"></i>
             <?php echo jgettext('Init database updates'); ?>
         </div>
     <?php endif; ?>

     <?php if($versions) :?>
         <?php if(count($versions) > 1
        || $versions[0] != $this->project->version) : ?>
         <div class="btn block" onclick="createFile('sql', 'update');">
             <i class="img icon16-update"></i>
             <?php echo jgettext('Update database update files'); ?>
         </div>
         <?php endif; ?>
     <?php endif; ?>

</div>
