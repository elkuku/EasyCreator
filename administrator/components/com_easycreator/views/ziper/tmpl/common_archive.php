<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 16-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$path = $this->project->getZipPath();

echo '<h1>'.jgettext('Archive').'</h1>';

echo '<div class="img icon-16-server">'.$path.'</div>';

if( ! JFolder::exists($path))
{
    ecrHTML::displayMessage(jgettext('Archive is empty'));

    return;
}

if(0 === strpos($path, ECRPATH_BUILDS))
{
    $base_href = JURI::Root().'administrator/components/com_easycreator/builds/'.$this->project->comName;
}
else
{
    $base_href = 'file://'.$path;
}

$folders = JFolder::folders($path);

natcasesort($folders);

$folders = array_reverse($folders);

foreach($folders as $folder)
{
    echo '<div style="background-color: #B2CCE5; font-size: 1.3em; font-weight: bold; padding-left: 1em;">';
    echo $this->project->comName.' '.$folder;
    echo '</div>';

    $base_path = $path.DS.$folder;
    $files = JFolder::files($base_path.DS);

    if( ! count($files))
    {
        echo '<strong style="color: red;">'.jgettext('No ZIP files found').'</strong>';

        continue;
    }
?>
<div id="ajaxMessage"></div>
<div id="ajaxDebug"></div>
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

        foreach($files as $file) :
            $info = lstat($base_path.DS.$file);
            $href = $base_href.'/'.$folder.'/'.$file;

            $p = str_replace(JPATH_ROOT.DS, '', $base_path);
            $p = str_replace('\\', '/', $p);

            ?>
        <tr id="row<?php echo $file; ?>"
        class="<?php echo 'row'.$k; ?>">
            <td><?php echo $file; ?></td>
            <td><?php echo JFactory::getDate($info[9])->format('Y-M-d H:i:s'); ?></td>
            <td><?php echo ecrHTML::byte_convert($info[7]); ?></td>
            <td width="2%"><a href="<?php echo $href; ?>"
                style="padding-left: 20px; height: 14px;"
                class="ecr_button img icon-16-save hasEasyTip"
                title="<?php echo jgettext('Download'); ?>::"> </a></td>
            <td width="2%">
            <?php if(0 === strpos($path, ECRPATH_BUILDS)) : ?>
                <div style="padding-left: 20px; height: 14px;"
                    class="ecr_button img icon-16-delete hasEasyTip"
                    title="<?php echo jgettext('Delete'); ?>::"
                    onclick="deleteZipFile(<?php echo "'$p', '$file'"?>);">
                </div>
            <?php endif; ?>
            </td>
        </tr>
        <?php
        $k = 1 - $k;
        endforeach;
        ?>
    </tbody>
</table>
<?php
}//foreach
