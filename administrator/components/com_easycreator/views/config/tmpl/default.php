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
    <h1><span class="img32c icon-32-ecr_config"></span><?php echo sprintf(jgettext('%s Configuration'), 'EasyCreator'); ?></h1>
</div>

<div style="clear: both;"></div>

<?php
if( ! class_exists('Xg11n')) :

    echo '<div style="background-color: #ffc; border: 1px solid orange; padding: 0.5em;">'
    .'EasyCreator is in "English ONLY" mode !'
    .' If you want a localized version, please install the g11n library. - '
    .'<a href="http://joomlacode.org/gf/project/elkuku/frs/?action=FrsReleaseBrowse&frs_package_id=5915">Download lig_g11n</a>'
    .'</div>';
endif;

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
