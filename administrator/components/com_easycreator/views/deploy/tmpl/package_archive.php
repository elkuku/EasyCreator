<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$path = $this->project->getZipPath();

$base_href = (0 === strpos($path, ECRPATH_BUILDS))
    ? JURI::Root().'administrator/components/com_easycreator/builds/'.$this->project->comName
    : 'file://'.$path;

echo '<div class="img icon16-server path">'.$path.'</div>';

if(false == JFolder::exists($path)) :
    EcrHtml::message(jgettext('Archive is empty'), 'warning');

    return;
endif;

$folders = JFolder::folders($path);

natcasesort($folders);

$folders = array_reverse($folders);
$i = 0;

foreach($folders as $folder) :
    echo '<div style="background-color: #B2CCE5; font-size: 1.3em; font-weight: bold; padding-left: 1em;">';
    echo $this->project->comName.' '.$folder;
    echo '</div>';

    $base_path = $path.DS.$folder;
    $files = JFolder::files($base_path.DS);

    if(0 == count($files)) :
        echo '<strong style="color: red;">'.jgettext('No ZIP files found').'</strong>';

        continue;
    endif;
    ?>
<div id="ajaxMessage"></div>
<div id="ajaxDebug"></div>
<table class="adminlist">
    <tbody>
    <tr style="background-color: #eee;">
        <th align="center" width="5%"><?php echo jgettext('Deploy'); ?></th>
        <th><?php echo jgettext('File'); ?></th>
        <th width="10%"><?php echo jgettext('Modified'); ?></th>
        <th width="10%"><?php echo jgettext('Size'); ?></th>
        <?php if(0 === strpos($path, ECRPATH_BUILDS)) : ?>
        <?php endif; ?>
    </tr>
        <?php
        $k = 0;

        foreach($files as $file) :
            $info = lstat($base_path.DS.$file);
            $href = $base_href.'/'.$folder.'/'.$file;

            $p = str_replace(JPATH_ROOT.DS, '', $base_path);
            $p = str_replace('\\', '/', $p);

            ?>
        <tr id="row<?php echo $file; ?>"
            class="<?php echo 'row'.$k; ?>">
            <td>
                <input type="checkbox" name="file[]" id="file_<?php echo $i; ?>"
                       value="<?php echo $base_path.DS.$file; ?>">
            </td>
            <td><label for="file_<?php echo $i; ?>"><?php echo $file; ?></label></td>
            <td nowrap="nowrap"><?php echo JFactory::getDate($info[9])->format('Y-M-d H:i:s'); ?></td>
            <td><?php echo EcrHtml::byte_convert($info[7]); ?></td>
        </tr>
            <?php
            $k = 1 - $k;
        $i ++;
        endforeach;
        ?>
    </tbody>
</table>
<?php
endforeach;
