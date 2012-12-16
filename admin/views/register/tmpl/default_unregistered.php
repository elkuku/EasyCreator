<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$projectTypes = EcrProjectHelper::getProjectTypes();
$projectScopes = EcrProjectHelper::getProjectScopes();

$input = JFactory::getApplication()->input;

$task = $input->get('task');
$showCore = ($input->get('show_core') == 'show_core') ? true : false;
$checked = ($showCore) ? ' checked="checked"' : '';

?>
<div class="projectListHeader unregistered">
    <div style="float: right;">
        <input type="checkbox" name="show_core"
               id="show_core" value="show_core" <?php echo $checked; ?>
               onchange="submitbutton('<?php echo $task; ?>');"/>
        <label for="show_core" class="inline">
            <i class="img icon16-joomla"></i><?php echo jgettext('Show Joomla core projects'); ?>
        </label>
    </div>
    <?php echo jgettext('Unregistered Projects'); ?>
</div>
<?php
foreach($projectScopes as $comType => $projectScope) :
    $scopes = explode(',', $projectScope);

    ?>
<div class="ecr_floatbox">
    <?php

    echo '<div class="boxHeader img icon12-'.$comType.'" style=" min-width: 150px;">';
    echo $projectTypes[$comType]->translateTypePlural();
    echo '</div>';

    foreach($scopes as $scope) :
        $unregisteredProjects = EcrProjectHelper::getUnregisteredProjects($comType, $scope, $showCore);

        if($comType != 'component' && $scope) :
            ?>
            <div style="background-color: #F2F2F2; color: green; padding: 0.3em; font-weight: bold;">
                <?php echo ucfirst($scope); ?>
            </div>
            <?php
        endif;

        if(0 == count($unregisteredProjects)) :
            ?>
            <div style="color: #ccc; text-align: center;">
                <?php echo jgettext('None found'); ?>
            </div>
            <?php
            continue;
        endif;

        $k = 0;

        foreach($unregisteredProjects as $project) :
            ?>
            <div class="btn block hasTip"
                 style="padding-left: 20px;; height: 14px; margin-top: 0.3em; margin-bottom: 0.3em;"
                 title="<?php
                     echo jgettext('Register').'&lt;span class=\'img icon16-import\''
                         .' style=\'padding-left: 20px; height: 14px;\'&gt;&lt;/span&gt;::'
                         .jgettext(ucfirst($comType)).' - '.$project; ?>"
                 onclick="registerProject(<?php echo "'$comType', '$project', '$scope'"; ?>);">
                <strong><?php echo $project; ?></strong>
            </div>
            <?php
            $k = 1 - $k;
        endforeach;
    endforeach;
    ?>
</div>
<?php endforeach; ?>
<div style="clear: both;"></div>

<input type="hidden" name="ecr_project_type" value=""/>
<input type="hidden" name="ecr_project_name" value=""/>
<input type="hidden" name="ecr_project_scope" value=""/>
