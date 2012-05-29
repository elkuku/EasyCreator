<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 27-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

?>
<div class="infoHeader">
    <?php echo jgettext('Build actions'); ?>
</div>

<?php if(count($this->project->actions)) : ?>

<dl class="dl-horizontal">
    <?php foreach($this->project->actions as $action): ?>
    <dt><?php echo $action->trigger; ?></dt>
    <dd>
        <?php echo $action->type; ?>
        <?php if('script' == $action->type) echo '<br />'.$action->script; ?>
    </dd>
    <?php endforeach; ?>
</dl>

<?php else : ?>

<p>
    <?php echo jgettext('No build actions defined'); ?>
</p>

<?php endif;
