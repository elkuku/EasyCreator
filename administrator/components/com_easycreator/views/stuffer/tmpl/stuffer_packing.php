<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views.Stuffer
 * @author     Nikolai Plath
 * @author     Created on 26-May-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/* @var EcrProjectModelBuildpreset $preset */
//$preset = $this->project->buildOpts;
$preset = $this->project->presets['default'];
$options = new JObject($this->project->buildOpts);
$buildEvents = array('precopy', 'postcopy', 'postbuild');

$js = '';

/* @var EcrProjectAction $action */
foreach($preset->actions as $action) :
    $js .= "   Stuffer.addAction('$action->type', '$action->event', ".json_encode($action->getProperties()).");\n";
endforeach;

$js .= "\n   Stuffer.initActions('container_action');";

JFactory::getDocument()->addScriptDeclaration("window.addEvent('domready', function() {\n".$js."\n});");

?>
<div class="ecr_floatbox">

    <div class="infoHeader img icon24-package_creation">
        <?php echo jgettext('Package'); ?>
    </div>

    <label for="preset" class="inline">
        <?php echo jgettext('Preset'); ?>
    </label>

    <?php echo $this->lists['presets']; ?>

    <label for="preset_saveas" class="inline">
        <?php echo jgettext('Save as'); ?>
    </label>
    <input type="text" name="preset_saveas" id="preset_saveas" class="span2"/>

    <span class="btn" onclick="Stuffer.savePreset(this);">
        <?php echo jgettext('Save'); ?>
    </span>

    <hr />

    <label for="buildFolder" class="inline">
        <?php echo jgettext('Build folder'); ?>
    </label>
    <?php echo EcrHelp::info(
    jgettext('The folder where your final package ends up. The folders extension_name and version will be added automatically.')
        .sprintf(jgettext('<br />If left blank the folder <strong>%s</strong> wil be used'), ECRPATH_BUILDS)
    , jgettext('Build folder'));
    ?>
    <input type="text" name="buildvars[zipPath]" id="buildFolder" class="span5"
           value="<?php echo $this->project->zipPath; ?>"/>
    <?php
    if($this->project->zipPath && false == JFolder::exists($this->project->zipPath)) :
        EcrHtml::message(sprintf(jgettext('The folder %s does not exist'), $this->project->zipPath), 'warning');
    endif;
    ?>

    <hr />

    <h4><?php echo jgettext('Compression'); ?></h4>

    <?php EcrHtmlOptions::packing($preset); ?>

    <hr />

    <h4><?php echo jgettext('Options'); ?></h4>

    <input type="checkbox" name="buildopts[]" id="createIndexhtml"
        <?php echo ('ON' == $preset->createIndexhtml) ? ' checked="checked"' : ''; ?>
           value="createIndexhtml"/>
    <label class="inline" for="createIndexhtml">
        <?php echo jgettext('Create index.html files'); ?>
    </label>
    <br/>

    <input type="checkbox" name="buildopts[]" id="createMD5"
        <?php echo ('ON' == $preset->createMD5) ? ' checked="checked"' : ''; ?>
           value="createMD5"/>
    <label class="inline" for="createMD5">
        <?php echo jgettext('Create MD5 checksum file'); ?>
    </label>
    <br/>
    &nbsp;&nbsp;&nbsp;|__<input type="checkbox" name="buildopts[]" id="createMD5Compressed"
    <?php echo ('ON' == $preset->createMD5Compressed) ? ' checked="checked"' : ''; ?>
                                value="createMD5Compressed"/>
    <label class="inline" for="createMD5Compressed">
        <?php echo jgettext('Compress checksum file'); ?>
    </label>
    <?php echo EcrHelp::info(jgettext('This will do a small compression on your checksum file')
    , jgettext('Compress checksum file')); ?>

    <h4 class="img icon16-easycreator"><?php echo jgettext('EasyCreator Options'); ?></h4>

    <input type="checkbox" name="buildopts[]" id="includeEcrProjectfile"
        <?php echo ('ON' == $preset->includeEcrProjectfile) ? ' checked="checked"' : ''; ?>
           value="includeEcrProjectfile"/>
    <label class="inline" for="includeEcrProjectfile">
        <?php echo jgettext('Include EasyCreator Project file'); ?>
    </label>
    <br/>

    <input type="checkbox" name="buildopts[]" id="removeAutocode"
        <?php echo ('ON' == $preset->removeAutocode) ? ' checked="checked"' : ''; ?>
           value="removeAutocode"/>
    <label class="inline" for="removeAutocode">
        <?php echo jgettext('Remove EasyCreator AutoCode'); ?>
    </label>

    <hr/>

    <h4><?php echo jgettext('File name'); ?></h4>

    <div class="control-group">
        <label class="control-label" for="custom_name_1">
            <?php echo jgettext('Default name'); ?>
        </label>

        <div class="controls">
            <input type="text" size="30" name="buildopts[custom_name_1]" id="custom_name_1"
                   value="<?php echo $options->get('custom_name_1'); ?>"/>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="custom_name_2">
            <?php echo jgettext('Custom name'); ?>
        </label>

        <div class="controls">
            <input type="text" size="30" name="buildopts[custom_name_2]" id="custom_name_2"
                   value="<?php echo $options->get('custom_name_2'); ?>"/>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="custom_name_3">
            <?php echo jgettext('Custom name'); ?>
        </label>

        <div class="controls">
            <input type="text" size="30" name="buildopts[custom_name_3]" id="custom_name_3"
                   value="<?php echo $options->get('custom_name_3'); ?>"/>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="custom_name_4">
            <?php echo jgettext('Custom name'); ?>
        </label>

        <div class="controls">
            <input type="text" size="30" name="buildopts[custom_name_4]" id="custom_name_4"
                   value="<?php echo $options->get('custom_name_4'); ?>"/>
        </div>
    </div>

    <div class="btn-toolbar">
        <span class="btn" onclick="Stuffer.loadFilenameDefaults(this);">
            <i class="img icon16-reload"></i>
            <?php echo jgettext('Reset to default'); ?>
        </span>
    </div>

    <?php echo EcrHelp::info(jgettext('Use:<br />*VERSION*<br />*VCSREV*<br />*DATETIMExxxx*'), jgettext('Custom name format')); ?>

    <hr/>

    <h4><?php echo jgettext('Build Actions'); ?></h4>

    <div id="container_action">
        <?php foreach($buildEvents as $event) : ?>
        <strong style="color: blue;"><?php echo ucfirst($event); ?></strong>
        <ul id="container_actions_<?php echo $event; ?>"></ul>
        <?php endforeach; ?>
    </div>

    <hr/>

    <h4><?php echo jgettext('New Action'); ?></h4>
    <label class="inline">
        <?php echo jgettext('Action'); ?>
    </label>
    <?php echo EcrHtmlSelect::actions(); ?>
    <label class="inline" for="sel_event">
        <?php echo jgettext('Event'); ?>
    </label>
    <select class="span2" id="sel_event" name="event">
        <option value="precopy">Pre Copy</option>
        <option value="postcopy">Post Copy</option>
        <option value="postbuild">Post Build</option>
    </select>

    <div class="btn-toolbar">
        <div class="btn" onclick="Stuffer.newAction(this);">
            <i class="img icon16-add"></i>
            <?php echo jgettext('Add Action');?>
        </div>
    </div>

    <?php echo EcrHelp::info('${temp_dir}<br />${j_root}', jgettext('Available replacements')); ?>

</div>
