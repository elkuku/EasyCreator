<?php
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 28-Sep-2009
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
class EasyCreatorControllerCodeEye extends JController
{
    /**
     * Standard display method.
     *
     * @param boolean $cachable If it is cachable
     * @param array|boolean $urlparams Desc
     *
     * @return void
     */
    public function display($cachable = false, $urlparams = false)
    {
        JRequest::setVar('view', 'codeeye');

        parent::display($cachable, $urlparams);
    }//function

    /**
     * Copy the bootstrap.php for UnitTests to the Joomla! root.
     *
     * @return void
     */
    public function copy_bootstrap()
    {
        if(JFile::copy(JPATH_COMPONENT.DS.'helpers'.DS.'bootstrap.php', JPATH_ROOT.DS.'bootstrap.php'))
        {
            ecrHTML::displayMessage(jgettext('The file bootstrap.php has been copied to your Joomla root'));
        }
        else
        {
            ecrHTML::displayMessage(sprintf(jgettext('Can not copy file %s'), 'bootstrap.php'), 'error');
        }

        JRequest::setVar('view', 'codeeye');
        JRequest::setVar('task', 'phpunit');

        parent::display();
    }//function

    /**
     * Enter description here ...
     *
     * @return void
     */
    public function create_test_dir_unit()
    {
        $this->create_test_dir('unit');
        JRequest::setVar('task', 'phpunit');
        parent::display();
    }//function

    /**
     * Enter description here ...
     *
     * @return void
     */
    public function create_test_dir_selenium()
    {
        $this->create_test_dir('system');
        JRequest::setVar('task', 'selenium');
        parent::display();
    }//function

    /**
     * Creates a test directory for UnitTests.
     *
     * @param string $type The type
     *
     * @return void
     */
    private function create_test_dir($type)
    {
        $ecr_project = JRequest::getCmd('ecr_project');

        JRequest::setVar('view', 'codeeye');

        if( ! $ecr_project)
        {
            ecrHTML::displayMessage(jgettext('No project selected'), 'error');

            return;
        }

        if( ! JFolder::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$ecr_project))
        {
            ecrHTML::displayMessage(jgettext('Invalid project'), 'error');

            return;
        }

        if( ! JFolder::create(JPATH_ADMINISTRATOR.DS.'components'.DS.$ecr_project.DS.'tests'.DS.$type))
        {
            ecrHTML::displayMessage(jgettext('Unable to create tests directory'), 'error');

            return;
        }

        ecrHTML::displayMessage(jgettext('The tests directory has been created'));
    }//function
}//class
