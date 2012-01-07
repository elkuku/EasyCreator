<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
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
    <h1>
        <span class="img32c icon-32-ecr_config"></span>
        <?php echo sprintf(jgettext('%s Configuration'), 'EasyCreator'); ?>
    </h1>
</div>

<div style="clear: both;"></div>

<?php if( ! class_exists('g11n')) : ?>
    <div style="background-color: #ffc; border: 1px solid orange; padding: 0.5em;">
        EasyCreator is in "English ONLY" mode ! If you want a localized version, please install the g11n library. -
        <a href="http://joomlacode.org/gf/project/elkuku/frs/?action=FrsReleaseBrowse&frs_package_id=5915">
            Download lig_g11n
        </a>
    </div>
<?php endif; ?>

<?php
foreach(array_keys($groups) as $group):
    if('Debug' == $group
    && ! ECR_DEV_MODE)
    continue;

    $style = str_replace(' ', '_', strtolower($group));
    ?>
    <div class="ecr_floatbox">
		<div class="imgbar icon-24-<?php echo $style; ?>"></div>

    	<div class="table_name"><?php echo jgettext($group); ?></div>
    	<?php echo $this->parameters->render('params', $group); ?>
        </div>
<?php endforeach; ?>

<div style="clear: both;"></div>

<?php
