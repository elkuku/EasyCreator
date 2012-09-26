<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 14-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$templateList = EcrProjectTemplateHelper::getTemplateList();

echo EcrHelp::help(jgettext('Select the templates to export'), EcrHelp::ALL, 'ecrBigInfo');
?>

<h1><?php echo jgettext('Export templates') ?></h1>

<div class="btn btn-success btn-large" onclick="submitbutton('do_export');">
    <i class="img icon16-ecr_save"></i>
    <?php echo jgettext('Export') ?>
</div>

<div style="clear: both;"></div>

<?php
foreach($templateList as $group => $templates):
    echo '<div class="ecr_floatbox">';
    echo '<h3 class="img12 icon12-'.$group.'">'.$group.'</h3>';

    foreach($templates as $template):
        echo '<input type="checkbox" id="'.$group.$template->folder.'"';
        echo ' name="exports['.$group.'][]" value="'.$template->folder.'" />';
        echo '<label class="inline" for="'.$group.$template->folder.'">'
            .$template->name.' ('.$template->folder.')'
            .'</label>'.BR;
    endforeach;

    echo '</div>';
endforeach;

echo BR;


?>
<div style="clear: both;"></div>

