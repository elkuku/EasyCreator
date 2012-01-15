<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
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
            EcrHtml::displayMessage($e);

            EcrHtml::easyFormEnd();

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
        EcrHtml::header(jgettext('CodeEye'), $this->project, 'xeyes');

        //--Draw the submenu
        echo $this->displayBar();

        parent::display($tpl);

        EcrHtml::easyFormEnd();
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
     * PHPLOC View.
     *
     * @return void
     */
    protected function phploc()
    {
        $this->setLayout('phploc');
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

                    EcrHtml::displayMessage(jgettext('Bootstrap file not found').$btn, 'notice');

                    return;
                }

                $this->testsBase = 'administrator'.DS.'components'.DS.$this->project->comName.DS.'tests'.DS.'unit';

                if( ! JFolder::exists(JPATH_ROOT.DS.$this->testsBase))
                {
                    $btn = '&nbsp;<span class="ecr_button img icon-16-add"'
                    .' onclick="submitbutton(\'create_test_dir_unit\');">'
                    .jgettext('Create Test directory').'</span>';

                    EcrHtml::displayMessage(jgettext('No tests defined yet').$btn, 'notice');

                    return;
                }

                $this->resultsBase = 'administrator'.DS.'components'.DS.$this->project->comName.DS.'results';
                $this->setLayout('phpunit');
                break;

            default:
                EcrHtml::displayMessage(sprintf(jgettext('Unit tests for %s not available yet'), $this->project->type), 'error');
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
//                    EcrHtml::displayMessage(jgettext('Bootstrap file not found').$btn, 'notice');
                //
                //                    return;
                //                }

                $this->testsBase = 'administrator'.DS.'components'.DS.$this->project->comName.DS.'tests'.DS.'system';

                if( ! JFolder::exists(JPATH_ROOT.DS.$this->testsBase))
                {
                    $btn = '&nbsp;<span class="ecr_button img icon-16-add"'
                    .' onclick="submitbutton(\'create_test_dir_selenium\');">'
                    .jgettext('Create Test directory').'</span>';

                    EcrHtml::displayMessage(jgettext('No tests defined yet').$btn, 'notice');

                    return;
                }

                $this->resultsBase = 'administrator'.DS.'components'.DS.$this->project->comName.DS.'results';
                $this->setLayout('selenium');
                break;

            default:
                EcrHtml::displayMessage(sprintf(jgettext('Unit tests for %s not available yet'), $this->project->type), 'error');
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
        , array('title' => jgettext('GitHub')
        , 'description' => jgettext('aaaaaaaaaa')
        , 'icon' => 'icon'
        , 'task' => 'git'
        )
        );

        return EcrHtml::getSubBar($subTasks);
    }//function
}//class
