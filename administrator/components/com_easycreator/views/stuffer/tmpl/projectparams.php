<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
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
            if($this->selected_xml && isset($this->params->_xml))
            {
            ?>
			<td>
				<div class="ecr_button img icon-16-add" onclick="div_new_group.toggle(); $('addGroupName').focus();"><?php echo jgettext('New group') ?></div>
				<div id="div_new_group" align="left">
				<div style="background-color: #F2F2F2; padding-left: 2px; padding-right: 2px; border-left: 1px solid #D9D9D9; border-right: 1px solid #D9D9D9;">
				<br />
					<?php echo jgettext('Name'); ?>:
					<input type="text" id="addGroupName" name="addGroupName" value="" />
					<div id="addGroupMessage"></div>
					<br />

					</div>
					<div class="ecr_button img icon-16-add" onclick="newGroup();"><?php echo jgettext('Add group'); ?></div>
				</div>
				<script type="text/javascript">
					var div_new_group = new Fx.Slide('div_new_group');
					div_new_group.hide();
				</script>
			</td>
			<td>
				<div class="ecr_button img icon-16-add" onclick="div_new_parameter.toggle();$('addParamName').focus();"><?php echo jgettext('New parameter') ?></div>
				<div id="div_new_parameter" align="left">
				<div style="background-color: #F2F2F2; padding-left: 2px; padding-right: 2px; border-left: 1px solid #D9D9D9; border-right: 1px solid #D9D9D9;">
				<br />
					<?php echo jgettext('Name'); ?>:
					<input type="text" id="addParamName" name="addParamName" value="" />
					<div id="addParamMessage"></div>
					<br />
					<?php echo jgettext('paramGroup'); ?>
					<select name="addParamGroup" id="addParamGroup">
						<?php
                        foreach($this->params->_xml as $groupName => $group)
                        {
                            echo '<option>'.$groupName.'</option>';
                        }//foreach
                        ?>
					</select>
					<br /><br />
					</div>
					<div class="ecr_button img icon-16-add" onclick="newParameter();"><?php echo jgettext('Add parameter'); ?></div>
				</div>
				<script type="text/javascript">
					var div_new_parameter = new Fx.Slide('div_new_parameter');
					div_new_parameter.hide();
				</script>
			</td>
			<td>
				<div class="ecr_button img icon-16-save" onclick="submitbutton('save_params');">
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
    ecrHTML::displayMessage(jgettext('Please select a XML file'));
    drawDocLinks();

    return;
}
else if( ! isset($this->params->_xml))
{
    ecrHTML::displayMessage(jgettext('No parameters defined'), 'notice');
    drawDocLinks();

    return;
}
?>

<div id="divParameters">
	<!-- To be filled by js -->
</div>

<?php
$jsCntSpacers = 0;

foreach($this->params->_xml as $groupName => $group)
{
    ?>
	<script type="text/javascript">addGroup('<?php echo $groupName; ?>');</script>
	<?php
    $i = 0;

    foreach($group->_children as $param)
    {
        if($param instanceof JSimpleXMLElement)
        {
            if($param->_attributes['type'] == 'spacer')
            {
                $this->params->_xml[$groupName]->_children[$i]->_attributes['name'] = '@spacer';
                $jsDivName = 'div_spacer_'.$jsCntSpacers;
                $jsCntSpacers++;
            }
            else
           {
                $jsDivName = 'div_'.$param->_attributes['name'];
            }

            drawParam($groupName, $param, $jsDivName);
        }

        $i++;
    }//foreach
}//foreach
?>
<input type="hidden" name="jscnt_spacers" value="<?php echo $jsCntSpacers; ?>"/>
<?php
drawDocLinks();

/*
 * FUNCTIONS..
 */

/**
 * draws a js function call with a js object array from
 * config xml as argument for drawing the html parameter - hu..
 *
 * @param string $groupName
 * @param object $param
 * @return void
 */
function drawParam($groupName, $param, $jsDivName)
{
    $jsParamAttributes = '{';
    $i = 1;

    foreach($param->_attributes as $k=>$v)
    {
        $jsParamAttributes .= '"'.$k.'":"'.$v.'"';

        if($i < count($param->_attributes))
        {
            $jsParamAttributes .= ',';
        }

    $i++;
    }//foreach
    $jsParamAttributes .= '}';

    $jsParamChildren = '';

    if(count($param->_children))
    {
        $jsParamChildren .= '{';
        $i = 1;

        foreach($param->_children as $child)
        {
            if($child instanceof JSimpleXMLElement)
            {
                $jsParamChildren .= '"'.$child->_attributes['value'].'":"'.$child->_data.'"';

                if($i < count($param->_children))
                {
                    $jsParamChildren .= ',';
                }
            }

            $i++;
        }//foreach
        $jsParamChildren .= '}';
    }

    $jsArgs = "'".$groupName."', ".$jsParamAttributes;
    $jsArgs .=($jsParamChildren) ? ", ".$jsParamChildren : '';
    /*
	 * drawing html by javascript...
	 */
    ?>
	<script type="text/javascript">
		startParam(<?php echo $jsArgs; ?>);
	</script>
	<?php
}//function

/**
 * draws a list of related links
 */
function drawDocLinks()
{
    $docLinks = array(
        'Standard parameter types'=>'http://docs.joomla.org/Standard_parameter_types'
        , 'Reference: XML parameters'
        =>'http://dev.joomla.org/component/option,com_jd-wiki/Itemid,/id,references:xml_parameters/'
        , 'Component parameters'=>'http://docs.joomla.org/Component_parameters'
        , 'Defining a parameter in templateDetails.xml'=>'http://docs.joomla.org/Defining_a_parameter_in_templateDetails.xml'
        , 'Creating custom template parameter types'=>'http://docs.joomla.org/Creating_custom_template_parameter_types'
    );

    echo '<br /><hr /><br />';
    echo '<div class="explanation">';
    echo '<br /><strong style="background-color: white; padding: 5px;">'.jgettext('Infos on parameters (external)').'</strong>';
    echo '<ul>';

    foreach($docLinks as $title => $link)
    {
        echo '<li><a class="external" href="'.$link.'" target="_blank" />'.$title.'</a></li>';
    }//foreach
    echo '</ul>';
    echo '</div>';
    echo '<br />';
}//function

function saveParams()
{
    //not used yet
    $params = JComponentHelper::getParams('com_componentname');
    $value = $params->get('someparams');
    $value = '...new...value';
    $params->set('someparams', $value);
    $row =& JTable::getInstance('component');
    $row->loadByOption('com_componentname');
    $parameters = JArrayHelper::fromObject($row);
    $parameters['params'] = $params->toString();
    $row->bind($parameters);
    $row->store();
}//function
