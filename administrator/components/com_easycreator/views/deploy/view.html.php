<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 19-Apr-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

jimport('joomla.application.component.view');

/**
 * EasyCreator HTML view.
 *
 */
class EasyCreatorViewDeploy extends JView
{
    protected $project;

    /**
     * Standard display method.
     *
     * @param null|string $tpl The name of the template file to parse;
     *
     * @return mixed|void
     */
    public function display($tpl = null)
    {
        ecrStylesheet('deploy');
        ecrScript('deploy', 'php2js', 'pollrequest');

        $task = JRequest::getCmd('task');

        try
        {
            $this->project = EcrProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            EcrHtml::displayMessage($e);

            EcrHtml::easyFormEnd();

            return;
        }//try

        //-- Draw h1 header
        EcrHtml::header(jgettext('Deploy'), $this->project, 'ecr_deploy');

        if(in_array($task, get_class_methods($this)))
        {
            //-- Execute the task
            $this->$task();
        }

        //-- Draw the submenu
        echo $this->displayBar();

        parent::display($tpl);

        EcrHtml::easyFormEnd();
    }//function

    protected function package()
    {
        $this->setLayout('package');
    }

    /**
     * Displays the submenu.
     *
     * @return string html
     */
    private function displayBar()
    {
        $subTasks = array(
            array('title' => jgettext('Files')
            , 'description' => jgettext('Deploys the project files to your server.')
            , 'icon' => 'package'
            , 'task' => array('deploy', 'files')
            )
            , array('title' => jgettext('Package')
            , 'description' => jgettext('This deploys you package to a server.')
            , 'icon' => 'archive'
            , 'task' => 'package'
            )
        );

        return EcrHtml::getSubBar($subTasks);
    }//function
}//class
