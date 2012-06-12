<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.view');

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package    EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewStuffer extends JView
{
    /**
     * @var EcrProjectBase
     */
    protected $project;

    /**
     * @var EasyCreatorViewStuffer
     */
    protected $lists = array();

    /**
     * @var string
     */
    protected $ecr_project;

    /**
     * Standard display method.
     *
     * @param null|string $tpl The name of the template file to parse;
     *
     * @throws Exception
     * @return mixed|void
     */
    public function display($tpl = null)
    {
        ecrScript('stuffer');

        $this->ecr_project = JRequest::getCmd('ecr_project');

        //-- Get the project
        try
        {
            $this->project = EcrProjectHelper::getProject();

            if('package' == $this->project->type
                && ! $this->project->creationDate
            )
            {
                //-- This is a hack to detect that a package has no install manifest :(
                throw new Exception(jgettext('Invalid project'));
            }
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            EcrHtml::formEnd();

            return;
        }

        $task = JRequest::getCmd('task', 'stuffer');
        $tmpl = JRequest::getCmd('tmpl');

        //-- We are loosing the controller name when coming from other tasks - config :(
        JRequest::setVar('controller', 'stuffer');

        if($task != 'display_snip'
            && $task != 'aj_reflection'
            && $tmpl != 'component'
        )
        {
            //-- Draw h1 header
            //EcrHtml::header(jgettext('Configure'), $this->project, 'ecr_settings');

            //-- Draw the submenu if task is not for a raw view
            echo $this->displayBar($task);
        }

        if(in_array($task, get_class_methods($this)))
        {
            //-- Execute the task
            $this->$task();

            if($task == 'display_snip')
            {
                //-- Raw view
                parent::display($tpl);

                return;
            }
        }
        else
        {
            if($task)
                echo 'UNDEFINED..'.$task.'<br />';

            $this->stuffer();
        }

        $this->assignRef('task', $task);
        $this->assignRef('tmpl', $tmpl);

        parent::display($tpl);

        EcrHtml::formEnd();
    }

    /**
     * Displays the submenu.
     *
     * @param string $task The actual task
     *
     * @return string html
     */
    private function displayBar($task)
    {
        $subtasks = array(
            array('title' => jgettext('Building')
            , 'description'
            => jgettext('Shows options for building your project like credits, files and folders to copy, languages and admin menu.')
            , 'icon' => 'ecr_config'
            , 'task' => 'stuffer'
            )
        , array('title' => jgettext('Files')
            , 'description' => jgettext('Shows all the files belonging to your project')
            , 'icon' => 'directory'
            , 'task' => 'files'
            )
        );

        if('cliapp' != $this->project->type
            && 'webapp' != $this->project->type
        )
        {
            $subtasks[] = array('title' => jgettext('Installing')
            , 'description' => jgettext('Create and modify install and uninstall files for your project.')
            , 'icon' => 'installfolder'
            , 'task' => 'install'
            );
            $subtasks[] = array('title' => jgettext('Parameters')
            , 'description' => jgettext('Modify your project parameters stored in XML files.')
            , 'icon' => 'ecr_params'
            , 'task' => 'projectparams'
            );

            $subtasks[] = array('title' => jgettext('DataDesigner')
            , 'description' => jgettext('Automated source code and db creation for your project.')
            , 'icon' => 'ecr_db'
            , 'task' => 'tables'
            );
        }

        $subtasks[] = array('title' => jgettext('Remove Project')
        , 'description' => jgettext('This will your delete your project.')
        , 'icon' => 'delete'
        , 'task' => 'projectdelete'
        );

        $rightTasks = array();

        if($task == 'stuffer')
        {
            $rightTasks[] = array('title' => jgettext('Save')
            , 'description' => jgettext('Save the configuration')
            , 'icon' => 'save'
            //, 'class' => 'btn-primary'
            , 'task' => 'save_config');
        }

        return EcrHtmlMenu::sub($subtasks, $rightTasks);
    }

    /**
     * Stuffer View.
     *
     * @return void
     */
    private function stuffer()
    {
        $this->projectList = EcrProjectHelper::getProjectList();
        $this->installFiles = EcrProjectHelper::findInstallFiles($this->project);

        $this->lists['presets'] = EcrHtmlSelect::presets(
            $this->project, array('onchange' => 'Stuffer.loadPreset(this);')
        );

        $this->setLayout('stuffer');
    }

    private function new_element()
    {
        $this->files();
    }

    /**
     * Files View.
     *
     * @return void
     */
    private function files()
    {
        ecrScript('addelement');

        $this->setLayout('files');
    }

    /**
     * Install View.
     *
     * @return void
     */
    private function install()
    {
        $this->installFiles = EcrProjectHelper::findInstallFiles($this->project);

        $this->setLayout('install');
    }

    /**
     * Delete project View.
     *
     * @return void
     */
    private function projectdelete()
    {
        $this->setLayout('deleteconfirm');
    }

    /**
     * Project parameters View.
     *
     * @return void
     */
    private function projectparams()
    {
        $selected_xml = JRequest::getVar('selected_xml');
        $params = array();
        $xmlFiles = array();

        foreach($this->project->copies as $path)
        {
            if(JFolder::exists($path))
            {
                $files = JFolder::files($path, '\.xml', true, true);

                if(count($files))
                {
                    foreach($files as $file)
                    {
                        $xmlFiles[] = substr($file, strlen(JPATH_ROOT) + 1);
                    }
                }
            }
            else if(JFile::getExt($path) == 'xml')
            {
                $xmlFiles[] = substr($path, strlen(JPATH_ROOT) + 1);
            }
        }

        if(in_array($selected_xml, $xmlFiles))
        {
            //-- @Joomla!-version-check
            switch(ECR_JVERSION)
            {
                //-- Get the project params
                case '1.5':
                    $this->params = new JParameter('', JPATH_ROOT.DS.$selected_xml);
                    break;

                case '1.6':
                case '1.7':
                case '2.5':
                    $this->params = JFactory::getXML(JPATH_ROOT.DS.$selected_xml);
                    break;

                default:
                    EcrHtml::message(__METHOD__.' - Undefined J! version', 'error');

                    return false;
                    break;
            }
        }

        $options = array();
        $options[] = JHTML::_('select.option', '', jgettext('Select'));

        for($i = 1; $i < count($xmlFiles) + 1; $i ++)
        {
            $options[$i] = JHTML::_('select.option', $xmlFiles[$i - 1]);
        }

        $xmlSelector = JHTML::_('select.genericlist', $options, 'selected_xml'
            , 'style="font-size: 1.3em;" onchange="submitbutton(\''.JRequest::getCmd('task').'\');"'
            , 'value', 'text', $selected_xml);
        $this->assignRef('xmlSelector', $xmlSelector);

        $this->assignRef('selected_xml', $selected_xml);

        $layout = 'projectparams';

        //-- @Joomla!-compat 1.5
        if('1.5' == ECR_JVERSION)
            $layout .= '_15';

        $this->setLayout($layout);
    }

    /**
     * Tables View.
     *
     * @return void
     */
    private function tables()
    {
        ecrScript('addelement');

        $this->setLayout('tables');
    }

    /**
     * Register table View.
     *
     * @return void
     */
    private function register_table()
    {
        $this->tables();
    }

    /**
     * Create table View.
     *
     * @return void
     */
    private function createTable()
    {
        $this->setLayout('tables');
    }

    /**
     * Display snippet View.
     *
     * @return void
     */
    private function display_snip()
    {
        $path = JRequest::getVar('file_path');
        $start = JRequest::getInt('start');
        $end = JRequest::getInt('end');

        $fileContents = '';

        if( ! JFile::exists($path))
        {
            echo '<div class="ebc_error" align="center">'.jgettext('File not found').'</div>';
            echo $path;

            //-- EXIT
            jexit();
        }
        else
        {
            $fileContents = JFile::read($path);
        }

        if('' != $fileContents)
        {
            $fileContents = explode("\n", $fileContents);

            $this->assignRef('fileContents', $fileContents);
            $this->assignRef('startAtLine', $start);
            $this->assignRef('endAtLine', $end);
            $this->assignRef('path', $path);
        }

        $this->setLayout('snippet');
    }

    /**
     * Draws a list of related links.
     *
     * @return string
     */
    public function drawDocLinks()
    {
        $docLinks = array(
            'Standard parameter types' => 'http://docs.joomla.org/Standard_parameter_types'
        , 'Reference: XML parameters'
            => 'http://dev.joomla.org/component/option,com_jd-wiki/Itemid,/id,references:xml_parameters/'
        , 'Component parameters' => 'http://docs.joomla.org/Component_parameters'
        , 'Defining a parameter in templateDetails.xml'
            => 'http://docs.joomla.org/Defining_a_parameter_in_templateDetails.xml'
        , 'Creating custom template parameter types'
            => 'http://docs.joomla.org/Creating_custom_template_parameter_types'
        );

        $ret = '';

        $ret .= '<br /><hr /><br />';
        $ret .= '<div class="explanation">';
        $ret .= '<br /><strong style="background-color: white; padding: 5px;">'.jgettext('Infos on parameters (external)').'</strong>';
        $ret .= '<ul>';

        foreach($docLinks as $title => $link)
        {
            $ret .= '<li><a class="external" href="'.$link.'" target="_blank" />'.$title.'</a></li>';
        }

        $ret .= '</ul>';
        $ret .= '</div>';
        $ret .= '<br />';

        return $ret;
    }
}//class
