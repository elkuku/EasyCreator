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
    EcrHtml::displayMessage(jgettext('No files found'), 'error');

    return;
endif;

JHTML::_('behavior.modal', 'a.modal');

EcrHtml::initFileTree();

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
                    <div class="ecr_button img icon-16-add" onclick="div_new_element.toggle();">
                        <?php echo jgettext('Add element') ?>
                    </div>
                    <?php
                break;

                default:
                break;
            endswitch;
        echo '<span style="float: right;" class="img icon-16-info hasEasyTip" title="'
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

EcrHtml::contextMenu();

/**
 *
 * @param EasyProject $project The project
 * @return unknown_type
 */
function drawFileTree(EasyProject $project)
{
    $ret = '';

    //--Allowed extensions
    //TODO set somewhere else...
    $allowed_exts = array('php', 'css', 'xml', 'js', 'ini', 'txt', 'html', 'sql');
    $allowed_pics = array('png', 'gif', 'jpg', 'ico');

    $javascript = '';
    $javascript .= " onmousedown=\"setAction(event, '[link]', '[file]', '[id]');\"";
    $javascript .= " onclick=\"ecr_loadFile('', '[link]', '[file]', '[id]');\"";

    $jsFolder = '';
    $jsFolder .= " onmousedown=\"setAction(event, '[link]', '[file]');\"";
    $fileTree = new phpFileTree('', '', $javascript, $jsFolder);

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
                //--This shows a single file not included in anterior directory list ;) - hi plugins...
                $fileName = JFile::getName(JPath::clean($dir));
                $dirName = substr($dir, 0, strlen($dir) - strlen($fileName));
                $oldDir =(isset($oldDir)) ? $oldDir : '';

                if($dirName != $oldDir)
                {
                    $dspl = str_replace(JPATH_ROOT.DS, '', $dirName);
                    $ret .= '<div class="file_tree_path"><strong>JROOT</strong>'.BR.$dspl.'</div>';
                }

                $oldDir = $dirName;

                if( ! isset($fileTree))
                {
                    $fileTree = new phpFileTree($dir, "javascript:", $javascript);
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
        $ecr_project = JRequest::getCmd('ecr_project');
        $link = 'index.php?option=com_easycreator&controller=ajax&task=show_part&tmpl=component';
        $link .= '&ecr_project='.$ecr_project;
        ?>
<script type="text/javascript">
            function showPart(group, part)
            {
                $('addPartShow').className = ' img ajax_loading16';

                $('addPartShow').innerHTML = jgettext('Loading...');
                $('addElementMessage').innerHTML = '';

                new Request({
                    url: '<?php echo $link; ?>&group='+group+'&part='+part,
                    onComplete: function(response)
                    {
                        $('addPartShow').className = '';
                        $('addPartShow').set('html', response);
                        $('addElementMessage').innerHTML = '';
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

            echo '<div class="ecr_button hasEasyTip" title="'
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
