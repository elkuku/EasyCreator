<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 28-Sep-2009
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
class EasyCreatorViewCodeEye extends JView
{
    protected $project = null;

    /**
     * Standard display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        ecrLoadHelper('pearhelpers.consolehelper');

        //-- Add javascript
        ecrScript('codeeye');

        //-- Add css
        ecrStylesheet('codeeye');

        $this->ecr_project = JRequest::getCmd('ecr_project');

        $this->task = JRequest::getCmd('task');

        //--Get the project
        try
        {
            $this->project = EasyProjectHelper::getProject();

            if( ! count($this->project->copies))
            throw new Exception(jgettext('No files found'));
        }
        catch(Exception $e)
        {
            $m =(JDEBUG || ECR_DEBUG) ? $e->getMessage() : $e;

            ecrHTML::displayMessage($m, 'error');

            ecrHTML::easyFormEnd();

            return;
        }//try

        if(in_array($this->task, get_class_methods($this)))
        {
            //--Execute the task
            $this->{$this->task}();

            if($this->task == 'display_snip')
            {
                //--Raw view
                parent::display($tpl);

                return;
            }
        }
        else if($this->task)
        {
            echo sprintf('UNDEFINED Task %s in view %s', $this->task, $this->_name).'<br />';
        }

        //--Draw h1 header
        ecrHTML::header(jgettext('CodeEye'), $this->project, 'xeyes');

        //--Draw the submenu
        echo $this->displayBar();

        parent::display($tpl);

        ecrHTML::easyFormEnd();
    }//function

    /**
     * Default task.
     *
     * @return void
     */
    protected function codeeye()
    {
    }//function

    /**
     * PHPCS View.
     *
     * @return void
     */
    protected function phpcs()
    {
        $this->setLayout('phpcs');
    }//function

    /**
     * PHPCPD View.
     *
     * @return void
     */
    protected function phpcpd()
    {
        $this->setLayout('phpcpd');
    }//function

    /**
     * Stats View.
     *
     * @return void
     */
    protected function stats()
    {
        ecrLoadHelper('projectmatrix');

        $this->projectMatrix = new EasyProjectMatrix($this->project);

        $this->setLayout('stats');
    }//function

    /**
     * Stats2 View.
     *
     * @return void
     */
    protected function stats2()
    {
        $this->setLayout('stats2');
    }//function

    /**
     * Reflect View.
     *
     * @return void
     */
    protected function reflect()
    {
        $this->reflection();
    }//function

    /**
     * Reflection View.
     *
     * @return void
     */
    protected function reflection()
    {
        $this->project = $this->project;

        $this->setLayout('reflection');
    }//function

    /**
     * PHPDoc View.
     *
     * @return void
     */
    protected function phpdoc()
    {
        $this->setLayout('phpdoc');
    }//function

    /**
     * W3CValidation View.
     *
     * @return void
     */
    protected function w3cvalidation()
    {
        $this->setLayout('w3cvalidation');
    }//function

    /**
     * GitHub View.
     *
     * @return void
     */
    protected function git()
    {
        $this->setLayout('git');
    }//function

    /**
     * PHPUnit View.
     *
     * @return void
     */
    protected function phpunit()
    {
        /*
         * Set the base directory for tests to 'tests' under component directory
         */
        switch($this->project->type)
        {
            case 'component':
                if( ! JFile::exists(JPATH_ROOT.DS.'bootstrap.php'))
                {
                    $btn = '&nbsp;<span class="ecr_button img icon-16-add" onclick="submitbutton(\'copy_bootstrap\');">'
                    .jgettext('Copy bootstrap.php to Joomla root').'</span>';

                    ecrHTML::displayMessage(jgettext('Bootstrap file not found').$btn, 'notice');

                    return;
                }

                $this->testsBase = 'administrator'.DS.'components'.DS.$this->project->comName.DS.'tests'.DS.'unit';

                if( ! JFolder::exists(JPATH_ROOT.DS.$this->testsBase))
                {
                    $btn = '&nbsp;<span class="ecr_button img icon-16-add"'
                    .' onclick="submitbutton(\'create_test_dir_unit\');">'
                    .jgettext('Create Test directory').'</span>';

                    ecrHTML::displayMessage(jgettext('No tests defined yet').$btn, 'notice');

                    return;
                }

                $this->resultsBase = 'administrator'.DS.'components'.DS.$this->project->comName.DS.'results';
                $this->setLayout('phpunit');
                break;

            default:
                ecrHTML::displayMessage(sprintf(jgettext('Unit tests for %s not available yet'), $this->project->type), 'error');
                break;
        }//switch
    }//function

