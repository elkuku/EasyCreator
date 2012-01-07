<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 25-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$params = JComponentHelper::getParams('com_easycreator');
?>
<h3><?php echo jgettext('File name'); ?></h3>

<div style="border: 1px dotted gray; padding: 0.5em; background-color: #ffc; margin: 0.5em;
font-size: 1.3em; font-family: monospace;">
    <?php echo $this->project->comName; ?><div id="ajName" style="color: blue; display: inline;
    margin: 0; font-weight: bold;"></div>.&lt;EXT&gt;
</div>
<strong><?php echo jgettext('Custom name format'); ?></strong>
<?php if(2 == ECR_HELP) echo JHTML::tooltip(jgettext('Custom name format').'::'
    .jgettext('Use:<br />*VERSION*<br />*SVNREV*<br />*DATETIMExxxx*')); ?>
<br />
<input type="radio" name="opt_format" id="opt_format_1" class="custom_opt"
checked="checked"
 onclick="$('cst_format').value=this.value; updateName('<?php echo $this->ecr_project; ?>');"
 value="<?php echo $params->get('custom_name_1'); ?>" />
 <label for="opt_format_1"><tt><?php echo $params->get('custom_name_1'); ?></tt></label>
 <br />
<input type="radio" name="opt_format" id="opt_format_2" class="custom_opt"
 onclick="$('cst_format').value=this.value; updateName('<?php echo $this->ecr_project; ?>');"
 value="<?php echo $params->get('custom_name_2'); ?>" />
 <label for="opt_format_2"><tt><?php echo $params->get('custom_name_2'); ?></tt></label>
 <br />
<input type="radio" name="opt_format" id="opt_format_3" class="custom_opt"
 onclick="$('cst_format').value=this.value; updateName('<?php echo $this->ecr_project; ?>');"
 value="<?php echo $params->get('custom_name_3'); ?>" />
 <label for="opt_format_3"><tt><?php echo $params->get('custom_name_3'); ?></tt></label>
 <br />
 <br />
<?php echo jgettext('Customize'); ?>&nbsp;<div id="ajMessage" style="display: inline;"></div>
<br />
<input type="text" size="50" onkeyup="updateName('<?php echo $this->ecr_project; ?>');"
name="cst_format" id="cst_format" value="<?php echo $params->get('custom_name_1'); ?>"
style="font-family: monospace; font-size: 1.2em;"/>
