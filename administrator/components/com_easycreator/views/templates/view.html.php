<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.view');

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewTemplates extends JView
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
        ecrLoadHelper('easytemplatehelper');

        $task = JRequest::getCmd('task');

        echo '<h1>'.jgettext('Extension templates').'</h1>';
        echo $this->displayBar($task);

        if(in_array($task, get_class_methods($this)))
        {
            //--Execute the task
            $this->$task();
        }
        else
        {
            if($task)
            echo 'UNDEFINED..'.$task.'<br />';
        }

        if($task == 'install')
        {
            //-- We end our form first, cause another one follows
            ecrHTML::easyFormEnd(false);

            parent::display($tpl);
        }
        else
        {
            parent::display($tpl);
            ecrHTML::easyFormEnd();
        }
    }//function

    /**
     * Display the template list.
     *
     * @return void
     */
    private function templates()
    {
        $profiling = false;

        if($profiling)
        {
            jimport('joomla.error.profiler');
            $this->profiler =& JProfiler::getInstance('EasyCreator');
        }

        $this->task = JRequest::getCmd('task');
        $this->ecr_project = JRequest::getCmd('ecr_project');

        $this->path = ECRPATH_EXTENSIONTEMPLATES;

        $this->file_path = JRequest::getVar('file_path');
        $this->file_name = JRequest::getVar('file_name');

        $this->com_type = JRequest::getVar('com_type');
        $this->template = JRequest::getVar('template');

        $this->comTypes = easyProjectHelper::getProjectTypes();

        $cache = JFactory::getCache();
        $cache->setCaching(1);

        if($profiling)
        $this->profiler->mark('start get templates');

        $this->templates  = EasyTemplateHelper::getTemplateList();

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
    private function install()
    {
        $this->setLayout('install');
    }//function

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
     * @param string $task The actual task
     *
     * @return string html
     */
    private function displayBar($task)
    {
        //--Get Joomla! document object
        $document = JFactory::getDocument();

        //--Get component parameters
        $params = JComponentHelper::getParams('com_easycreator');

        //--Setup debugger
        $ecr_help = $params->get('ecr_help');

        $subtasks = array(
        array('title'=> jgettext('Templates')
        , 'description' => jgettext('Manage EasyCreator Extension Templates')
        , 'icon' => 'directory'
        , 'task' => 'templates'
        )
        , array('title' => jgettext('Install')
        , 'description' => jgettext('Installs EasyCreator Extension Templates')
        , 'icon' => 'import'
        , 'task' => 'install'
        )
        , array('title' => jgettext('Export')
        , 'description' => jgettext('Exports EasyCreator Extension Templates')
        , 'icon' => 'export'
        , 'task' => 'export'
        )
        );

        $htmlDescriptionDivs = '';
        $jsVars = '';
        $jsMorphs = '';
        $jsEvents = '';
        $html = '';
        $html .= '<div id="ecr_sub_toolbar" >';

        foreach($subtasks as $sTask)
        {
            $selected =($sTask['task'] == $task) ? '_selected' : '';
            $html .= '<span id="btn_'.$sTask['task'].'" style="margin-left: 0.3em;"'
            .' class="ecr_button'.$selected.' img icon-16-'.$sTask['icon'].'"'
            .' onclick="submitbutton(\''.$sTask['task'].'\');">';
            $html .= $sTask['title'].'</span>';

            if($ecr_help == 'all'
            || $ecr_help == 'some')
            {
                $htmlDescriptionDivs .= '<div class="hidden_div ecr_description" id="desc_'.$sTask['task'].'">'
                .$sTask['description'].'</div>';
                $jsVars .= "var desc_".$sTask['task']." = $('desc_".$sTask['task']."');\n";

                $jsEvents .= "$('btn_".$sTask['task']."').addEvents({\n"
                . "'mouseenter': showTaskDesc.bind(desc_".$sTask['task']."),\n"
                . "'mouseleave': hideTaskDesc.bind(desc_".$sTask['task'].")\n"
                . "});\n";
            }
        }//foreach

        $html .= $htmlDescriptionDivs;

        if($ecr_help == 'all'
        || $ecr_help == 'some')
        {
            $html .= "<script type='text/javascript'>"
            ."window.addEvent('domready', function() {\n"
            ."function showTaskDesc(name) {\n"
            ."this.setStyle('display', 'block');\n"
            ."}\n"
            ."function hideTaskDesc(name) {\n"
            ."  this.setStyle('display', 'none');\n"
            ."}\n"
            . $jsVars
            . $jsEvents
            . "});\n"
            . "</script>";
        }

        $html .= '</div>';

        return $html;
    }//function
}//class
