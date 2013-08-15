<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 09-Sep-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$sniffer = new EcrPearHelperCodesniffer;

if(false == $sniffer->checkEnv()):
    echo 'Env check failed.. cannot continue :(';

    return;
endif;

$standard = JFactory::getApplication()->input->get('sniff_standard');

$standards = $sniffer->getStandards();

//-- @todo provide own standards ¿ JFolder::folders(JPATH_COMPONENT.DS.'helpers'.DS.'CodeSniffer');
$easyStandards = array();

$formats = array('full', 'xml', 'checkstyle', 'csv', 'emacs', 'source', 'summary');

ecrLoadMedia('php_file_tree');

$fileTree = drawFileTree($this->project);
?>

<div class="ecr_floatbox">
    <?php echo $fileTree; ?>
    <div onclick="sniffFolder();" class="btn block left">
        <i class="img32 icon32-nose"></i>
        <?php echo jgettext('Sniff folder')?></div>
</div>

<div class="ecr_floatbox">
    Standard:<br/>
    <select name="sniff_standard" id="sniff_standard">
        <optgroup label="PEAR provided">
            <?php
            foreach($standards as $standard):
                echo '<option>'.$standard.'</option>';
            endforeach;
            ?>
        </optgroup>

        <optgroup label="EasyCreator provided">
            <?php
            foreach($easyStandards as $standard):
                echo '<option value="'.JPATH_COMPONENT.DS.'helpers'.DS.'CodeSniffer'.DS.$standard.'">'
                    .$standard.'</option>';
            endforeach;
            ?>
        </optgroup>
    </select>

    <br/>
    Format:<br/>
    <select name="sniff_format" id="sniff_format">
        <?php
        foreach($formats as $format):
            echo '<option >'.$format.'</option>';
        endforeach;
        ?>
    </select>
    <br/><br/>
    <input type="checkbox" name="sniff_verbose" id="sniff_verbose"/>
    <label class="inline" for="sniff_verbose">Verbose</label>

    <?php
    foreach($this->project->copies as $dir):
        if(is_dir($dir)):
            $d = str_replace(JPATH_ROOT.DS, '', $dir);
            echo '<div onclick="setPath(\''.$d.'\'); sniffFolder();"'
                .' class="btn block left" style="padding: left: 45px;">'
                .'<i class="img32 icon32-nose"></i>'
                .$d.'</div>';
        endif;
    endforeach;

    echo '<br />';
    echo jgettext('Perform only');
    echo '<br />';

    foreach($easyStandards as $standard):
        echo '<h3>'.$standard.'</h3>';

        $cats = JFolder::folders(JPATH_COMPONENT.DS.'helpers'.DS.'CodeSniffer'.DS.$standard.DS.'Sniffs');

        foreach($cats as $cat):
            echo '<strong>'.$cat.'</strong>'.BR;

            $snfs = JFolder::files(JPATH_COMPONENT.DS.'helpers'.DS.'CodeSniffer'.DS.$standard.DS.'Sniffs'.DS.$cat);

            foreach($snfs as $snf):
                $s = str_replace('Sniff.php', '', $snf);
                echo '<input type="checkbox" name="sniff_sniffs" value="'
                    .$standard.'.'.$cat.'.'.$s.'" id="'.$cat.'.'.$s.'"/>';

                echo ' <label for="'.$cat.'.'.$s.'">'.$s.'</label>'.BR;
            endforeach;
        endforeach;
    endforeach;
    ?>
</div>

<div class="ecr_floatbox"">
    <span id="dspl_sniff_folder"></span>
    <span id="dspl_sniff_file"></span>
    <br/>
    <div id="ecr_title_file"></div>
</div>

<div style="clear: both;"></div>
<div id="ecr_codeeye_output" style="padding-top: 0.2em;"><h2><?php echo jgettext('Output')?></h2></div>
<pre id="ecr_codeeye_console"><?php echo jgettext('Console'); ?></pre>

    <div class="clr" style="xheight: 75px;"></div>

<?php

echo EcrHtmlDebug::logConsole();

/**
 * Draws a file tree.
 *
 * @param EcrProjectBase $project The project
 *
 * @return string
 */
function drawFileTree(EcrProjectBase $project)
{
    $ret = '';

    $javascript = '';
    $javascript .= " onclick=\"loadSniff('[link]', '[file]', '[id]');\"";

    $jsFolder = '';
    $jsFolder .= " onmousedown=\"setPath('[link]/[file]');\"";
    $fileTree = new EcrFileTree('', '', $javascript, $jsFolder);

    foreach($project->copies as $dir)
    {
        if(is_dir($dir))
        {
            $dspl = str_replace(JPATH_ROOT.DS, '', $dir);
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
            }

            if( ! $show)
            {
                continue;
            }

            //-- This shows a single file not included in anterior directory list ;) - hi plugins...
            $fileName = basename(JPath::clean($dir));
            $dirName = substr($dir, 0, strlen($dir) - strlen($fileName));
            $oldDir = (isset($oldDir)) ? $oldDir : '';

            if($dirName != $oldDir)
            {
                $dspl = str_replace(JPATH_ROOT.DS, '', $dirName);
                $ret .= '<div class="file_tree_path"><strong>JROOT</strong>'.BR.$dspl.'</div>';
            }

            $oldDir = $dirName;

            if(false == isset($fileTree))
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

    return $ret;
}
