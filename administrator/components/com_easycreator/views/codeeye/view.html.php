<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 28-Sep-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package    EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewCodeEye extends JViewLegacy
{
    /**
     * @var EcrProjectBase
     */
    protected $project = null;

    /**
     * @var EcrProjectMatrix
     */
    protected $projectMatrix;

    /**
     * @var string href for running a web application.
     */
    protected $href;

    protected $task;

    /**
     * Standard display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @throws Exception
     * @return mixed|void
     */
    public function display($tpl = null)
    {
        $input = JFactory::getApplication()->input;

        //-- Add javascript
        ecrScript('codeeye', 'pollrequest', 'logconsole');

        //-- Add css
        ecrStylesheet('codeeye');

        $this->ecr_project = $input->get('ecr_project');

        $this->task = $input->get('task');

        //--Get the project
        try
        {
            $this->project = EcrProjectHelper::getProject();

            if(0 == count($this->project->copies))
                throw new Exception(jgettext('No files found'));
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            EcrHtml::formEnd();

            return;
        }

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
            echo sprintf('UNDEFINED Task "%s" in %s', $this->task, __CLASS__).'<br />';
        }

        //--Draw the submenu
        echo $this->displayBar();

        parent::display($tpl);

        EcrHtml::formEnd();
    }

    /**
     * Default task.
     *
     * @return void
     */
    protected function codeeye()
    {
    }

    /**
     * CLI Application View.
     *
     * @return void
     */
    protected function runcli()
    {
        $this->setLayout('runcli');
    }

    /**
     * CLI Application View.
     *
     * @return void
     */
    protected function runwap()
    {
        $webPath = str_replace(JPATH_ROOT.DIRECTORY_SEPARATOR, '', $this->project->getExtensionPath());

        $this->href = JURI::root().$webPath;//.'/'.$this->project->comName.'.php';

        $this->setLayout('runwap');
    }

    /**
     * PHPCS View.
     *
     * @return void
     */
    protected function phpcs()
    {
        $this->setLayout('phpcs');
    }

    /**
     * PHPCPD View.
     *
     * @return void
     */
    protected function phpcpd()
    {
        $this->setLayout('phpcpd');
    }

    //function

    /**
     * Stats View.
     *
     * @return void
     */
    protected function stats()
    {
        $this->projectMatrix = new EcrProjectMatrix($this->project);

        $this->setLayout('stats');
    }

    /**
     * Stats2 View.
     *
     * @return void
     */
    protected function stats2()
    {
        $this->setLayout('stats2');
    }

    /**
     * Reflect View.
     *
     * @return void
     */
    protected function reflect()
    {
        $this->reflection();
    }

    /**
     * Reflection View.
     *
     * @return void
     */
    protected function reflection()
    {
        $this->setLayout('reflection');
    }

    /**
     * PHPDoc View.
     *
     * @return void
     */
    protected function phpdoc()
    {
        $this->setLayout('phpdoc');
    }

    /**
     * PHPLOC View.
     *
     * @return void
     */
    protected function phploc()
    {
        $this->setLayout('phploc');
    }

    /**
     * W3CValidation View.
     *
     * @return void
     */
    protected function w3cvalidation()
    {
        $this->setLayout('w3cvalidation');
    }

    /**
     * GitHub View.
     *
     * @return void
     */
    protected function git()
    {
        $this->setLayout('git');
    }

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
                if(false == JFile::exists(JPATH_ROOT.DS.'bootstrap.php'))
                {
                    $btn = '&nbsp;<span class="ecr_button img icon16-add" onclick="submitbutton(\'copy_bootstrap\');">'
                        .jgettext('Copy bootstrap.php to Joomla root').'</span>';

                    EcrHtml::message(jgettext('Bootstrap file not found').$btn, 'notice');

                    return;
                }

                $this->testsBase = 'administrator'.DS.'components'.DS.$this->project->comName.DS.'tests'.DS.'unit';

                if(false == JFolder::exists(JPATH_ROOT.DS.$this->testsBase))
                {
                    $btn = '&nbsp;<span class="ecr_button img icon16-add"'
                        .' onclick="submitbutton(\'create_test_dir_unit\');">'
                        .jgettext('Create Test directory').'</span>';

                    EcrHtml::message(jgettext('No tests defined yet').$btn, 'notice');

                    return;
                }

                $this->resultsBase = 'administrator'.DS.'components'.DS.$this->project->comName.DS.'results';
                $this->setLayout('phpunit');
                break;

            default:
                EcrHtml::message(sprintf(jgettext('Unit tests for %s not available yet'), $this->project->type), 'error');
                break;
        }
    }

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
//        $btn = '&nbsp;<span class="ecr_button img icon16-add" onclick="submitbutton(\'copy_bootstrap\');">'
//.jgettext('Copy bootstrap.php to Joomla root').'</span>';
//                    EcrHtml::displayMessage(jgettext('Bootstrap file not found').$btn, 'notice');
                //
                //                    return;
                //                }

                $this->testsBase = 'administrator'.DS.'components'.DS.$this->project->comName.DS.'tests'.DS.'system';

                if(false == JFolder::exists(JPATH_ROOT.DS.$this->testsBase))
                {
                    $btn = '&nbsp;<span class="ecr_button img icon16-add"'
                        .' onclick="submitbutton(\'create_test_dir_selenium\');">'
                        .jgettext('Create Test directory').'</span>';

                    EcrHtml::message(jgettext('No tests defined yet').$btn, 'notice');

                    return;
                }

                $this->resultsBase = 'administrator'.DS.'components'.DS.$this->project->comName.DS.'results';
                $this->setLayout('selenium');
                break;

            default:
                EcrHtml::message(sprintf(jgettext('Unit tests for %s not available yet'), $this->project->type), 'error');
                break;
        }
    }

    /**
     * Displays the submenu.
     *
     * @return string html
     */
    private function displayBar()
    {
        $subTasks = array(
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
        , array('title' => jgettext('PHPLOC')
            , 'description' => jgettext('Count the lines of code you have written.')
            , 'icon' => 'eye'
            , 'task' => 'phploc'
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
            /*
        , array('title' => jgettext('GitHub')
            , 'description' => jgettext('GitHub stuff')
            , 'icon' => 'icon'
            , 'task' => 'git'
            )
            */
        );

        if('cliapp' == $this->project->type)
        {
            $subTasks[] = array('title' => jgettext('CLI Runner')
            , 'description' => jgettext('Runs a CLI Application')
            , 'icon' => 'icon'
            , 'task' => 'runcli'
            );
        }

        if('webapp' == $this->project->type)
        {
            $subTasks[] = array('title' => jgettext('Web Runner')
            , 'description' => jgettext('Runs a Web Application')
            , 'icon' => 'icon'
            , 'task' => 'runwap'
            );
        }

        return EcrHtmlMenu::sub($subTasks);
    }
}//class
