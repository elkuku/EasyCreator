<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewTemplates extends JViewLegacy
{
    protected $profiler = null;

    /**
     * Standard display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $task = JFactory::getApplication()->input->get('task');

        echo $this->displayBar();

        if(in_array($task, get_class_methods($this)))
        {
            //-- Execute the task
            $this->$task();
        }
        else
        {
            if($task)
            echo 'UNDEFINED..'.$task.'<br />';
        }

        if($task == 'tplinstall')
        {
            //-- We end our form first, cause another one follows
            echo '<input type="hidden" name="com_type" /><input type="hidden" name="template" />';

            EcrHtml::formEnd(false);

            parent::display($tpl);
        }
        else
        {
            parent::display($tpl);

            EcrHtml::formEnd();
        }
    }//function

    /**
     * Display the template list.
     *
     * @return void
     */
    private function templates()
    {
        $input = JFactory::getApplication()->input;

        $profiling = false;

        if($profiling)
        {
//            jimport('joomla.error.profiler');
            $this->profiler = JProfiler::getInstance('EasyCreator');
        }

        $this->task = $input->get('task');
        $this->ecr_project = $input->get('ecr_project');

        $this->path = ECRPATH_EXTENSIONTEMPLATES;

        $this->file_path = $input->getPath('file_path');
        $this->file_name = $input->getPath('file_name');

        $this->com_type = $input->get('com_type');
        $this->template = $input->get('template');

        $this->comTypes = EcrProjectHelper::getProjectTypes();

        $cache = JFactory::getCache();
        $cache->setCaching(1);

        if($profiling)
        $this->profiler->mark('start get templates');

        $this->templates = EcrProjectTemplateHelper::getTemplateList();

        if($profiling)
        $this->profiler->mark('end get cached templates');

        if($profiling)
        echo '<pre>'.print_r($this->profiler->getBuffer(), true).'</pre>';

        $this->setLayout('templates');
    }//function

    /**
     * Install view.
     *
     * @return void
     */
    private function tplinstall()
    {
        $this->setLayout('install');
    }//function

    private function tplarchive()
    {
        $this->setLayout('archive');
    }

    /**
     * Export view.
     *
     * @return void
     */
    private function export()
    {
        $this->setLayout('export');
    }//function

    /**
     * Displays the submenu.
     *
     * @return string html
     */
    private function displayBar()
    {
        $subTasks = array(
            array('title' => jgettext('Templates')
            , 'description' => jgettext('Manage EasyCreator Extension Templates')
            , 'icon' => 'directory'
            , 'task' => 'templates'
            )
            , array('title' => jgettext('Install')
            , 'description' => jgettext('Installs EasyCreator Extension Templates')
            , 'icon' => 'import'
            , 'task' => 'tplinstall'
            )
            , array('title' => jgettext('Export')
            , 'description' => jgettext('Exports EasyCreator Extension Templates')
            , 'icon' => 'export'
            , 'task' => 'export'
            )
            , array('title' => jgettext('Archive')
            , 'description' => jgettext('View archived versions of your extension.')
            , 'icon' => 'ecr_archive'
            , 'task' => 'tplarchive'
            )
        );

        return EcrHtmlMenu::sub($subTasks);
    }//function
}//class
