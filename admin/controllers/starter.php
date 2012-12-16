<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 23-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerStarter extends JControllerLegacy
{
    /**
     * Standard display method.
     *
     * @param bool       $cachable  If true, the view output will be cached
     * @param array|bool $urlparams An array of safe url parameters and their variable types,
     *                              for valid values see {@link JFilterInput::clean()}.
     *
     * @return \JController|void
     */
    public function display($cachable = false, $urlparams = false)
    {
        JFactory::getApplication()->input->set('view', 'starter');

        parent::display($cachable, $urlparams);
    }//function

    /**
     * Build a new Joomla! extension from request.
     *
     * @return mixed Redirect on success | boolean false on error
     */
    public function starterstart()
    {
        $input = JFactory::getApplication()->input;

        $builder = new EcrProjectBuilder;

        $type = $input->get('tpl_type');
        $name = $input->get('tpl_name');
        $comName = $input->get('com_name');

        if( ! $newProject = $builder->build($type, $name, $comName))
        {
            //-- Error
            EcrHtml::message('An error happened while creating your project', 'error');
            JFactory::getApplication()->enqueueMessage(jgettext('An error happened while creating your project'), 'error');
            $builder->printErrors();

            EcrHtml::formEnd();

            return false;
        }

        if('test' == $input->get('ecr_test_mode'))
        {
            //-- Exiting in test mode
            echo '<h2>Exiting in test mode...</h2>';

            echo $builder->printLog();

            $builder->printErrors();

            EcrHtml::formEnd();

            return true;
        }

        $ecr_project = JFile::stripExt($newProject->getEcrXmlFileName());

        $uri = 'index.php?option=com_easycreator&controller=stuffer&ecr_project='.$ecr_project;

        $this->setRedirect($uri, jgettext('Your project has been created'));
    }//function

    /**
     * Register a Joomla! extension as an EasyCreator project.
     *
     * @return mixed Redirect on success | boolean false on error
     */
    public function register_project()
    {
        $input = JFactory::getApplication()->input;

        $builder = new EcrProjectBuilder;

        $type = $input->get('ecr_project_type');
        $name = $input->get('ecr_project_name');
        $scope = $input->get('ecr_project_scope');

        $project = $builder->registerProject($type, $name, $scope);

        if(false == $project)
        {
            //-- Error
            JFactory::getApplication()->enqueueMessage('Can not register project', 'error');

            $builder->printErrors();

            EcrHtml::formEnd();

            return false;
        }

        $ecr_project = JFile::stripExt($project->getEcrXmlFileName());

        $uri = 'index.php?option=com_easycreator&controller=stuffer&ecr_project='.$ecr_project;

        $this->setRedirect($uri, jgettext('Your project has been registered'));
    }//function

    /**
     * Get extended Information for an extension temlate.
     *
     * AJAX called. !
     *
     * @return void
     */
    public function ajGetExtensionTemplateInfo()
    {
        $input = JFactory::getApplication()->input;

        $jsFile = '';
        $fileTree = new EcrFileTree('', '', $jsFile, '');

        $extType = $input->get('extType');
        $folder = $input->get('folder');

        $response = array();
        $response['status'] = 0;
        $response['text'] = '';

        if( ! $extType || ! $folder)
        {
            $response['text'] = 'Invalid options';

            echo json_encode($response);

            return;
        }

        $dir = ECRPATH_EXTENSIONTEMPLATES.DS.$extType.DS.$folder;

        $folders = JFolder::folders($dir, '.', false, true, array('.svn'));

        foreach($folders as $folder)
        {
            $fileTree->setDir($folder);
            $response['text'] .= $fileTree->drawFullTree();
        }//foreach

        $response['status'] = 1;

        echo json_encode($response);
    }//function
}//class
