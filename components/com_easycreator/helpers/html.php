<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Frontend helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 24-Sep-2008
 */

/**
 * Frontend HTML. ...
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
        echo '<h1>EasyCreator :: <small style="color: green;">'.jgettext('Sandbox').'</small></h1>'.NL;

        echo '<form name="adminForm" method="post">';
    }

    /**
     * Displays the footer, closing the form.
     *
     * @return void
     */
    public static function end()
    {
        echo '<input type="hidden" name="ecr_project"/>'.NL;
        echo '</form>'.NL;
    }

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
        $projects = EcrProjectHelper::getProjectList();
        $selectedProject = JFactory::getApplication()->input->get('ecr_project');

        if( ! isset($projects['component']))
        {
            JFactory::getApplication()->enqueueMessage(jgettext('No projects found'), 'error');

            return;
        }

        echo jgettext('Registered projects');

        echo '<ol style="list-style-type: none; text-align: left;">';

        foreach($projects['component'] as $project)
        {
            $selected = ($project->comName == $selectedProject) ? '_selected' : '';
            echo '<li class="ecr_button'.$selected.'" onclick="drawProject(\''.$project->comName.'\');">'
                .$project->name
                .'</li>';
        }

        echo '</ol>';
    }
}