    /**
     * Selenium View.
     *
     * @return void
     */
    protected function selenium()
    {
        /*
         * Set the base directory for tests to 'tests' under component directory
         */
        switch($this->project->type)
        {
            case 'component':
                //                if( ! JFile::exists(JPATH_ROOT.DS.'bootstrap.php'))
                //                {
//                    $btn = '&nbsp;<span class="ecr_button img icon-16-add" onclick="submitbutton(\'copy_bootstrap\');">'
//.jgettext('Copy bootstrap.php to Joomla root').'</span>';
//                    ecrHTML::displayMessage(jgettext('Bootstrap file not found').$btn, 'notice');
                //
                //                    return;
                //                }

                $this->testsBase = 'administrator'.DS.'components'.DS.$this->project->comName.DS.'tests'.DS.'system';

                if( ! JFolder::exists(JPATH_ROOT.DS.$this->testsBase))
                {
                    $btn = '&nbsp;<span class="ecr_button img icon-16-add"'
                    .' onclick="submitbutton(\'create_test_dir_selenium\');">'
                    .jgettext('Create Test directory').'</span>';

                    ecrHTML::displayMessage(jgettext('No tests defined yet').$btn, 'notice');

                    return;
                }

                $this->resultsBase = 'administrator'.DS.'components'.DS.$this->project->comName.DS.'results';
                $this->setLayout('selenium');
                break;

            default:
                ecrHTML::displayMessage(sprintf(jgettext('Unit tests for %s not available yet'), $this->project->type), 'error');
                break;
        }//switch
    }//function

    /**
     * Displays the submenu.
     *
     * @return string html
     */
    private function displayBar()
    {
        //--Setup debugger
        $ecr_help = JComponentHelper::getParams('com_easycreator')->get('ecr_help');

        $subtasks = array(
        array('title' => jgettext('CodeSniffer')
        , 'description' => jgettext('Use CodeSniffer to assure coding standards.')
        , 'icon' => 'eye'
        , 'task' => 'phpcs'
        )
        , array('title' => jgettext('Duplicated Code')
        , 'description' => jgettext('Searches your code for duplicates.')
        , 'icon' => 'eye'
        , 'task' => 'phpcpd'
        )
        , array('title' => jgettext('PHPUnit Tests')
        , 'description' => jgettext('Generates and executes PHPUnit Tests.')
        , 'icon' => 'eye'
        , 'task' => 'phpunit'
        )
        , array('title' => jgettext('Selenium Tests')
        , 'description' => jgettext('Generates and executes Selenium Tests.')
        , 'icon' => 'eye'
        , 'task' => 'selenium'
        )
        , array('title' => jgettext('PHPDocumentor')
        , 'description' => jgettext('Create automatic documentation for your project with PHPDocumentor.')
        , 'icon' => 'eye'
        , 'task' => 'phpdoc'
        )
        , array('title' => jgettext('Statistics')
        , 'description' => jgettext('Shows some statistics about your project.')
        , 'icon' => 'chart'
        , 'task' => 'stats'
        )
        , array('title' => jgettext('Statistics2')
        , 'description' => jgettext('Shows some statistics about your project.')
        , 'icon' => 'chart'
        , 'task' => 'stats2'
        )
        , array('title' => jgettext('Reflection')
        , 'description' => jgettext('Displays information about your project (experimental).')
        , 'icon' => 'icon'
        , 'task' => 'reflection'
        )
        , array('title' => jgettext('GitHub')
        , 'description' => jgettext('aaaaaaaaaa')
        , 'icon' => 'icon'
        , 'task' => 'git'
        )
        );

        $htmlDescriptionDivs = '';
        $jsVars = '';
        $jsMorphs = '';
        $jsEvents = '';
        $html = '';
        $html .= '<div id="ecr_sub_toolbar" style="margin-bottom: 1em; margin-top: 0.5em;">';

        foreach($subtasks as $sTask)
        {
            if($this->project->type != 'component'
            && $sTask['task'] == 'tables')
            {
                continue;
            }

            $selected =($sTask['task'] == $this->task) ? '_selected' : '';
            $html .= '<span id="btn_'.$sTask['task'].'" style="margin-left: 0.3em;" class="ecr_button'
            .$selected.' img icon-16-'.$sTask['icon'].'" onclick="submitbutton(\''.$sTask['task'].'\');">';

            $html .= $sTask['title'].'</span>';

            if($ecr_help == 'all'
            || $ecr_help == 'some')
            {
                $htmlDescriptionDivs .= '<div class="hidden_div ecr_description" id="desc_'
                .$sTask['task'].'">'.$sTask['description'].'</div>';

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
