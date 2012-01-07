<?php
/**
 * @package    EasyCreator
 * @subpackage Frontend helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 24-Sep-2008
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class easyHTML
{
    /**
     * Displays the 'header' for the component, starting the form.
     *
     * @return void
     */
    public static function start()
    {
        ?>
<h1><img
	src="<?php echo JURI::root(); ?>components/com_easycreator/assets/images/easylogo_t.png"
	alt="easy-joomla logo" /> EasyCreator :: <small style="color: green;"><?php echo jgettext('Sandbox'); ?></small>
</h1>

<form name="adminForm" method="post"><?php
    }//function

    /**
     * Displays the footer, closing the form.
     *
     * @return void
     */
    public static function end()
    {
        ?> <input type="hidden" name="ebc_project" /></form>
        <?php
    }//function

    /**
     * Draws a project selector.
     *
     * @return void
     */
    public static function projectSelector()
    {
        //--Get the project helper
        JLoader::import('helpers.projecthelper', JPATH_COMPONENT_ADMINISTRATOR);

        //--Get existing projects
        $projects = EasyProjectHelper::getProjectList();
        $selectedProject = JRequest::getVar('ebc_project');

        if( ! isset($projects['component']))
        {
	        JFactory::getApplication()->enqueueMessage(jgettext('No projects found'), 'error');

            return;
        }

        echo jgettext('Registered projects');
        ?>
<ol style="list-style-type: none; text-align: left;">
<?php
foreach($projects['component'] as $project)
{
    $selected =($project->comName == $selectedProject) ? '_selected' : '';
    echo '<li class="ecr_button'.$selected.'" onclick="drawProject(\''.$project->comName.'\');">'.$project->name.'</li>';
}//foreach
?>
</ol>
<?php
}//function
}//class
