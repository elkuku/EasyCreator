<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 07-Aug-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$path = $this->project->getZipPath();

$base_href = (0 === strpos($path, ECRPATH_BUILDS))
    ? JURI::Root().'administrator/components/com_easycreator/data/builds/'.$this->project->comName
    : 'file://'.$path;

echo '<div class="img icon16-server path">'.JPath::clean($path).'</div>';

if( ! JFolder::exists($path)) :
    EcrHtml::message(jgettext('Archive is empty'), 'warning');

    return;
endif;

$folders = JFolder::folders($path);

natcasesort($folders);

$folders = array_reverse($folders);

foreach($folders as $folder) :
    echo '<div style="background-color: #B2CCE5; font-size: 1.3em; font-weight: bold; padding-left: 1em;">';
    echo $this->project->comName.' '.$folder;
    echo '</div>';

    $base_path = $path.DS.$folder;
    $files = JFolder::files($base_path.DS);

    $files = array_reverse($files);

    if( ! count($files)) :
        echo '<strong style="color: red;">'.jgettext('No ZIP files found').'</strong>';

        continue;
    endif;
    ?>
<div id="ajaxMessage"></div>
<div id="ajaxDebug"></div>
<table class="table table-striped table-bordered table-condensed">
    <thead>
    <tr style="background-color: #eee;">
        <th><?php echo jgettext('File'); ?></th>
        <th width="10%"><?php echo jgettext('Modified'); ?></th>
        <th width="10%"><?php echo jgettext('Size'); ?></th>
        <?php if(0 === strpos($path, ECRPATH_BUILDS)) : ?>
        <th align="center"><?php echo jgettext('Action'); ?></th>
        <?php endif; ?>
    </tr>
    </thead>
    <tbody>
        <?php
        foreach($files as $file) :
            $info = lstat($base_path.DS.$file);
            $href = $base_href.'/'.$folder.'/'.$file;

            $p = str_replace(JPATH_ROOT.DS, '', $base_path);
            $p = str_replace('\\', '/', $p);

            ?>
        <tr id="row<?php echo $file; ?>">
            <td><a href="<?php echo $href; ?>"><?php echo $file; ?></a></td>
            <td nowrap="nowrap"><?php echo JFactory::getDate($info[9])->format('Y-M-d H:i:s'); ?></td>
            <td><?php echo EcrHtml::byte_convert($info[7]); ?></td>
            <?php if(0 === strpos($path, ECRPATH_BUILDS)) : ?>
            <td width="2%">
                <div class="btn btn-mini hasTip" title="<?php echo jgettext('Delete'); ?>::"
                     onclick="EcrZiper.deleteZipFile(<?php echo "'$p', '$file'"?>);">
                    <i class="img icon16-delete"></i>
                </div>
            </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php endforeach;
