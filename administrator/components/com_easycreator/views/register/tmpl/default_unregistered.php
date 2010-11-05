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

        //@todo project types
        $projectTypes = array(
        'component' => ''
        , 'module' => 'admin,site'
        , 'plugin' => implode(',', JFolder::folders(JPATH_ROOT.DS.'plugins', '.', false, false, array('tmp', '.svn')))
        , 'template' => 'admin,site');

        $task = JRequest::getCmd('task');
        $showCore =(JRequest::getCmd('show_core') == 'show_core') ? true : false;
        $checked =($showCore) ? ' checked="checked"' : '';
        ?>

<table class="adminlist" rules="groups">
	<tr>
		<th class="projectListHeader unregistered" colspan="<?php echo count($projectTypes); ?>"
			style="">
		<div style="float: right;"><input type="checkbox" name="show_core"
			id="show_core" value="show_core" <?php echo $checked; ?>
			onchange="submitbutton('<?php echo $task; ?>');" /> <label
			for="show_core" class="img icon-16-joomla"> <?php echo jgettext('Show Joomla core projects'); ?>
		</label></div>
		<?php echo jgettext('Unregistered Projects'); ?></th>
	</tr>
	<tr valign="top">
	<?php
    foreach($projectTypes as $projectType => $projectScope)
    {
        $scopes = explode(',', $projectScope);
        ?>
		<td>
		<table width="100%" class="ecrlist" rules="groups">
			<tr>
				<th nowrap="nowrap" colspan="2"
					style="background-color: #F2F2F2; color: blue;"><span
					class="img icon-12-<?php echo $projectType; ?>"> </span><?php echo jgettext(ucfirst($projectType).'s'); ?>
				</th>
			</tr>
			<?php
            if( ! is_array($scopes))
            {
                $scopes = array('admin');
            }

            foreach($scopes as $scope)
            {
                $unregisteredProjects = EasyProjectHelper::getUnregisteredProjects($projectType, $scope, $showCore);

                if($projectType != 'component')
                {
                    ?>
			<tr>
				<th colspan="2" style="background-color: #F2F2F2; color: green;"><?php echo ucfirst($scope); ?>
				</th>
			</tr>
			<?php
                }

                if( ! count($unregisteredProjects))
                {
                    ?>
            <tr>
				<td colspan="2" style="color: orange;" align="center"><?php echo jgettext('None found'); ?>
				</td>
			</tr>
			<?php
            continue;
                }

                $k = 0;

                foreach($unregisteredProjects as $project)
                {
                    ?>
			<tr class="row<?php echo $k; ?>">
				<td><strong><?php echo $project; ?></strong></td>
				<td width="5%">
				<div class="ecr_button img icon-16-install hasEasyTip"
					style="padding-left: 20px;; height: 14px;"
					title="<?php
                    echo jgettext('Register').'&lt;span class=\'img icon-16-install\''
                    .' style=\'padding-left: 20px; height: 14px;\'&gt;&lt;/span&gt;::'
                    .jgettext(ucfirst($projectType)).' - '.$project; ?>"
					onclick="registerProject(<?php echo "'$projectType', '$project', '$scope'"; ?>);"></div>
				</td>
			</tr>
			<?php
            $k = 1 - $k;
                }//foreach
            }//foreach
            ?>
		</table>
		</td>
		<?php
    }//foreach
    ?>
	</tr>
</table>

<input type="hidden"
	name="ecr_project_type" value="" />
<input type="hidden"
	name="ecr_project_name" value="" />
<input type="hidden"
	name="ecr_project_scope" value="" />
	<?php
