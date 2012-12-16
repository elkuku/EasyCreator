<?php
/**
* @package    EasyCreator
* @subpackage Views
* @author     Nikolai Plath
* @author     Created on 12-Aug-2011
* @license    GNU/GPL, see JROOT/LICENSE.php
*/

//-- No direct access
defined('_JEXEC') || die('=;)');
?>

<div class="ecr_floatbox">

<?php
foreach($this->project->copies as $dir):
    if(is_dir($dir)):
        $d = str_replace(JPATH_ROOT.DS, '', $dir);
        echo '<div onclick="phploc(\''.$d.'\');"'
        .' class="btn block" style="text-align: left;"><i class="img32a icon32-nose"></i>'.$d.'</div>';
    endif;
endforeach;

?>
</div>

<div style="clear: both;"></div>

<h2><?php echo jgettext('Output')?></h2>

<div id="ecr_codeeye_output" style="padding-top: 0.2em;"></div>

<pre id="ecr_codeeye_console"><?php echo jgettext('Console'); ?></pre>
