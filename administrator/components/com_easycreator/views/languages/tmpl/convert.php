<?php
/**
 * @package
 * @subpackage
 * @author     Nikolai Plath
 * @author     Created on 22.04.2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

ecrStylesheet('diff', 'languages');

$fixForJVersion = '1.6';
$languageTags = $this->converter->getKnownLanguageTags();

$selected_file = JFactory::getApplication()->input->getPath('selected_file');

$allPaths = $this->project->getLanguagePaths();
$paths = (array)$allPaths[$this->scope];

echo $this->menuBoxes['file'];

$color =(count($this->badDefinitions)) ? 'yellow' : 'green';
echo '<div class="ecr_menu_box" style="margin-left: 0.3em; background-color: '.$color.';">';
printf('Found <b>%d</b> errors in project', count($this->badDefinitions));
echo '</div>';

echo $this->menuBox('php_show_all', jgettext('Show all PHP code'));
echo $this->menuBox('convert_quotes', jgettext('Convert quotes'));

echo '<div style="padding-top: 1em; padding-bottom: 1em;">';

echo $this->menuBox('convert_forbidden', jgettext('Convert forbidden keys'));
echo $this->menuBox('remove_bads', jgettext('Remove bad charcters'));
echo $this->menuBox('convert_white_space', jgettext('Convert white space'));
echo $this->menuBox('add_prefix', jgettext('Add prefix'));
echo $this->menuBox('convert_all_caps', jgettext('Convert to all caps'));
?>
<div class="ecrSpecialLangConvert"><?php
echo $this->menuBox('convert_case_code', jgettext('Convert to language file keys to the case of your code'));
?></div>
<?php
echo '<br />';
echo '<br />';

if($selected_file
&& isset($this->fileList[JPATH_ROOT.DS.$selected_file])
&& $this->fileList[JPATH_ROOT.DS.$selected_file])
{
    echo '<div class="ecr_menu_box" style="margin-left: 0.3em; background-color: yellow;">';
    echo sprintf('Found <b>%d</b> errors in %s', $this->fileList[JPATH_ROOT.DS.$selected_file], $selected_file);
    echo '</div>';
}

if($this->diff)
{
    echo '<span style="margin-left: 0.3em;" onclick="submitform(\'do_convert\');"'
    .' class="ecr_button img icon16-rename">';
    echo jgettext('Convert code and language file').'</span>';
}
else
{
    echo '<span style="margin-left: 0.3em;" onclick="submitform(\'do_convert\');"'
    .' class="ecr_button img icon16-rename">';
    echo jgettext('Convert language file').'</span>';
    echo '</div>';
}

echo $this->menuBoxes['file_errors'];

if($this->diff)
{
    echo '</div>';
    ?>
<h1>PHP</h1>
<table class="diff" width="100%"
	style="border: 1px solid gray; background-color: #fff; font-size: 10px;">
	<tr>
		<th colspan="2" width="50%" style="background-color: #dfd;"><?php echo jgettext('Original'); ?></th>
		<th colspan="2" width="50%" style="background-color: #ffc;">
		    <?php echo sprintf(jgettext('Fixed for %s'), $fixForJVersion); ?>
		</th>
	</tr>
	<?php echo $this->diff; ?>
</table>

	<?php
}

?>
<h1>INI</h1>
<?php
$headersButtons = '';

$languageFilesFound = false;

$buttons = '';
$tables = '';

foreach($languageTags as $tag) :
$sTag = substr($tag, 0, 2);
$fileContents = '';

foreach($paths as $p)
{
    if($this->project->langFormat != 'ini')
    {
        //-- Special gg11n Language
        $addPath = $tag.'/'.$tag.'.'.$this->project->getLanguageFileName($this->scope);
    }
    else
    {
        $addPath = 'language/'.$tag.'/'.$tag.'.'.$this->project->getLanguageFileName($this->scope);
    }

    $fileName = JPath::clean($p.'/'.$addPath);




    //    $path = $p.DS.'language'.DS.$tag;
    //
    //    $fileName = $tag.'.'.$this->project->getLanguageFileName($this->scope);

    if(JFile::exists($fileName)) :
    $fileContents = JFile::read($fileName);
    $languageFilesFound = true;
    break;
    else :
    continue;
    endif;
}//foreach

$lines = explode("\n", $fileContents);

$newLines = $this->converter->cleanLines($lines);
$newLines = $this->converter->cleanLangFileErrors($newLines, array_keys($this->fileErrors));

$newFileContents = implode("\n", $newLines);

$buttons .= '
<div class="ecr_button img icon16-add" style="float: left;" onclick="langfile_'.$sTag.'.toggle();">'.$tag.'</div>';

$tables .= '
<div id="langfile_'.$sTag.'" style="float: left">

<table cellpadding="0" cellspacing="0"
    style="background-color: #fff; border: 1px solid gray;">
    <tr>
    <th style="border-bottom: 1px solid gray;" colspan="4">'
        .sprintf(jgettext('Language file: %s'), $tag)
        .'</th>
    </tr>
    <tr>
        <th width="50%" style="border-bottom: 1px solid gray; border-right: 1px solid gray;" colspan="2">'
        .jgettext('Original')
        .'</th>
        <th width="50%" style="border-bottom: 1px solid gray; background-color: #ffc;" colspan="1">'
        .sprintf(jgettext('Fixed for %s'), $fixForJVersion)
        .'</th>
    </tr>';

        for($i = 0; $i < count($lines); $i++) :
        $origParts = $this->converter->splitLine($lines[$i]);
        $newParts = $this->converter->splitLine($newLines[$i]);

        $tables .= '<tr>';
        $tables .= '<td align="right" style="border-bottom: 1px solid gray; background-color: #eee;">'.$i.'</td>';
        $bgColor = '';
        $line = $lines[$i];

        if($origParts[1] != '') :
        if($origParts[0] == '') :
        //-- Comment
        $bgColor = ' background-color: #cff;';
        else :
        $color =($newParts[0] != $origParts[0]) ? 'red' : 'blue';

        //-- key = value
        $line = '<span style="color: '.$color.';">'.$origParts[0].'</span>';
        $line .= ' = <span style="color: green;">'.htmlspecialchars($origParts[1]).'</span>';
        endif;
        endif;

        $color =($lines[$i] != $newLines[$i]) ? ' background-color: #ddffdd;' : $bgColor;
        $tables .= '<td style="border-bottom: 1px solid gray; border-right: 1px solid gray;'.$color.'">';
        $tables .= $line;
        $tables .= '</td>';

        $bgColor = '';
        $line = $newLines[$i];

        if($newParts[1] != '') :
        if($newParts[0] == '') :
        //-- Comment
        $bgColor = ' background-color: #cff;';
        else :
        $color =($newParts[0] != $origParts[0]) ? 'red' : 'blue';

        //-- key value
        $line = '<span style="color: '.$color.';">'.$newParts[0].'</span>'
        .' = <span style="color: green;">'.htmlspecialchars($newParts[1]).'</span>';
        endif;
        endif;

        $color =($lines[$i] != $newLines[$i]) ? ' background-color: #ffc;' : $bgColor;
        $tables .= '<td style="border-bottom: 1px solid gray; border-right: 1px solid gray;'.$color.'">';
        $tables .= $line;
        $tables .= '</tr>';
        endfor;
        $tables .= '
</table>
</div>
<script type="text/javascript">
	var langfile_'.$sTag.' = new Fx.Slide(\'langfile_'.$sTag.'\');
	langfile_'.$sTag.'.hide();
</script>';

        endforeach;

        echo $buttons;
        echo '<div style="clear: both"></div>';
        echo $tables;

        if( ! $languageFilesFound)
        {
            EcrHtml::message(jgettext('No language files found in selected scope'), 'notice');
        }
