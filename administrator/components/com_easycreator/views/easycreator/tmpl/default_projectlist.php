<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

ecrStylesheet('qtabs');

switch(ECR_JVERSION)
{
    case '1.5':
        ecrScript('qtabs_m11');
    break;

    case '1.6':
        ecrScript('qtabs_m12');
    break;

    default:
        ecrHTML::displayMessage('Unsupported JVersion', 'error');
    break;
}//switch

$projectCount = 0;

$projects = EasyProjectHelper::getProjectList();
$projectTypes = EasyProjectHelper::getProjectTypes();

$toolImgBase = '&lt;span class=\'img icon-16-%s\' style=\'padding-left: 20px; height: 14px;\'&gt;&lt;/span&gt;';

$toolImg = new stdClass();
$toolImg->package = sprintf($toolImgBase, 'module');
$toolImg->config = sprintf($toolImgBase, 'config');
$toolImg->language = sprintf($toolImgBase, 'language');


?>
<script type="text/javascript">
<!--
window.addEvent('domready', function(){
  var opt = {
    scrolling: 'lr'
    , flexHeight: true
  };
  var t = new QTabs('ex1', opt);
})
//-->
</script>

<div class="projectListHeader registered"><?php echo jgettext('Registered Projects'); ?></div>

<div class="qtwrapper qtwrap-lft-blue1">
  <div class="qthead-lft-blue1">
    <ul class="qtabs" id="qtabs-ex1">
    <?php
    foreach($projectTypes as $comType => $titel) :
        $count =(isset($projects[$comType])) ? count($projects[$comType]) : 0;
        switch ($comType) :
            case 'library':
                $plural = 'Libraries';
            break;

            default:
                $plural = ucfirst($comType).'s';
            break;
        endswitch;

        echo '<li><span class="img icon-12-'.$comType.'"></span>';
        echo sprintf(jngettext('%d '.ucfirst($comType), '%d '.$plural, $count), $count);
        echo '</li>';
    endforeach;
    ?>
    </ul>
  </div>

  <div class="qtcurrent current-lft-blue1" id="current-ex1">
  	<?php
    foreach($projectTypes as $comType => $titel) :
        echo '<div class="qtcontent">';
        if(isset($projects[$comType])
        && count($projects[$comType])) :
            foreach($projects[$comType] as $project) :
            ?>
	    	<div class="projectListRow ecr_button hasEasyTip"
			title="<?php echo jgettext('Configure').$toolImg->config.'::'.$project->name; ?>"
			style="height: 28px; min-width: 200px;"
			onclick="configureProject('<?php echo $project->fileName; ?>');">
				<span class="img icon-12-<?php echo $comType; ?>"></span>
				<strong><?php echo $project->name;?></strong>
				<div style="float: right;">
					<a class="ecr_button img icon-16-language hasEasyTip"
						style="margin-left: 5px; padding-left: 20px; height: 14px;"
						title="<?php echo jgettext('Languages').$toolImg->language.'::'.$project->name; ?>"
						onclick="translateProject('<?php echo $project->fileName; ?>');">
					</a>
					<a class="ecr_button img icon-16-module hasEasyTip"
						style="margin-left: 5px; padding-left: 20px;; height: 14px;"
						title="<?php echo jgettext('Package').$toolImg->package.'::'.$project->name; ?>"
						onclick="packProject('<?php echo $project->fileName; ?>');">
					</a>
				</div>
			</div>
	        <?php
            $projectCount += count($projects[$comType]);
            endforeach;
        else:
            echo '<div class="ecr_noproject">'.jgettext('No projects found').'</div>';
        endif;

        echo '</div>';
    endforeach;
    ?>
  </div>
  <div style="clear: both"></div>
</div>
<?php if($projectCount == 0) : ?>
	<div class="ecr_noproject" style="color: orange; text-align: center;"><?php echo jgettext('None found'); ?></div>
	<div class="ecr_button img icon-16-add"
		onclick="easySubmit('starter', 'starter');"><?php echo jgettext('Create your first project'); ?>
	</div>
<?php endif;
