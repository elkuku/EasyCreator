<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 09-Sep-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

ecrLoadHelper('pearhelpers.CodeSniffer');

$sniffer = new EasyCodeSniffer;

if( ! $sniffer->checkEnv()):
    echo 'Env check failed.. cannot continue :(';

    return;
endif;

$standard = JRequest::getCmd('sniff_standard');

$standards = $sniffer->getStandards();
$easyStandards = JFolder::folders(JPATH_COMPONENT.DS.'helpers'.DS.'CodeSniffer');

$formats = array('full', 'xml', 'checkstyle', 'csv', 'emacs', 'source', 'summary');

EcrHtml::initFileTree();

$fileTree = drawFileTree($this->project);
?>

<div class="ecr_floatbox">
        <?php echo $fileTree; ?>
        <div onclick="sniffFolder();" class="ecr_button img32 icon-32-nose"><?php echo jgettext('Sniff folder')?></div>
</div>

<div class="ecr_floatbox">
        Standard:<br />
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
            echo '<option value="'.JPATH_COMPONENT.DS.'helpers'.DS.'CodeSniffer'.DS.$standard.'">'.$standard.'</option>';
        endforeach;
?>
        </optgroup>
        </select>

        <br />
        Format:<br />
        <select name="sniff_format" id="sniff_format">
<?php
        foreach($formats as $format):
            echo '<option >'.$format.'</option>';
        endforeach;
?>
        </select>
        <br /><br />
        <input type="checkbox" name="sniff_verbose" id="sniff_verbose" />
        <label for="sniff_verbose">Verbose</label>

        <?php
        foreach($this->project->copies as $dir):
            if(is_dir($dir)):
                $d = str_replace(JPATH_ROOT.DS, '', $dir);
                echo '<div onclick="setPath(\''.$d.'\'); sniffFolder();"'
                .' class="ecr_button img32 icon-32-nose" style="padding: left: 45px;">'.$d.'</div>';
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
    <br />
    <div id="ecr_title_file"></div>
</div>

<div style="clear: both;"></div>
<div id="ecr_codeeye_output" style="padding-top: 0.2em;"><h2><?php echo jgettext('Output')?></h2></div>
<pre id="ecr_codeeye_console"><?php echo jgettext('Console'); ?></pre>
<?php

/**
 * Draws a file tree.
 *
 * @param EasyProject $project The project
 *
 * @return string
 */
function drawFileTree(EasyProject $project)
{
    $ret = '';

    $file_path = JRequest::getString('file_path');
    $file_name = JRequest::getString('file_name');

    //--Allowed extensions
    //TODO set somewhere else...
    $allowed_exts = array('php', 'css', 'xml', 'js', 'ini', 'txt', 'html', 'sql');
    $allowed_pics = array('png', 'gif', 'jpg', 'ico');

    $javascript = '';
    $javascript .= " onclick=\"loadSniff('[link]', '[file]', '[id]');\"";

    $jsFolder = '';
    $jsFolder .= " onmousedown=\"setPath('[link]/[file]');\"";
    $fileTree = new phpFileTree('', '', $javascript, $jsFolder);

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
            }//foreach

            if( ! $show)
            {
                continue;
            }

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
    }//foreach

    return $ret;
}//function
