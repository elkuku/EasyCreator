<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author		Nikolai Plath {@link http://www.nik-it.de}
 * @author		Created on 14-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$templateList = EasyTemplateHelper::getTemplateList();
?>
<div style="height: 1em;"></div>

<?php
ecrHTML::floatBoxStart();

echo '<h2>'.jgettext('Export templates').'</h2>';
echo '<div class="ecrBigInfo">'.jgettext('Select the templates to export').'</div>';
foreach($templateList as $group => $templates):
    echo '<h3 class="img12 icon-12-'.$group.'">'.$group.'</h3>';

    foreach($templates as $template):
        echo '<input type="checkbox" id="'.$group.$template->folder.'"';
        echo ' name="exports['.$group.'][]" value="'.$template->folder.'" />';
        echo '<label for="'.$group.$template->folder.'">'.$template->name.' ('.$template->folder.')</label>'.BR;
    endforeach;

endforeach;

echo BR;
echo '<div class="ecr_button img icon-16-save" onclick="submitbutton(\'do_export\');">'.jgettext('Export').'</div>';

ecrHTML::floatBoxEnd();
?>

<?php
$exportTypes = JFolder::folders(ECRPATH_EXPORTS);

ecrHTML::floatBoxStart();
echo '<h2>'.jgettext('Exports').'</h2>';

foreach($exportTypes as $exportType) :
    echo '<h3>'.jgettext($exportType).'</h3>';
    $exportFiles = JFolder::files(ECRPATH_EXPORTS.DS.$exportType, 'gz$');

    if( ! count($exportFiles)) :
        ecrHTML::displayMessage(jgettext('Archive is empty'));
    else:

        $base_href = str_replace(JPATH_ROOT.DS, '', ECRPATH_EXPORTS.DS.$exportType);
        $base_href = str_replace(DS, '/', $base_href);
        $base_href = JURI::Root().$base_href;

        rsort($exportFiles);
?>
<table class="adminlist" cellspacing="5">
    <tbody>
        <tr style="background-color: #eee;">
            <th><?php echo jgettext('File'); ?></th>
            <th><?php echo jgettext('Modified'); ?></th>
            <th><?php echo jgettext('Size'); ?></th>
            <th colspan="2" align="center"><?php echo jgettext('Action'); ?></th>
        </tr>
        <?php
        $k = 0;

        foreach($exportFiles as $fileName) :
            $info = lstat(ECRPATH_EXPORTS.DS.$exportType.DS.$fileName);
            $date = JFactory::getDate($info[9]);
            $href = $base_href.'/'.$fileName;
            $fsize = $info[7];

            $js_delete = '';
            $js_delete .= " document.adminForm.file_name.value='".$fileName."';";

            $js_delete .= " document.adminForm.file_path.value='"
            .str_replace(JPATH_ROOT.DS, '', ECRPATH_EXPORTS.DS.$exportType)."';";

            $js_delete .= " submitbutton('delete');";

            $js_delete .= ' onclick="'.$js_delete.'"';

            ?>
            <tr id="row<?php echo $fileName; ?>" class="<?php echo 'row'.$k; ?>">
                <td><?php echo $fileName; ?></td>
                <td><?php echo $date->toFormat(); ?></td>
                <td><?php echo ecrHTML::byte_convert($fsize); ?></td>
                <td width="2%">
                    <a href="<?php echo $href; ?>"
                    style="padding-left: 20px;"
                    class="ecr_button img icon-16-save hasEasyTip"
                    title="<?php echo jgettext('Download').'::'.$fileName; ?>">
                    </a>
                </td>
                <td width="2%">
                    <a href="javascript:" style="padding-left: 20px; height: 14px;"
                    class="ecr_button img icon-16-delete hasEasyTip"
                    title="<?php echo jgettext('Delete').'::'.$fileName; ?>" <?php echo $js_delete; ?>>
                    </a>
                </td>
            </tr>
            <?php
            $k = 1 - $k;
        endforeach;
        ?>
    </tbody>
</table>
<?php
    endif;
endforeach;

ecrHTML::floatBoxEnd();
?>

<div style="clear: both;"></div>
<?php
