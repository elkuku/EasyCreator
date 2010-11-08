<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author		Nikolai Plath {@link http://www.nik-it.de}
 * @author		Created on 28-Sep-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');
?>

<div style="text-align: center;">
    <span id="ecr_title_file"></span>
</div>

<div id="ecr_codeeye_output">
    <div style="text-align: center; padding-bottom: 2em;">
        <h1><?php echo jgettext('First Things First'); ?></h1>
        <span class="img icon-16-apply ecr_button" onclick="checkEnvironment();">
            <?php echo jgettext('Check your environment'); ?>
        </span>
    </div>
</div>

<pre id="ecr_codeeye_console"><?php echo jgettext('Console'); ?></pre>
