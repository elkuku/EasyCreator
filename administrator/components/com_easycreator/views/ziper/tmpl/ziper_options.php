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
?>

<div class="infoHeader img icon-24-package_creation">
    <?php echo jgettext('File name'); ?>
</div>
<span id="ajMessage" style="float: right;"></span>

<div style="border: 1px dotted gray; padding: 0.5em; background-color: #ffc; margin: 0.5em;
font-size: 1.3em; font-family: monospace;">
    <?php echo $this->project->comName; ?><span id="ajName" style="color: blue; display: inline;
    margin: 0; font-weight: bold;"></span>.zip
</div>

<strong><?php echo jgettext('Custom name format'); ?></strong>

<?php if(2 == ECR_HELP) echo JHTML::tooltip(jgettext('Custom name format').'::'
    .jgettext('Use:<br />*VERSION*<br />*VCSREV*<br />*DATETIMExxxx*')); ?>

<br/>
<input type="radio" name="opt_format" id="opt_format_1" class="custom_opt"
       checked="checked"
       onclick="document.id('cst_format').value=this.value; EcrZiper.updateName('<?php echo $this->ecr_project; ?>');"
       value="<?php echo $buildOpts->get('custom_name_1'); ?>"/>
<label for="opt_format_1"><tt><?php echo $buildOpts->get('custom_name_1'); ?></tt></label>
<br/>
<input type="radio" name="opt_format" id="opt_format_2" class="custom_opt"
       onclick="document.id('cst_format').value=this.value; EcrZiper.updateName('<?php echo $this->ecr_project; ?>');"
       value="<?php echo $buildOpts->get('custom_name_2'); ?>"/>
<label for="opt_format_2"><tt><?php echo $buildOpts->get('custom_name_2'); ?></tt></label>
<br/>
<input type="radio" name="opt_format" id="opt_format_3" class="custom_opt"
       onclick="document.id('cst_format').value=this.value; EcrZiper.updateName('<?php echo $this->ecr_project; ?>');"
       value="<?php echo $buildOpts->get('custom_name_3'); ?>"/>
<label for="opt_format_3"><tt><?php echo $buildOpts->get('custom_name_3'); ?></tt></label>
<br/>
<input type="radio" name="opt_format" id="opt_format_4" class="custom_opt"
       onclick="document.id('cst_format').value=this.value; EcrZiper.updateName('<?php echo $this->ecr_project; ?>');"
       value="<?php echo $buildOpts->get('custom_name_4'); ?>"/>
<label for="opt_format_4"><tt><?php echo $buildOpts->get('custom_name_4'); ?></tt></label>
<br/>
<br/>
<label for="cst_format">
    <?php echo jgettext('Customize'); ?>&nbsp;
</label>
<br/>
<input type="text" size="50" onkeyup="EcrZiper.updateName('<?php echo $this->ecr_project; ?>');"
       name="cst_format" id="cst_format" value="<?php echo $buildOpts->get('custom_name_1'); ?>"
       style="font-family: monospace; font-size: 1.2em;"/>
