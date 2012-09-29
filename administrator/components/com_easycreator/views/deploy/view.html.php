<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 19-Apr-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator HTML view.
 *
 */
class EasyCreatorViewDeploy extends JViewLegacy
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

        $task = JFactory::getApplication()->input->get('task');

        try
        {
            $this->project = EcrProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            EcrHtml::formEnd();

            return;
        }

        //-- Draw h1 header
        //EcrHtml::header(jgettext('Deploy'), $this->project, 'ecr_deploy');

        if(in_array($task, get_class_methods($this)))
        {
            //-- Execute the task
            $this->$task();
            $this->setLayout($task);
        }

        //-- Draw the submenu
        echo $this->displayBar();

        parent::display($tpl);

        EcrHtml::formEnd();
    }

    private function package()
    {
    }

    private function updateserver()
    {
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
            , 'description' => jgettext('Deploy the project files to your server.')
            , 'icon' => 'deploy'
            , 'task' => array('deploy', 'files')
            )
        , array('title' => jgettext('Package')
            , 'description' => jgettext('Deploy you package to a server.')
            , 'icon' => 'package'
            , 'task' => 'package'
            )
        , array('title' => jgettext('Update server')
            , 'description' => jgettext('Manage your update server.')
            , 'icon' => 'package'
            , 'task' => 'updateserver'
            )
        );

        return EcrHtmlMenu::sub($subTasks);
    }
}
