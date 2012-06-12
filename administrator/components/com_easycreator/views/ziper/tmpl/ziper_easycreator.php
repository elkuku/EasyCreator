<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 03-Jun-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/* @var EcrProjectModelBuildpreset $preset */
$preset = $this->preset;
?>
<div class="infoHeader img icon24-easycreator">
    <?php echo jgettext('EasyCreator Options'); ?>
</div>

<input type="checkbox" name="buildopts[]" id="includeEcrProjectfile"
    <?php echo ('ON' == $preset->includeEcrProjectfile) ? ' checked="checked"' : ''; ?>
       value="includeEcrProjectfile"/>
<label class="inline" for="includeEcrProjectfile">
    <?php echo jgettext('Include EasyCreator Project file'); ?>
</label>
<br/>

<?php if($this->project->type == 'component') : ?>
<input type="checkbox" name="buildopts[]" id="removeAutocode"
    <?php echo ($preset->removeAutocode == 'ON') ? ' checked="checked"' : ''; ?>
       value="removeAutocode"/>
<label class="inline" for="removeAutocode">
    <?php echo jgettext('Remove EasyCreator AutoCode'); ?>
</label>
<?php endif;
