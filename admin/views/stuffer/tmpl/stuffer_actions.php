<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views.Stuffer
 * @author     Nikolai Plath
 * @author     Created on 23-Jun-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$buildEvents = array('precopy', 'postcopy', 'postbuild');

?>
<div class="ecr_floatbox">

    <div class="infoHeader img icon24-actions">
        <?php echo jgettext('Build Actions'); ?>
    </div>

    <div id="container_action">
        <?php foreach($buildEvents as $event) : ?>
        <strong style="color: blue;"><?php echo ucfirst($event); ?></strong>
        <ul id="container_actions_<?php echo $event; ?>"></ul>
        <?php endforeach; ?>
    </div>

    <hr/>

    <fieldset>

        <h4><?php echo jgettext('New Action'); ?></h4>
        <label class="inline">
            <?php echo jgettext('Action'); ?>
        </label>
        <?php echo EcrHtmlSelect::actions(array('class' => 'span7')); ?>


        <label class="inline" for="sel_event">
            <?php echo jgettext('Event'); ?>
        </label>
        <select class="span7" id="sel_event" name="event">
            <option value="precopy">Pre Copy</option>
            <option value="postcopy">Post Copy</option>
            <option value="postbuild">Post Build</option>
        </select>

        <div class="btn-toolbar">
            <div class="btn" onclick="Stuffer.newAction(this);">
                <i class="img icon16-add"></i>
                <?php echo jgettext('Add Action');?>
            </div>
        </div>

        <?php echo EcrHelp::info('${temp_dir}<br />${j_root}', jgettext('Available replacements')); ?>

    </fieldset>

</div>
