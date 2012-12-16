<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 25-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$chk_upgrade = ($this->project->method == 'upgrade') ? ' checked="checked"' : '';

/* @var EcrProjectModelBuildpreset $preset */
$preset = $this->preset;

?>
<div class="infoHeader imgbarleft icon24-info">
    <?php echo jgettext('Options') ?>
</div>

<p>
    <strong><?php echo jgettext('Packing format options'); ?>:</strong>
    <?php EcrHtmlOptions::packing($this->project->presets['default']); ?>
</p>
<p>
    <strong class="img icon16-joomla">
        <?php echo jgettext('Joomla! version'); ?>
    </strong>
</p>

<p>
    <!-- @Joomla!-version-check -->
    <input type="radio" id="jversion25" name="jcompat" value="2.5"
        <?php echo ('2.5' == $this->project->JCompat) ? ' checked="checked"' : ''; ?>
        />
    <label for="jversion25" class="inline img32b iconjoomla-compat-25">&nbsp;</label>

    <input type="radio" id="jversion30" name="jcompat" value="3.0"
        <?php echo ('3.0' == $this->project->JCompat) ? ' checked="checked"' : ''; ?>
        />
    <label for="jversion30" class="inline img32b iconjoomla-compat-30">&nbsp;</label>
</p>

<p>
    <strong><?php echo jgettext('Options'); ?>:</strong>
    <br/>

    <input type="checkbox" <?php echo $chk_upgrade; ?> name="buildvars[method]" id="buildvars_method"
           value="upgrade"/>
    <label class="inline" for="buildvars_method"><?php echo jgettext('Upgrade'); ?></label>
    <?php echo EcrHelp::info(jgettext('This will perform an upgrade on installing your extension'), 'method=upgrade'); ?>
    <br/>

    <input type="checkbox" name="buildopts[]" id="createIndexhtml"
        <?php echo ($preset->createIndexhtml == 'ON') ? ' checked="checked"' : ''; ?>
           value="createIndexhtml"/>
    <label class="inline" for="createIndexhtml"><?php echo jgettext('Create index.html files'); ?></label>
    <br/>

    <input type="checkbox" name="buildopts[]" id="createMD5"
        <?php echo ($preset->createMD5 == 'ON') ? ' checked="checked"' : ''; ?>
           value="createMD5"/>
    <label class="inline" for="createMD5"><?php echo jgettext('Create MD5 checksum file'); ?></label>
    <br/>
    &nbsp;&nbsp;&nbsp;|__<input type="checkbox" name="buildopts[]" id="createMD5Compressed"
        <?php echo ($preset->createMD5Compressed == 'ON') ? ' checked="checked"' : ''; ?>
                                value="createMD5Compressed"/>
    <label class="inline" for="createMD5Compressed">
        <?php echo jgettext('Compress checksum file'); ?>
    </label>

    <?php echo EcrHelp::info(jgettext('This will do a small compression on your checksum file'), jgettext('Compress checksum file')); ?>
</p>
