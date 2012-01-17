<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */


//-- No direct access
defined('_JEXEC') || die('=;)');

$projectTypes = EcrProjectHelper::getProjectTypes();
$projectScopes = EcrProjectHelper::getProjectScopes();

$task = JRequest::getCmd('task');
$showCore =(JRequest::getCmd('show_core') == 'show_core') ? true : false;
$checked =($showCore) ? ' checked="checked"' : '';
?>
<div class="projectListHeader unregistered">
	<div style="float: right;">
		<input type="checkbox" name="show_core"
			id="show_core" value="show_core" <?php echo $checked; ?>
			onchange="submitbutton('<?php echo $task; ?>');" />
		<label for="show_core" class="img icon-16-joomla"> <?php echo jgettext('Show Joomla core projects'); ?>
		</label>
    </div>
	<?php echo jgettext('Unregistered Projects'); ?>
</div>
<?php
foreach($projectScopes as $comType => $projectScope)
{
    $scopes = explode(',', $projectScope);

    ?>
    <div class="ecr_floatbox">
    <?php

    echo '<div class="boxHeader img icon-12-'.$comType.'" style=" min-width: 150px;">';
    echo $projectTypes[$comType];
    echo '</div>';

    foreach($scopes as $scope)
    {
        $unregisteredProjects = EcrProjectHelper::getUnregisteredProjects($comType, $scope, $showCore);

        if($comType != 'component' && $scope)
        {
            ?>
			<div style="background-color: #F2F2F2; color: green; padding: 0.3em; font-weight: bold;">
	            <?php echo ucfirst($scope); ?>
			</div>
	        <?php
        }

        if( ! count($unregisteredProjects))
        {
            ?>
            <div style="color: orange; text-align: center;">
                <?php echo jgettext('None found'); ?>
        	</div>
        	<?php
            continue;
        }

        $k = 0;

        foreach($unregisteredProjects as $project)
        {
            ?>
    		<div class="ecr_button img icon-16-install hasEasyTip"
    			style="padding-left: 20px;; height: 14px; margin-top: 0.3em; margin-bottom: 0.3em;"
    			title="<?php
                echo jgettext('Register').'&lt;span class=\'img icon-16-install\''
                .' style=\'padding-left: 20px; height: 14px;\'&gt;&lt;/span&gt;::'
                .jgettext(ucfirst($comType)).' - '.$project; ?>"
    			onclick="registerProject(<?php echo "'$comType', '$project', '$scope'"; ?>);">
    		<strong><?php echo $project; ?></strong>
        	</div>
        	<?php
            $k = 1 - $k;
        }//foreach
    }//foreach
?>
</div>
<?php
}//foreach
?>
<div style="clear: both;"></div>

<input type="hidden" name="ecr_project_type" value="" />
<input type="hidden" name="ecr_project_name" value="" />
<input type="hidden" name="ecr_project_scope" value="" />
<?php
