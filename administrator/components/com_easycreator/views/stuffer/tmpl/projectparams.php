<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 08-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.html.pane');

ecrScript('parameter');

?>

<div class="toolbar" style="float: right;">
	<table class="toolbar">
		<tr valign="top">
			<td>
			<?php echo $this->xmlSelector; ?>
			</td>
			<?php
            if($this->selected_xml
            && $this->params)
            {
            ?>
			<td>
				<div class="ecr_button img icon16-add" onclick="div_new_group.toggle(); $('addGroupName').focus();"><?php echo jgettext('New group') ?></div>
				<div id="div_new_group" align="left">
				<div style="background-color: #F2F2F2; padding-left: 2px;
				padding-right: 2px; border-left: 1px solid #D9D9D9; border-right: 1px solid #D9D9D9;">
				<br />
					<?php echo jgettext('Name'); ?>:
					<input type="text" id="addGroupName" name="addGroupName" value="" />
					<div id="addGroupMessage"></div>
					<br />

					</div>
					<div class="ecr_button img icon16-add" onclick="addGroup($('addGroupName').value);"><?php echo jgettext('Add group'); ?></div>
				</div>
				<script type="text/javascript">
					var div_new_group = new Fx.Slide('div_new_group');
					div_new_group.hide();
				</script>
			</td>
			<td>
				<div class="ecr_button img icon16-add" onclick="div_new_parameter.toggle();$('addParamName').focus();"><?php echo jgettext('New parameter') ?></div>
				<div id="div_new_parameter" align="left">
				<div style="background-color: #F2F2F2; padding-left: 2px;
				padding-right: 2px; border-left: 1px solid #D9D9D9; border-right: 1px solid #D9D9D9;">
				<br />
					<?php echo jgettext('Name'); ?>:
					<input type="text" id="addParamName" name="addParamName" value="" />
					<div id="addParamMessage"></div>
					<br />
					<?php echo jgettext('paramGroup'); ?>
					<select name="addParamGroup" id="addParamGroup">
						<!-- To be filled by JS -->
					</select>
					<br /><br />
					</div>
					<div class="ecr_button img icon16-add" onclick="newParameter();"><?php echo jgettext('Add parameter'); ?></div>
				</div>
				<script type="text/javascript">
					var div_new_parameter = new Fx.Slide('div_new_parameter');
					div_new_parameter.hide();
				</script>
			</td>
			<td>
				<div class="ecr_button img icon16-ecr_save" onclick="submitbutton('save_params');">
				<?php echo jgettext('Save') ?>
				</div>
			</td>
			<?php
            }
            ?>
		</tr>
	</table>
</div>

<?php
if( ! $this->selected_xml)
{
    EcrHtml::message(jgettext('Please select a XML file'));
    $this->drawDocLinks();

    return;
}

if( ! $this->params->params)
{
    EcrHtml::message(jgettext('No parameters defined'), 'notice');
    $this->drawDocLinks();

    return;
}
?>

<div id="divParameters">
	<!-- To be filled by js -->
</div>

<?php
$jsCntSpacers = 0;

$js = '';

foreach($this->params->params as $group)
{
    ?>
	<script type="text/javascript">addGroup('<?php echo $group->attributes()->group; ?>');</script>
	<?php
    $i = 0;

    foreach($group->param as $param)
    {
        if($param->attributes()->type == 'spacer')
        {
            $this->params->_xml[$groupName]->_children[$i]->_attributes['name'] = '@spacer';
            $jsDivName = 'div_spacer_'.$jsCntSpacers;
            $jsCntSpacers++;
        }
        else
        {
            $jsDivName = 'div_'.$param->attributes()->name;
        }

        $attribs = array();

        foreach($param->attributes() as $k => $v)
        {
            $attribs[] = "\"$k\":\"$v\"";
        }//foreach

        $jsParamAttributes = '{'.implode(',', $attribs).'}';

        $jsParamChildren = '';

        if(count($param->children()))
        {
            $attribs = array();

            foreach($param->children() as $child)
            {
                $attribs[] = '"'.$child->attributes()->value.'":"'.(string)$child.'"';
            }//foreach

            $jsParamChildren = '{'.implode(',', $attribs).'}';
        }

        $jsArgs = "'".$group->attributes()->group."', ".$jsParamAttributes;
        $jsArgs .=($jsParamChildren) ? ", ".$jsParamChildren : '';

        $js .= 'startParam('.$jsArgs.');'.NL;

        $i++;
    }//foreach
}//foreach
?>
<!-- draws a js function call with a js object array from config xml as argument
 for drawing the html parameter - hu.. -->
<script type="text/javascript">
    <?php echo $js; ?>
</script>

<input type="hidden" name="jscnt_spacers" value="<?php echo $jsCntSpacers; ?>"/>
<?php

echo $this->drawDocLinks();
