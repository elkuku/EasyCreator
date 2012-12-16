<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 09-Apr-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$matrix = $this->matrix;
?>

<table>
    <tr>
        <th style="background-color: #cce5ff;"><?php echo jgettext('Type'); ?></th>
        <th style="background-color: #ffffb2;" colspan="2"><?php echo jgettext('Files'); ?></th>
        <th style="background-color: #e5ff33;" colspan="2"><?php echo jgettext('Size'); ?></th>
        <th style="background-color: #e5ff99;" colspan="2"><?php echo jgettext('Lines'); ?></th>
        <th style="background-color: #e5ff55;" colspan="2"><?php echo jgettext('Code'); ?></th>
        <th style="background-color: #e5ff55;" colspan="2"><?php echo jgettext('Comments'); ?></th>
        <th style="background-color: #e5ff55;" colspan="2"><?php echo jgettext('Blanks'); ?></th>
    </tr>

<?php foreach($matrix->getProjectExtensions() as $ext) : ?>
    <tr>
        <th style="background-color: #CCE5FF;">
            <?php echo jgettext($ext); ?>
        </th>
        <td class="preCell" style="background-color: #ffffb2;">
            <?php echo number_format($matrix->projectData[$ext]['files'], 0, '', '.'); ?>
        </td>
        <td class="preCell" style="background-color: #ffffb2;">
            (<?php echo number_format($matrix->projectData[$ext]['perc_files'], 2); ?>%)
        </td>
        <td class="preCell" style="background-color: #e5ff33;">
            <?php echo EcrHtml::byte_convert($matrix->projectData[$ext]['size']); ?>
        </td>
        <td class="preCell" style="background-color: #e5ff33;">
            (<?php echo number_format($matrix->projectData[$ext]['perc_size'], 2); ?>%)
        </td>
        <td class="preCell" style="background-color: #e5ff99;">
            <?php echo number_format($matrix->projectData[$ext]['lines'], 0, '', '.'); ?>
        </td>
        <td class="preCell" style="background-color: #e5ff99;">
            (<?php echo number_format($matrix->projectData[$ext]['perc_lines'], 2); ?>%)
        </td>
        <td class="preCell" style="background-color: #e5ff55;">
            <?php echo number_format($matrix->projectData[$ext]['ratioCode']); ?>
        </td>
        <td class="preCell" style="background-color: #e5ff55;">
        <?php if(isset($matrix->projectData[$ext]['perc_ratio_code'])) : ?>
            (<?php echo number_format($matrix->projectData[$ext]['perc_ratio_code'], 2); ?>%)
            <?php endif; ?>
        </td>
        <td class="preCell" style="background-color: #e5ff55;">
            <?php echo number_format($matrix->projectData[$ext]['ratioComments']); ?>
        </td>
        <td class="preCell" style="background-color: #e5ff55;">
        <?php if(isset($matrix->projectData[$ext]['perc_ratio_comments'])) : ?>
            (<?php echo number_format($matrix->projectData[$ext]['perc_ratio_comments'], 2); ?>%)
            <?php endif; ?>
        </td>
        <td class="preCell" style="background-color: #e5ff55;">
            <?php echo number_format($matrix->projectData[$ext]['ratioBlanks']); ?>
        </td>
        <td class="preCell" style="background-color: #e5ff55;">
        <?php if(isset($matrix->projectData[$ext]['perc_ratio_blanks'])) : ?>
            (<?php echo number_format($matrix->projectData[$ext]['perc_ratio_blanks'], 2); ?>%)
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
    <tr>
        <th style="background-color: #cce5ff;">
            <?php echo jgettext('Images'); ?>
        </th>
        <td class="preCell" style="background-color: #ffffb2;">
            <?php echo number_format($matrix->projectData['images']['files']); ?>
        </td>
        <td class="preCell" style="background-color: #ffffb2;">
             (<?php echo number_format($matrix->projectData['images']['perc_files'], 2); ?>%)
        </td>
        <td class="preCell" style="background-color: #e5ff33;">
            <?php echo EcrHtml::byte_convert($matrix->projectData['images']['size']); ?>
        </td>
        <td class="preCell" style="background-color: #e5ff33;">
             (<?php echo number_format($matrix->projectData['images']['perc_size'], 2); ?>%)
        </td>
        <td colspan="8">&nbsp;</td>
    </tr>

    <tr style="font-size: 1.4em;">
        <th><?php echo jgettext('TOTAL'); ?></th>
        <th class="preCell" style="background-color: #ffffb2;">
            <?php echo number_format($matrix->totalFiles); ?>
        </th>
        <td>&nbsp;</td>
        <th class="preCell" style="background-color: #e5ff33;">
            <?php echo EcrHtml::byte_convert($matrix->totalSize); ?>
        </th>
        <td>&nbsp;</td>
        <th class="preCell" style="background-color: #e5ff99;">
            <?php echo number_format($matrix->totalLines); ?>
        </th>
        <td colspan="7">&nbsp;</td>
    </tr>

</table>
