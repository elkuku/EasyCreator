<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 28-Apr-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$exportTypes = JFolder::folders(ECRPATH_EXPORTS);

echo '<h2>'.jgettext('Exports').'</h2>';

foreach($exportTypes as $exportType) :
    echo '<h3>'.jgettext($exportType).'</h3>';
    $exportFiles = JFolder::files(ECRPATH_EXPORTS.DS.$exportType, 'gz$');

    if(! count($exportFiles)) :
        EcrHtml::displayMessage(jgettext('Archive is empty'));

        continue;
    endif;

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
        <th><?php echo jgettext('Action'); ?></th>
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
            <td><a href="<?php echo $href; ?>"><?php echo $fileName; ?></a></td>
            <td><?php echo $date->toFormat(); ?></td>
            <td><?php echo EcrHtml::byte_convert($fsize); ?></td>
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

endforeach;
