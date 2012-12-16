<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 09-Apr-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$colorPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pchart'.DS.'colours';
$colorFiles = JFolder::files($colorPath);

?>
<script type="text/javascript">
    function getStats(ecr_project) {
        url = ecrAJAXLink;
        url += '&controller=codeeyeajax&task=get_stats';
        url += '&ecr_project=' + ecr_project;

        color = 0;

        for(var i = 0; i < document.adminForm.color.length; i++) {
            if(document.adminForm.color[i].checked) {
                var color = document.adminForm.color[i].value;
            }
        }

        new Request({
            url : url,

            'onRequest' : function() {
                document.id('control_btn').className = 'ajax_loading16';

                document.id('chart1').src = '../media/com_easycreator/admin/images/ajax-loader2.gif';
                document.id('chart2').src = '../media/com_easycreator/admin/images/ajax-loader2.gif';
                document.id('chart3').src = '../media/com_easycreator/admin/images/ajax-loader2.gif';
                document.id('matrix_table').className = 'ajax_loading';
            },

            'onComplete' : function(response) {
                var resp = JSON.decode(response);
                var baseUrl = ecrAJAXLink;

                baseUrl += '&controller=codeeyeajax&task=get_chart';
                baseUrl += '&labels=' + resp.labels;
                baseUrl += '&color=' + color;
                baseUrl += '&tick=' + Math.round(new Date().getTime() / 1000);

                document.id('matrix_table').className = '';

                if(!resp.status) {
                    //-- Error
                    document.id('control').innerHTML = resp.text;
                }
                else {
                    document.id('control').innerHTML = '';
                    document.id('matrix_table').innerHTML = resp.table;
                    document.id('chart').innerHTML = resp.text;

                    document.id('chart1').src = baseUrl + '&data=' + resp.files;
                    document.id('chart2').src = baseUrl + '&data=' + resp.size;
                    document.id('chart3').src = baseUrl + '&data=' + resp.lines;
                }
            }
        }).send();
    }
</script>

<div id="control">
    <div class="ecr_floatbox">
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
                    echo '<span class="colorBox"
                    style="background-color: rgb('.$colour.');">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
                }

                echo '</label>';
                echo '</li>';
            }//foreach
            ?>
        </ul>
    </div>
    <div class="ecr_floatbox">
        <div class="ecr_button img32a icon32-chart" id="control_btn"
             onclick="getStats('<?php echo $this->ecr_project; ?>');">
            <?php echo jgettext('Generate statistics'); ?>
        </div>
    </div>

    <div style="clear: both;"></div>
</div>

<div class="ecr_floatbox">
    <h2><?php echo sprintf(jgettext('Extension %s Code statistics'), $this->project->name); ?></h2>

    <div id="matrix_table"></div>
</div>

<div style="clear: both;"></div>
<div class="ecr_floatbox">
    <h2><?php echo jgettext('Files'); ?></h2>
    <img id="chart1" src=""/>
</div>

<div class="ecr_floatbox">
    <h2><?php echo jgettext('Size'); ?></h2>
    <img id="chart2" src=""/>
</div>

<div class="ecr_floatbox">
    <h2><?php echo jgettext('Code lines'); ?></h2>
    <img id="chart3" src=""/>
</div>

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
        $values = explode(',', $line);

        if(count($values) == 3)
            $colors[] = implode(',', $values);
    }

    return $colors;
}
