<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 09-Apr-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$colorPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pchart'.DS.'colours';

$colorFiles = JFolder::files($colorPath);
?>
<script>
function getStats(ecr_project)
{
    url = ecrAJAXLink;
    url += '&controller=codeeyeajax&task=get_stats';
  	url += '&ecr_project=' + ecr_project;

  	color = 0;

  	for (var i=0; i < document.adminForm.color.length; i++)
    {
    	if (document.adminForm.color[i].checked)
       	{
       		var color = document.adminForm.color[i].value;
       	}
    }//for

  	new Request({
  	  	url: url,

  	  	'onRequest' : function()
      	{
          $('control_btn').className = 'ajax_loading16';

          $('chart1').src = 'components/com_easycreator/assets/images/ajax-loader2.gif';
          $('chart2').src = 'components/com_easycreator/assets/images/ajax-loader2.gif';
          $('chart3').src = 'components/com_easycreator/assets/images/ajax-loader2.gif';
          $('matrix_table').className = 'ajax_loading';
      	},

      	'onComplete' : function(response)
      	{
          	var resp = Json.evaluate(response);

          	var baseUrl = ecrAJAXLink;
          	baseUrl += '&controller=codeeyeajax&task=get_chart';
          	baseUrl += '&labels='+resp.labels;
          	baseUrl += '&color='+color;
          	baseUrl += '&tick='+Math.round(new Date().getTime() / 1000);

            $('matrix_table').className = '';

            if( ! resp.status) {
              //-- Error
              	$('control').innerHTML = resp.text;
          	}
          	else
          	{
              	$('control').innerHTML = '';
              	$('matrix_table').innerHTML = resp.table;
              	$('chart').innerHTML = resp.text;

                $('chart1').src=baseUrl+'&data='+resp.files;
                $('chart2').src=baseUrl+'&data='+resp.size;
                $('chart3').src=baseUrl+'&data='+resp.lines;
          	}
      	}
  	}).send();
}//function
</script>

<div id="control">
<?php ecrHTML::floatBoxStart(); ?>
<h4><?php echo jgettext('Color scheme'); ?></h4>
<ul class="colorChooser">
<?php
foreach($colorFiles as $fileName)
{
    $colours = loadColorPalette($colorPath.DS.$fileName);
    $id = preg_replace("/[^0-9]/", "", $fileName);
    echo '<li style="clear: both;">';
    echo '<label for="color'.$id.'">';
    echo '<input type="radio" name="color" id="color'.$id.'" value="'.$id.'" />';

    foreach($colours as $colour)
    {
        echo '<span class="colorBox" style="background-color: rgb('.$colour.');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
    }//foreach

    echo '</label>';
    echo '</li>';
}//foreach
?>
</ul>
<?php ecrHTML::floatBoxEnd(); ?>
 <?php ecrHTML::floatBoxStart(); ?>
  <div class="ecr_button img32a icon-32-chart" id="control_btn"
	onclick="getStats('<?php echo $this->ecr_project; ?>');">
	<?php echo jgettext('Generate statistics'); ?>
	</div>

<?php ecrHTML::floatBoxEnd(); ?>

<div style="clear: both;"></div>
</div>

<?php ecrHTML::floatBoxStart(); ?>
<h2><?php echo sprintf(jgettext('Extension %s Code statistics'), $this->project->name); ?></h2>
<div id="matrix_table"></div>
<?php ecrHTML::floatBoxEnd(); ?>

<div style="clear: both;"></div>
<?php ecrHTML::floatBoxStart(); ?>
<h2><?php echo jgettext('Files'); ?></h2>
<img id="chart1" src="" />
<?php ecrHTML::floatBoxEnd(); ?>

<?php ecrHTML::floatBoxStart(); ?>
<h2><?php echo jgettext('Size'); ?></h2>
<img id="chart2" src="" />
<?php ecrHTML::floatBoxEnd(); ?>

<?php ecrHTML::floatBoxStart(); ?>
<h2><?php echo jgettext('Code lines'); ?></h2>
<img id="chart3" src="" />
<?php ecrHTML::floatBoxEnd(); ?>

<div style="clear: both;"></div>

<pre id="chart"></pre>

<?php
/**
 * Load the color palette from a file.
 *
 * @param string $FileName Full path to file
 *
 * @return array
 */
function loadColorPalette($FileName)
{
    $fileContents = explode("\n", JFile::read($FileName));

    $colors = array();

    foreach($fileContents as $line)
    {
        $Values = split(',', $line);

        if(count($Values) == 3)
        {
            $colors[] = implode(',', $Values);
        }
    }//foreach

    return $colors;
}//function
