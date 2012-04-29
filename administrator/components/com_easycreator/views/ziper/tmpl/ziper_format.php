<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 25-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$projectOptions = $this->project->buildOpts;
$chk_upgrade = ($this->project->method == 'upgrade') ? ' checked="checked"' : '';
?>
<div class="infoHeader imgbarleft icon-24-info">
    <?php echo jgettext('Options') ?>
</div>

<strong><?php echo jgettext('Packing format options'); ?>:</strong>
<?php EcrHtml::drawPackOpts($this->project->buildOpts); ?>
<br/>
<br/>
<strong class="img icon-16-joomla">
    <?php echo jgettext('Joomla! version'); ?>
</strong>
<br/>

<p>
    <!-- @Joomla!-version-check -->
    <input type="radio" id="jversion15" name="jcompat" value="1.5"
        <?php echo ($this->project->JCompat == '1.5') ? ' checked="checked"' : ''; ?>
        />
    <label for="jversion15" class="img32b icon-joomla-compat-15">&nbsp;</label>

    <input type="radio" id="jversion16" name="jcompat" value="1.6"
        <?php echo (in_array($this->project->JCompat, array('1.6', '1.7', '2.5'))) ? ' checked="checked"' : ''; ?>
        />
    <label for="jversion16" class="img32b icon-joomla-compat-25"></label>
</p>

<strong><?php echo jgettext('Options'); ?>:</strong>
<br/>

<input type="checkbox" <?php echo $chk_upgrade; ?> name="buildvars[method]" id="buildvars_method"
       value="upgrade"/>
<label for="buildvars_method"><?php echo jgettext('Upgrade'); ?></label>
<?php if(2 == ECR_HELP) echo JHTML::tooltip('method=upgrade::'
    .jgettext('This will perform an upgrade on installing your extension')); ?>
<br/>

<input type="checkbox" name="buildopts[]" id="lbl_create_index_html"
    <?php echo (isset($projectOptions['create_indexhtml'])
    && $projectOptions['create_indexhtml'] == 'ON') ? ' checked="checked"' : ''; ?>
       value="create_indexhtml"/>
<label for="lbl_create_index_html"><?php echo jgettext('Create index.html files'); ?></label>
<br/>

<?php if($this->project->type == 'component') : ?>
<input type="checkbox" name="buildopts[]" id="lbl_create_md5"
    <?php echo (isset($projectOptions['create_md5'])
    && $projectOptions['create_md5'] == 'ON') ? ' checked="checked"' : ''; ?>
       value="create_md5"/>
<label for="lbl_create_md5"><?php echo jgettext('Create MD5 checksum file'); ?></label>
<br/>
&nbsp;&nbsp;&nbsp;|__<input type="checkbox" name="buildopts[]" id="lbl_create_md5_compressed"
    <?php echo (isset($projectOptions['create_md5_compressed'])
        && $projectOptions['create_md5_compressed'] == 'ON') ? ' checked="checked"' : ''; ?>
                            value="create_md5_compressed"/>
<label for="lbl_create_md5_compressed"><?php echo jgettext('Compress checksum file'); ?></label>
<?php if(2 == ECR_HELP) echo JHTML::tooltip(jgettext('Compress checksum file').'::'
        .jgettext('This will do a small compression on your checksum file')); ?>
<br/>
<?php endif; ?>
<br/>

<span class="img icon-16-easycreator" style="font-weight: bold;">
    <?php echo jgettext('EasyCreator Options'); ?>
</span>
<br/>

<input type="checkbox" name="buildopts[]" id="lbl_include_ecr_projectfile"
    <?php echo (isset($projectOptions['include_ecr_projectfile'])
    && $projectOptions['include_ecr_projectfile'] == 'ON') ? ' checked="checked"' : ''; ?>
       value="include_ecr_projectfile"/>
<label for="lbl_include_ecr_projectfile">
    <?php echo jgettext('Include EasyCreator Project file'); ?>
</label>
<br/>

<?php if($this->project->type == 'component') : ?>
<input type="checkbox" name="buildopts[]" id="lbl_remove_autocode"
    <?php echo (isset($projectOptions['remove_autocode'])
    && $projectOptions['remove_autocode'] == 'ON') ? ' checked="checked"' : ''; ?>
       value="remove_autocode"/>
<label for="lbl_remove_autocode"><?php echo jgettext('Remove EasyCreator AutoCode'); ?></label>
<br/>
<?php endif; ?>

<br/>
<strong><?php echo jgettext('Build options'); ?>:</strong>
<?php EcrHtml::drawLoggingOptions();
