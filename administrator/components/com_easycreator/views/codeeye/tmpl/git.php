<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 28-Sep-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

?>
<div class="ecr_floatbox">

<div onclick="gitStatus();" class="ecr_button img32 icon32-nose"><?php echo jgettext('git status')?></div>
</div>
<div style="clear: both;"></div>
<div id="ecr_codeeye_output" style="padding-top: 0.2em;"><h2><?php echo jgettext('Output')?></h2></div>
<pre id="ecr_codeeye_console"><?php echo jgettext('Console'); ?></pre>
