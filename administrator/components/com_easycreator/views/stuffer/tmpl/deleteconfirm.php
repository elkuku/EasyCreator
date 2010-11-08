<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 10-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$skull = JURI::root().'administrator/components/com_easycreator/assets/images/skull.png';
?>
<div  align="center" style="background-color: #fff; text-align: center;">
<?php ecrHTML::boxStart(); ?>
	<h1 style="color: red;">
		<span><?php echo jgettext('Attention'); ?> !</span></h1>
		<h1>
		<?php echo sprintf(jgettext('The project %s will be deleted'), $this->project->name); ?>
	</h1>
	<table width="100%">
		<tr>
			<td class="ecr_deletebutton" align="center" onclick="submitbutton('delete_project')">
				<img src="<?php echo $skull; ?>" alt="<?php echo jgettext('Delete'); ?>" />
				<br />
				<?php echo sprintf(jgettext('Delete %s project file'), $this->project->name); ?>
			</td>
			<td width="3%">&nbsp;</td>
			<td class="ecr_deletebutton" align="center" onclick="submitbutton('delete_project_full')">
				<img src="<?php echo $skull; ?>" alt="<?php echo jgettext('Delete'); ?>" />
				<img src="<?php echo $skull; ?>" alt="<?php echo jgettext('Delete'); ?>" />
				<img src="<?php echo $skull; ?>" alt="<?php echo jgettext('Delete'); ?>" />
				<br />
				<?php echo sprintf(jgettext('Delete the whole %s project including files and database'), $this->project->name); ?>
			</td>
		</tr>
	</table>
<?php ecrHTML::boxEnd(); ?>
</div>
