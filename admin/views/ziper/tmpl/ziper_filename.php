<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 25-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$params = JComponentHelper::getParams('com_easycreator');

$buildOpts = new JRegistry($this->project->buildOpts);
$javascript = "document.id('cst_format').value=this.value; EcrZiper.updateName();"
?>

<div class="infoHeader img icon24-package_creation">
    <span id="loadStat_filename">
        <?php echo jgettext('File name'); ?>
    </span>
</div>

<div class="customPath">
    <?php echo $this->project->comName; ?><span id="ajName"></span>.zip
</div>

<strong><?php echo jgettext('Custom name format'); ?></strong>

<?php echo EcrHelp::info(jgettext('Use:<br />*VERSION*<br />*VCSREV*<br />*DATETIMExxxx*'), jgettext('Custom name format')); ?>

<br/>
<input type="radio" name="opt_format" id="custom_name_1" class="custom_opt" checked="checked"
       value="<?php echo $buildOpts->get('custom_name_1'); ?>"
       onclick="<?php echo $javascript; ?>"
    />
<label class="inline filename" id="lbl_custom_name_1" for="custom_name_1">
    <?php echo $buildOpts->get('custom_name_1'); ?>
</label>
<br/>
<input type="radio" name="opt_format" id="custom_name_2" class="custom_opt"
       value="<?php echo $buildOpts->get('custom_name_2'); ?>"
       onclick="<?php echo $javascript; ?>"
    />
<label class="inline" id="lbl_custom_name_2" for="custom_name_2">
    <?php echo $buildOpts->get('custom_name_2'); ?>
</label>
<br/>
<input type="radio" name="opt_format" id="custom_name_3" class="custom_opt"
       value="<?php echo $buildOpts->get('custom_name_3'); ?>"
       onclick="<?php echo $javascript; ?>"
    />
<label class="inline" id="lbl_custom_name_3" for="custom_name_3">
    <?php echo $buildOpts->get('custom_name_3'); ?>
</label>
<br/>
<input type="radio" name="opt_format" id="custom_name_4" class="custom_opt"
       value="<?php echo $buildOpts->get('custom_name_4'); ?>"
       onclick="<?php echo $javascript; ?>"
    />
<label class="inline" id="lbl_custom_name_4" for="custom_name_4">
    <?php echo $buildOpts->get('custom_name_4'); ?>
</label>

<hr />

<label class="" for="cst_format">
    <?php echo jgettext('Customize'); ?>&nbsp;
</label>
<input type="text" class="span12" onkeyup="EcrZiper.updateName();"
       name="cst_format" id="cst_format" value="<?php echo $buildOpts->get('custom_name_1'); ?>"
       />
