<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 06-Apr-.2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$checked =($this->project->method == 'upgrade') ? ' checked="checked"' : '';
try
{

    $updater = new dbUpdater($this->project);
}
catch (Exception $e)
{
    ecrHTML::displayMessage($e->getMessage(), 'error');

    return;
}

$versions = $updater->versions;
?>

<div class="ecr_floatbox">
    <div class="infoHeader img icon-24-update"><?php echo jgettext('Update') ?></div>

    <strong>
        <?php echo jgettext('Method'); ?>
    </strong>

    <input type="checkbox" <?php echo $checked; ?> name="buildvars[method]"
    id="buildvars_method" value="upgrade" />

    <label for="buildvars_method" class="hasEasyTip"
    title="method=upgrade::<?php echo jgettext('This will perform an upgrade on installing your extension'); ?>">
        <?php echo jgettext('Upgrade'); ?>
    </label>
<?php //var_dump($versions); ?>
    <?php if('1.5' != $this->project->JCompat) : ?>
        <h4><?php echo jgettext('Versions'); ?></h4>
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

         <?php $path = JPATH_ADMINISTRATOR.'/components/'.$this->project->comName.'/install/sql/updates/mysql';
         $files = array();
         if(JFolder::exists($path))
         {
         $files = JFolder::files($path);
         }

         if($files) : ?>
             <?php echo jgettext('Found sql update files'); ?>
             <br >/
         	<?php echo $path; ?>
             <ul>
             <?php foreach ($files as $file) { ?>
                 <li><?php echo $file; ?></li>
             <?php }?>
             </ul>
         <?php else : ?>
             <div class="ecr_button img icon-16-add" onclick="createFile('sql', 'update');">
                 <?php echo jgettext('Init database updates'); ?>
             </div>
         <?php endif; ?>

         <?php if($versions) :?>
             <?php if(count($versions) > 1
            || $versions[0] != $this->project->version) : ?>
             <div class="ecr_button img icon-16-add" onclick="createFile('sql', 'update');">
                 <?php echo jgettext('Create database updates'); ?>
             </div>
             <?php endif; ?>
         <?php endif; ?>
     <?php endif; ?>
</div>
