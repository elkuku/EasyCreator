<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views.Stuffer
 * @author     Nikolai Plath
 * @author     Created on 26-May-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$buildOpts = $this->project->buildOpts;
$options = new JObject($this->project->buildOpts);
?>

<div class="ecr_floatbox">

    <div class="infoHeader img icon24-package_creation">
        <?php echo jgettext('Package'); ?>
    </div>

    <br/>
    <strong class="img icon16-installfolder"><?php echo jgettext('Build folder'); ?></strong>
    <?php if(2 == ECR_HELP) echo JHTML::tooltip(jgettext('Build folder').'::'
    .jgettext('The folder where your final package ends up. The folders extension_name and version will be added automatically.')
    .sprintf(jgettext('<br />If left blank the folder <strong>%s</strong> wil be used'), ECRPATH_BUILDS));
    ?>
    <br/>
    <input type="text" name="buildvars[zipPath]" size="40"
           value="<?php echo $this->project->zipPath; ?>"/>
    <?php
    if($this->project->zipPath && ! JFolder::exists($this->project->zipPath)) :
        EcrHtml::displayMessage(sprintf(jgettext('The folder %s does not exist'), $this->project->zipPath), 'warning');
    endif;
    ?>

    <br/>

    <strong><?php echo jgettext('Compression'); ?></strong>
    <?php EcrHtml::drawPackOpts($buildOpts); ?>

    <br/><br/>

    <strong><?php echo jgettext('Options'); ?></strong>
    <br/>
    <input type="checkbox" name="buildopts[]" id="lbl_create_index_html"
        <?php echo (isset($buildOpts['create_indexhtml'])
        && $buildOpts['create_indexhtml'] == 'ON') ? ' checked="checked"' : ''; ?>
           value="create_indexhtml"/>
    <label class="inline" for="lbl_create_index_html"><?php echo jgettext('Create index.html files'); ?></label>
    <br/>

    <input type="checkbox" name="buildopts[]" id="lbl_create_md5"
        <?php echo (isset($buildOpts['create_md5'])
        && $buildOpts['create_md5'] == 'ON') ? ' checked="checked"' : ''; ?>
           value="create_md5"/>
    <label class="inline" for="lbl_create_md5"><?php echo jgettext('Create MD5 checksum file'); ?></label>
    <br/>
    &nbsp;&nbsp;&nbsp;|__<input type="checkbox" name="buildopts[]" id="lbl_create_md5_compressed"
    <?php echo (isset($buildOpts['create_md5_compressed'])
    && $buildOpts['create_md5_compressed'] == 'ON') ? ' checked="checked"' : ''; ?>
                                value="create_md5_compressed"/>
    <label class="inline" for="lbl_create_md5_compressed"><?php echo jgettext('Compress checksum file'); ?></label>
    <?php if(2 == ECR_HELP) echo JHTML::tooltip(jgettext('Compress checksum file').'::'
    .jgettext('This will do a small compression on your checksum file')); ?>

    <br/>
    <br/>

    <strong class="img icon16-easycreator"><?php echo jgettext('EasyCreator Options'); ?></strong>
    <br/>

    <input type="checkbox" name="buildopts[]" id="lbl_include_ecr_projectfile"
        <?php echo (isset($buildOpts['include_ecr_projectfile'])
        && $buildOpts['include_ecr_projectfile'] == 'ON')
        ? ' checked="checked"'
        : ''; ?>
           value="include_ecr_projectfile"/>
    <label class="inline" for="lbl_include_ecr_projectfile"><?php echo jgettext('Include EasyCreator Project file'); ?></label>
    <br/>

    <input type="checkbox" name="buildopts[]" id="lbl_remove_autocode"
        <?php echo (isset($buildOpts['remove_autocode'])
        && $buildOpts['remove_autocode'] == 'ON') ? ' checked="checked"' : ''; ?>
           value="remove_autocode"/>
    <label class="inline" for="lbl_remove_autocode"><?php echo jgettext('Remove EasyCreator AutoCode'); ?></label>

    <br/>
    <br/>
    <strong><?php echo jgettext('File name'); ?></strong>

    <div class="control-group">
        <label class="control-label" for="custom_name_1"><?php echo jgettext('Default name'); ?></label>

        <div class="controls">
            <input type="text" size="30" name="buildopts[custom_name_1]" id="custom_name_1"
                   value="<?php echo $options->get('custom_name_1'); ?>"/>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="custom_name_2"><?php echo jgettext('Custom name'); ?></label>

        <div class="controls">
            <input type="text" size="30" name="buildopts[custom_name_2]" id="custom_name_2"
                   value="<?php echo $options->get('custom_name_2'); ?>"/>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="custom_name_3"><?php echo jgettext('Custom name'); ?></label>

        <div class="controls">
            <input type="text" size="30" name="buildopts[custom_name_3]" id="custom_name_3"
                   value="<?php echo $options->get('custom_name_3'); ?>"/>
        </div>
    </div>
    <div class="control-group">

        <label class="control-label" for="custom_name_4"><?php echo jgettext('Custom name'); ?></label>

        <div class="controls">
            <input type="text" size="30" name="buildopts[custom_name_4]" id="custom_name_4"
                   value="<?php echo $options->get('custom_name_4'); ?>"/>
        </div>
    </div>
    <span class="btn" onclick="Stuffer.loadFilenameDefaults();"><?php echo jgettext('Reset to default'); ?></span>

</div>
