<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$projectCount = 0;

$projects = EasyProjectHelper::getProjectList();
$projectTypes = EasyProjectHelper::getProjectTypes();

$toolImgBase = '&lt;span class=\'img icon-16-%s\' style=\'padding-left: 20px; height: 14px;\'&gt;&lt;/span&gt;';

$toolImg = new stdClass;
$toolImg->package = sprintf($toolImgBase, 'module');
$toolImg->config = sprintf($toolImgBase, 'config');
$toolImg->language = sprintf($toolImgBase, 'language');
?>
<div class="projectListHeader registered"><?php echo jgettext('Registered Projects'); ?></div>
<?php
foreach($projectTypes as $comType => $titel) :
    if( ! isset($projects[$comType])
    || ! count($projects[$comType]))
    continue;
?>
<div class="ecr_floatbox">
<?php

    switch ($comType) :
        case 'library':
            $plural = 'Libraries';
        break;

        default:
            $plural = ucfirst($comType).'s';
        break;
    endswitch;

    $count =(isset($projects[$comType])) ? count($projects[$comType]) : 0;

    echo '<div class="boxHeader img icon-12-'.$comType.'">';
    echo sprintf(jngettext('%d '.ucfirst($comType), '%d '.$plural, $count), $count);
    echo '</div>';

    foreach($projects[$comType] as $project) :
    ?>
	<div class="projectListRow ecr_button hasEasyTip"
	title="<?php echo jgettext('Configure').$toolImg->config.'::'.$project->name; ?>"
	style="height: 20px; min-width: 200px; text-align: left; margin-top: 0.3em; margin-bottom: 0.3em;"
	onclick="configureProject('<?php echo $project->fileName; ?>');">
		<div style="float: left;">
			<a class="ecr_button img icon-16-language hasEasyTip"
				style="margin-right: 5px; padding-left: 20px; height: 14px;"
				title="<?php echo jgettext('Languages').$toolImg->language.'::'.$project->name; ?>"
				onclick="translateProject('<?php echo $project->fileName; ?>');">
			</a>
			<a class="ecr_button img icon-16-module hasEasyTip"
				style="margin-right: 5px; padding-left: 20px;; height: 14px;"
				title="<?php echo jgettext('Package').$toolImg->package.'::'.$project->name; ?>"
				onclick="packProject('<?php echo $project->fileName; ?>');">
			</a>
		</div>
		<strong><?php echo $project->name;?></strong>
	</div>
    <?php
    $projectCount += count($projects[$comType]);
    endforeach;
?>
</div>
<?php endforeach; ?>

<?php if($projectCount == 0) : ?>
	<div class="ecr_noproject" style="color: orange; text-align: center;">
	    <?php echo jgettext('None found'); ?>
	</div>
	<div style="padding: 1em; font-size: 1.3em; font-weight: bold;"
	class="ecr_button img icon-16-add" onclick="easySubmit('starter', 'starter');">
		<?php echo jgettext('Create your first project'); ?>
	</div>
	<div style="padding: 2em;"></div>
<?php endif; ?>
<div style="clear: both;"></div>