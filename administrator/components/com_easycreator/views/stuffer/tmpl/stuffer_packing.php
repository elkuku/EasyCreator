<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views.Stuffer
 * @author     Nikolai Plath
 * @author     Created on 26-May-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$buildOpts = $this->project->buildOpts;
?>

<div class="ecr_floatbox">

<div class="infoHeader img icon-24-package_creation">
    <?php echo jgettext('Package'); ?>
</div>

<br />
<strong class="img icon-16-installfolder"><?php echo jgettext('Build folder'); ?></strong>
<?php if(2 == ECR_HELP) echo JHTML::tooltip(jgettext('Build folder').'::'
    .jgettext('The folder where your final package ends up. The folders extension_name and version will be added automatically.')
    .sprintf(jgettext('<br />If left blank the folder <strong>%s</strong> wil be used'), ECRPATH_BUILDS));
?>
<br />
<input type="text" name="buildvars[zipPath]" size="40"
    value="<?php echo $this->project->zipPath; ?>" />
<?php
if($this->project->zipPath && ! JFolder::exists($this->project->zipPath)) :
    ecrHTML::displayMessage(sprintf(jgettext('The folder %s does not exist'), $this->project->zipPath), 'warning');
endif;
?>

<br />

<strong><?php echo jgettext('Compression'); ?></strong>
<?php ecrHTML::drawPackOpts($buildOpts); ?>

<br /><br />

<strong><?php echo jgettext('Options'); ?></strong>
<br />
<input type="checkbox" name="buildopts[]" id="lbl_create_index_html"
 <?php echo (isset($buildOpts['create_indexhtml'])
 && $buildOpts['create_indexhtml'] == 'ON') ? ' checked="checked"' : ''; ?>
 value="create_indexhtml" />
<label for="lbl_create_index_html"><?php echo jgettext('Create index.html files'); ?></label>
<br />

<input type="checkbox" name="buildopts[]" id="lbl_create_md5"
 <?php echo (isset($buildOpts['create_md5'])
 && $buildOpts['create_md5'] == 'ON') ? ' checked="checked"' : ''; ?>
 value="create_md5" />
<label for="lbl_create_md5"><?php echo jgettext('Create MD5 checksum file'); ?></label>
<br />
&nbsp;&nbsp;&nbsp;|__<input type="checkbox" name="buildopts[]" id="lbl_create_md5_compressed"
 <?php echo (isset($buildOpts['create_md5_compressed'])
 && $buildOpts['create_md5_compressed'] == 'ON') ? ' checked="checked"' : ''; ?>
 value="create_md5_compressed" />
<label for="lbl_create_md5_compressed"><?php echo jgettext('Compress checksum file'); ?></label>
<?php if(2 == ECR_HELP) echo JHTML::tooltip(jgettext('Compress checksum file').'::'
    .jgettext('This will do a small compression on your checksum file')); ?>

<br />
<br />

<strong class="img icon-16-easycreator"><?php echo jgettext('EasyCreator Options'); ?></strong>
<br />

<input type="checkbox" name="buildopts[]" id="lbl_include_ecr_projectfile"
 <?php echo (isset($buildOpts['include_ecr_projectfile'])
 && $buildOpts['include_ecr_projectfile'] == 'ON')
 ? ' checked="checked"'
 : ''; ?>
 value="include_ecr_projectfile" />
<label for="lbl_include_ecr_projectfile"><?php echo jgettext('Include EasyCreator Project file'); ?></label>
<br />

<input type="checkbox" name="buildopts[]" id="lbl_remove_autocode"
 <?php echo (isset($buildOpts['remove_autocode'])
 && $buildOpts['remove_autocode'] == 'ON') ? ' checked="checked"' : ''; ?>
 value="remove_autocode" />
<label for="lbl_remove_autocode"><?php echo jgettext('Remove EasyCreator AutoCode'); ?></label>

</div>
