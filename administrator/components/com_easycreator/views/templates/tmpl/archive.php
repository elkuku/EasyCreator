<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 28-Apr-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$exportTypes = JFolder::exists(ECRPATH_EXPORTS) ? JFolder::folders(ECRPATH_EXPORTS) : array();

echo '<h1>'.jgettext('Template Archive').'</h1>';

foreach($exportTypes as $exportType) :
    echo '<h4>'.jgettext($exportType).'</h4>';
    $exportFiles = JFolder::files(ECRPATH_EXPORTS.DS.$exportType, 'gz$');

    if(0 == count($exportFiles)) :
        EcrHtml::message(jgettext('Archive is empty'));

        continue;
    endif;

    $base_href = str_replace(JPATH_ROOT.DS, '', ECRPATH_EXPORTS.DS.$exportType);
    $base_href = str_replace(DS, '/', $base_href);
    $base_href = JURI::Root().$base_href;

    rsort($exportFiles);
    ?>

    <table class="table table-striped table-hover table-condensed">
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
        <tr id="row<?php echo $fileName; ?>"
            class="<?php echo 'row'.$k; ?>">
            <td>
                <a href="<?php echo $href; ?>">
                    <?php echo $fileName; ?>
                </a>
            </td>
            <td><?php echo $date->format('Y-m-d H:i:s'); ?></td>
            <td><?php echo EcrHtml::byte_convert($fsize); ?></td>
            <td width="2%">
                <a <?php echo $js_delete; ?> href="javascript:" style="padding-left: 20px; height: 14px;"
                   class="btn hasTip"
                   title="<?php echo jgettext('Delete').'::'.$fileName; ?>">
                    <i class="img icon16-delete"></i>
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
