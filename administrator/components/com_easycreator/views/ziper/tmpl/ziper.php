<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

ecrScript('util', 'pollrequest');

if('package' == $this->project->type
    && ! $this->project->elements
)
{
    EcrHtml::message(jgettext('Please add some extensions to your package before creating it'), 'error');

    return;
}

JFactory::getDocument()->addScriptDeclaration(
    "window.addEvent('domready', function() { EcrZiper.updateName('{$this->ecr_project}'); });");

?>
<div class="ecr_floatbox" style="float: right;">
    <a href="javascript:;" class="btn btn-success btn-large" onclick="EcrZiper.createPackage();"
       style="margin: 1em; padding: 1em;">
        <i class="img32 icon32-ecr_package"></i>
        <br/>
        <br/>
        <?php echo sprintf(jgettext('Create %s'), $this->project->name); ?>
    </a>

    <div id="ajaxMessage"></div>

    <div id="zipResultLinks"></div>

</div>
<div class="xclr"></div>

<div id="zipResult" style="display: none;"></div>

<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('folder'); ?>
</div>
<div style="clear: both;"></div>
<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('format'); ?>
</div>

<div class="ecr_floatbox" style="min-width: 300px;">
    <?php echo $this->loadTemplate('options'); ?>
</div>

<!--
<div class="ecr_floatbox">
    <?php //echo $this->loadTemplate('database'); ?>
</div>
-->

<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('actions'); ?>
</div>

<div class="clr" style="height: 75px;"></div>

<?php echo EcrHtmlDebug::logConsole();
