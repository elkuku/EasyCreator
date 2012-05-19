<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die(';)');


echo '<h1>'.jgettext('Versions').'</h1>';

if( ! $this->sel_language)
{
    EcrHtml::message(jgettext('Please choose a language'));

    return;
}

if( ! count($this->versions))
{
    EcrHtml::message(jgettext('No versions found'), 'notice');

    return;
}

//-- Add css
ecrStylesheet('diff');

//-- Add Javascript
ecrScript('versions');

if($this->selected_version)
{
    $this->easyLanguage->displayVersion($this->selected_version, $this->sel_language);
}
?>
<table class="adminlist">
  <tr>
    <th><?php echo jgettext('Version'); ?></th>
    <th><?php echo jgettext('Size'); ?></th>
    <th><?php echo jgettext('Date'); ?></th>
    <th><?php echo jgettext('Actions'); ?></th>
  </tr>
  <?php
  $k = 0;
    foreach($this->versions as $version) :
    $selected =($version->revNo == $this->selected_version) ? '_selected' : '';
    ?>
  <tr class="row<?php echo $k; ?>">
    <td><?php echo $version->revNo; ?></td>
    <td><?php echo $version->size; ?></td>
    <td><?php echo $version->lastMod; ?></td>
    <td>
    <div class="ecr_button<?php echo $selected; ?>" onclick="showVersion('<?php echo $version->revNo; ?>');"><?php echo jgettext('Show'); ?></div>
    </td>
  </tr>
  <?php
    $k = 1 - $k;
    endforeach;
 ?>
</table>

<input type="hidden" name="selected_version" />
