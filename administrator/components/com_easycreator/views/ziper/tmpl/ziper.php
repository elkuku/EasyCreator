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
    EcrHtml::displayMessage(jgettext('Please add some extensions to your package before creating it'), 'error');

    return;
}

JFactory::getDocument()->addScriptDeclaration(
    "window.addEvent('domready', function() { EcrZiper.updateName('{$this->ecr_project}'); });");

?>
<div class="clr"></div>

<div id="zipResult" style="display: none;">

    <div id="ajaxMessage"></div>

    <div id="zipResultLinks"></div>

    <h3><?php echo jgettext('Log console'); ?></h3>
    <div id="pollStatus"></div>
    <pre id="ecrDebugBox"></pre>

</div>
<div style="clear: both;"></div>

<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('folder'); ?>
</div>
<div style="clear: both;"></div>
<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('format'); ?>
</div>

<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('options'); ?>
</div>

<!--

<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('database'); ?>
</div>
-->
<div class="ecr_floatbox" style="background-color: #ccff99;">
    <h3><?php echo jgettext('Create the package'); ?></h3>

    <div class="ecr_button"
         onclick="EcrZiper.createPackage();return false;document.id('ecr_ajax_loader').className='ecr_ajax_loader_big'; submitbutton('ziperzip');"
         style="margin: 1em; padding: 1em; text-align: center;">
        <div id="ecr_ajax_loader" class="img icon-32-ecr_archive"
             style="padding-bottom: 32px; margin-top: 1em; margin-bottom: 1em; margin-left: 3em;"></div>
        <h1>
            <?php echo sprintf(jgettext('Create %s'), $this->project->name); ?>
        </h1>
    </div>
</div>

<input type="hidden" name="old_task" value="<?php echo JRequest::getCmd('task'); ?>"/>
