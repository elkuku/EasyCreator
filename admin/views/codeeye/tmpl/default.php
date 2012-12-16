<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 28-Sep-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */
?>

<div style="text-align: center;">
    <span id="ecr_title_file"></span>
</div>

<div id="ecr_codeeye_output" style="text-align: center; padding: 2em;">

    <div class="infoHeader img32" style="width: 50%; margin: auto; margin-bottom: 1em;">
        <span class="img32 icon32-nose"></span>
        &nbsp;
        <?php echo jgettext('First Things First'); ?>
    </div>

    <span class="btn" onclick="checkEnvironment();">
        <i class="img icon16-apply"></i>
        <?php echo jgettext('Check your environment'); ?>
    </span>
</div>

<pre id="ecr_codeeye_console"><?php echo jgettext('Console'); ?></pre>
