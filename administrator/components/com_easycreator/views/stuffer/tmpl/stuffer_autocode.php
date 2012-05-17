<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author		Created on 21-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');
/* @var EasyCreatorViewStuffer $this */
?>

<div class="ecr_floatbox">
    <div class="infoHeader img icon24-easycreator">AutoCode</div>
    <h4><?php echo jgettext('List postfix') ?></h4>
    <?php echo $this->project->listPostfix ?>
    <h4><?php echo jgettext('File header template') ?></h4>

    <?php echo EcrHtmlOptions::header($this->project->headerType) ?>
</div>
