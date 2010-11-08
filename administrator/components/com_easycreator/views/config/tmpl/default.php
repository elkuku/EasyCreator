<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 12-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//--No direct access
defined('_JEXEC') || die('=;)');

$groups = $this->parameters->getGroups();
$blacks = array('_default', 'Personal');

?>
<div class="ecr_easy_toolbar" style="float: right;">
    <ul>
        <li>
            <a href="javascript:;" onclick="submitform('save_config');">
                <span class="icon-32-save" title="<?php echo jgettext('Save'); ?>"></span>
                <?php echo jgettext('Save'); ?>
            </a>
        </li>
    </ul>
</div>

<div align="center">
    <strong style="font-size: 1.5em;">EasyCreator</strong>
    <h1><?php echo jgettext('Configuration'); ?></h1>
</div>

<div style="clear: both;"></div>

<?php
foreach(array_keys($groups) as $group):
    $style = str_replace(' ', '_', strtolower($group));
    ecrHTML::floatBoxStart();
    ?>
		<div class="imgbar icon-24-<?php echo $style; ?>"></div>

    	<div class="table_name"><?php echo jgettext($group); ?></div>
        <?php
        echo formatMy16Params($this->parameters, $group);
    ecrHTML::floatBoxEnd();
endforeach;
?>

<div style="clear: both;"></div>

<?php
function formatMy16Params($parameters, $groupName)
{
    $html = $parameters->render('params', $groupName);
    $html = explode(PHP_EOL, $html);

    for($i = 0; $i < count($html) - 1; $i++)
    {
        if(strpos($html[$i], '<label') === 0)
        {
            $html[$i] = BR.BR.$html[$i];
            continue;
        }
    }//for

    return implode("\n", $html);
}//function
