<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 23-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.controller');

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerStarter extends JController
{
    /**
     * Standard display method.
     *
     * @param boolean $cachable If true, the view output will be cached
     * @param array $urlparams An array of safe url parameters and their variable types,
     * for valid values see {@link JFilterInput::clean()}.
     *
     * @return void
     * @see JController::display()
     */
    public function display($cachable = false, $urlparams = false)
    {
        JRequest::setVar('view', 'starter');

        parent::display($cachable, $urlparams);
    }//function

    /**
     * Build a new Joomla! extension from request.
     *
     * @return mixed Redirect on success | boolean false on error
     */
    public function starterstart()
    {
        ecrLoadHelper('builder');
        $EasyBuilder = new EasyBuilder();

        $type = JRequest::getCmd('tpl_type');
        $name = JRequest::getCmd('tpl_name');
        $comName = JRequest::getCmd('com_name');

        if( ! $newProject = $EasyBuilder->build($type, $name, $comName))
        {
            //-- Error
            ecrHTML::displayMessage('An error happened while creating your project', 'error');
            JError::raiseWarning(100, jgettext('An error happened while creating your project'));
            $EasyBuilder->printErrors();

            ecrHTML::easyFormEnd();

            return false;
        }

        if(JRequest::getCmd('ecr_test_mode') == 'test')
        {
            //-- Exiting in test mode
            echo '<h2>Exiting in test mode...</h2>';

            echo $EasyBuilder->printLog();

            $EasyBuilder->printErrors();

            ecrHTML::easyFormEnd();

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
        ecrLoadHelper('builder');

        $EasyBuilder = new EasyBuilder();

        $type = JRequest::getCmd('ecr_project_type');
        $name = JRequest::getCmd('ecr_project_name');
        $scope = JRequest::getCmd('ecr_project_scope');

        if( ! $project = $EasyBuilder->registerProject($type, $name, $scope))
        {
            //-- Error
            JError::raiseWarning(100, 'Can not register project');

            $EasyBuilder->printErrors();

            ecrHTML::easyFormEnd();

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
        ecrLoadHelper('php_file_tree');

        $jsFile = '';
        $fileTree = new phpFileTree('', '', $jsFile, '');

        $extType = JRequest::getCmd('extType');
        $folder = JRequest::getCmd('folder');

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
