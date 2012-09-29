<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

if( ! count($this->project->copies)):
    EcrHtml::message(jgettext('No files found'), 'error');

    return;
endif;

JHTML::_('behavior.modal', 'a.ecr_modal');

ecrLoadMedia('php_file_tree');

//-- Create a cache instance.
$cache = JFactory::getCache('EasyCreator_'.$this->ecr_project);

$cache->setCaching(1);

$fileTree = $cache->call('drawFileTree', $this->project);
?>

<table width="100%">
    <tr valign="top">
        <td width="15%">
        <div class="ecr_floatbox" style="min-width: 200px;">
            <?php
            switch($this->project->type):
                case 'component':
                    ?>
                    <div class="btn block" onclick="div_new_element.toggle();">
                        <i class="img icon16-add"></i>
                        <?php echo jgettext('Add element') ?>
                    </div>
                    <?php
                break;

                default:
                break;
            endswitch;
        echo '<span style="float: right;" class="img icon16-info hasTip" title="'
        .jgettext('File tree').'::'.jgettext('Left click files to edit.').'<br />'
        .jgettext('Right click files and folders for options.').'">&nbsp;</span>';

        //-- The file tree
        echo $fileTree;

        ?>
        </div>

        </td>
        <td>
        <div id="div_new_element">
            <?php
            switch($this->project->type):
                case 'component':
                    drawAddElementTable();
                break;

                default:
                    echo $this->project->type.' not supported yet...<br />';
                break;
            endswitch;
            ?>
        </div>

        <script type="text/javascript">
            var div_new_element = new Fx.Slide('div_new_element');
            div_new_element.hide();
        </script>
        <?php EcrHtml::prepareFileEdit(); ?>
        </td>
    </tr>
</table>

<div id="log" class="ecr_log"></div>

<?php

EcrHtmlContextmenu::display();

/**
 *
 * @param EcrProjectBase $project The project
 * @return string
 */
function drawFileTree(EcrProjectBase $project)
{
    $ret = '';

    $javascript = '';
    $javascript .= " onmousedown=\"setAction(event, '[link]', '[file]', '[id]');\"";
    $javascript .= " onclick=\"ecr_loadFile('', '[link]', '[file]', '[id]');\"";

    $jsFolder = '';
    $jsFolder .= " onmousedown=\"setAction(event, '[link]', '[file]');\"";
    $fileTree = new EcrFileTree('', '', $javascript, $jsFolder);

    foreach($project->copies as $dir)
    {
        if(is_dir($dir))
        {
            $dspl = str_replace(JPATH_ROOT, '', $dir);
            $dspl = trim($dspl, DS);
            $dspl = str_replace(DS, ' '.DS.' ', $dspl);
            $ret .= '<div class="file_tree_path"><strong>JROOT</strong>'.BR.$dspl.'</div>';

            $fileTree->setDir($dir);
            $ret .= $fileTree->startTree();
            $ret .= $fileTree->drawTree();
            $ret .= $fileTree->endTree();
        }
        else if(JFile::exists($dir))
        {
            $show = true;

            foreach($project->copies as $test)
            {
                if(strpos($dir, $test))
                {
                    $show = false;
                }
            }//foreach

            if($show)
            {
                //-- This shows a single file not included in anterior directory list ;) - hi plugins...
                $fileName = JFile::getName(JPath::clean($dir));
                $dirName = substr($dir, 0, strlen($dir) - strlen($fileName));
                $oldDir =(isset($oldDir)) ? $oldDir : '';

                if($dirName != $oldDir)
                {
                    $dspl = str_replace(JPATH_ROOT, '', $dirName);
                    $ret .= '<div class="file_tree_path"><strong>JROOT</strong>'.$dspl.'</div>';
                }

                $oldDir = $dirName;

                if( ! isset($fileTree))
                {
                    $fileTree = new EcrFileTree($dir, "javascript:", $javascript);
                }
                else
                {
                    $fileTree->setDir($dir);
                }

                $ret .= $fileTree->startTree();
                $ret .= $fileTree->getLink($dirName, $fileName);
                $ret .= $fileTree->endTree();

                $ret .= '<br />';
            }
        }
    }//foreach

    return $ret;
}//function

    function drawAddElementTable()
    {
        $ecr_project = JFactory::getApplication()->input->get('ecr_project');
        $link = 'index.php?option=com_easycreator&controller=ajax&task=show_part&tmpl=component';
        $link .= '&ecr_project='.$ecr_project;
        ?>
<script type="text/javascript">
            function showPart(group, part)
            {
                document.id('addPartShow').className = ' img ajax_loading16';

                document.id('addPartShow').innerHTML = jgettext('Loading...');
                document.id('addElementMessage').innerHTML = '';

                new Request({
                    url: '<?php echo $link; ?>&group='+group+'&part='+part,
                    onComplete: function(response)
                    {
                        document.id('addPartShow').className = '';
                        document.id('addPartShow').set('html', response);
                        document.id('addElementMessage').innerHTML = '';
                        div_new_element.show();
                    }
                }).send();

                return false;
            }//function
        </script>
<div id="addElementMessage"></div>
<div id="addPartPartsList" style="float: left;"><?php
foreach(EcrProjectHelper::getPartsGroups() as $group)
{
    echo '<strong style="color: blue;">'.ucfirst($group).'</strong><br />';

    foreach(EcrProjectHelper::getParts($group) as $part)
    {
        $easyPart = EcrProjectHelper::getPart($group, $part, '', '');

        if($easyPart)
        {
            $toolTip = $group.'::'.$part;
            $title = $part;

            if(method_exists($easyPart, 'info'))
            {
                $info = $easyPart->info();
                $title = $info->title;
                $toolTip = $info->title;

                if($info->description)
                {
                    $toolTip .= '::'.$info->description;
                }
            }

            echo '<div class="btn block hasTip" title="'
            .$toolTip.'" onclick="showPart(\''.$group.'\', \''.$part.'\');">'.$title.'</div>';
        }
    }//foreach
}//foreach

?></div>
<div style="float: left; margin-left: 1em;">
	<div class="ecr_floatbox">
    	<div id="addPartShow">
    		<strong style="color: red;"><?php echo jgettext('Select an element'); ?></strong>
    	</div>
	</div>
</div>

<div style="clear: both;"></div>
<?php
}//function
