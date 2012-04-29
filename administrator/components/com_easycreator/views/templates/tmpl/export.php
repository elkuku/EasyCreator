<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 14-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$templateList = EcrProjectTemplateHelper::getTemplateList();
?>

<div style="height: 1em;"></div>
<div class="ecr_floatbox">
<?php

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

?>
</div>
<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('archive'); ?>
</div>
<div style="clear: both;"></div>
<?php
