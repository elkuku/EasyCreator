<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$projectCount = 0;

$projects = EcrProjectHelper::getProjectList();

$toolImgBase = '&lt;span class=\'img icon16-%s\' style=\'padding-left: 20px; height: 14px;\'&gt;&lt;/span&gt;';

$toolImg = new stdClass;
$toolImg->config = sprintf($toolImgBase, 'ecr_config');

$toolImg->language = sprintf($toolImgBase, 'locale');
$toolImg->codeeye = sprintf($toolImgBase, 'xeyes');
$toolImg->ziper = sprintf($toolImgBase, 'package');
$toolImg->deploy = sprintf($toolImgBase, 'deploy');
?>
<div class="projectListHeader registered"><?php echo jgettext('Registered Projects'); ?></div>
<?php
/* @var EcrProjectBase $pType */
foreach(EcrProjectHelper::getProjectTypes() as $pTag => $pType) :
    if( ! isset($projects[$pTag]) || ! count($projects[$pTag]))
        continue;
    ?>
<div class="ecr_floatbox">
    <?php
    $count = (isset($projects[$pTag])) ? count($projects[$pTag]) : 0;

    echo '<div class="boxHeader img icon12-'.$pTag.'">';
    echo sprintf($pType->translateTypeCount($count), $count);
    echo '</div>';

    foreach($projects[$pTag] as $project) :
        ?>
        <div class="projectListRow btn hasTip"
             title="<?php echo jgettext('Configure').$toolImg->config.'::'.$project->name; ?>"
             style="text-align: left; width: 90%"
             onclick="EasyCreator.project('<?php echo $project->fileName; ?>', 'stuffer');">
            <div class="btn-group" style="float: right; margin-left:5px;">
                <a class="btn btn-mini hasTip"
                   title="<?php echo jgettext('Languages').$toolImg->language.'::'.$project->name; ?>"
                onclick="EasyCreator.project('<?php echo $project->fileName; ?>', 'languages');">
                    <i class="img16 icon16-locale"></i>
                </a>
                <a class="btn btn-mini hasTip"
                   title="<?php echo jgettext('CodeEye').$toolImg->codeeye.'::'.$project->name; ?>"
                   onclick="EasyCreator.project('<?php echo $project->fileName; ?>', 'codeeye');">
                    <i class="img16 icon16-xeyes"></i>
                </a>
                <a class="btn btn-mini hasTip"
                   title="<?php echo jgettext('Package').$toolImg->ziper.'::'.$project->name; ?>"
                   onclick="EasyCreator.project('<?php echo $project->fileName; ?>', 'ziper');">
                    <i class="img16 icon16-package"></i>
                </a>
                <a class="btn btn-mini hasTip"
                   title="<?php echo jgettext('Deploy').$toolImg->deploy.'::'.$project->name; ?>"
                   onclick="EasyCreator.project('<?php echo $project->fileName; ?>', 'deploy');">
                    <i class="img16 icon16-deploy"></i>
                </a>
            </div>
            <strong><?php echo $project->name;?></strong>
        </div>
            <br />
        <?php
        $projectCount += count($projects[$pTag]);
    endforeach;
    ?>
</div>
<?php endforeach; ?>

<?php if($projectCount == 0) : ?>
<div class="ecr_noproject" style="color: orange; text-align: center;">
    <?php echo jgettext('None found'); ?>
</div>
<div
     class="btn btn-large btn-success" onclick="easySubmit('starter', 'starter');">
    <i class="img icon16-add"></i>
    <?php echo jgettext('Create your first project'); ?>
</div>
<div style="padding: 2em;"></div>
<?php endif; ?>
<div style="clear: both;"></div>
