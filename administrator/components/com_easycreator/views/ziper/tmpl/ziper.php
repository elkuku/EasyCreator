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
    "window.addEvent('domready', function() { EcrZiper.updateName(); });");

?>
<div class="ecr_floatbox buttonZip">
    <?php echo $this->loadTemplate('button'); ?>
</div>

<div class="ecr_floatbox">
    <div class="infoHeader img icon24-preset">
        <?php echo jgettext('Preset'); ?>
    </div>
    <?php echo EcrHtmlSelect::presets($this->project
    , array('onchange' => 'EcrZiper.loadPreset(this);', 'style' => 'font-size: 1.5em;')); ?>
</div>
<div class="clr"></div>
<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('actions'); ?>
</div>


<div id="zipResult" style="display: none;"></div>

<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('folder'); ?>
</div>

<div class="ecr_floatbox" style="min-width: 300px;">
    <?php echo $this->loadTemplate('filename'); ?>
</div>

<div style="xclear: both;"></div>

<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('options'); ?>
</div>

<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('easycreator'); ?>
</div>

<!--
<div class="ecr_floatbox">
    <?php //echo $this->loadTemplate('database'); ?>
</div>
-->


<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('logging'); ?>
</div>

<div class="clr" style="height: 75px;"></div>

<?php echo EcrHtmlDebug::logConsole();
