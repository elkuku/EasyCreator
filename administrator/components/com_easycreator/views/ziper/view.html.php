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
 * Enter description here ...@todo class comment.
 *
 */
class EasyCreatorViewZiper extends JView
{
    protected $zipResult = false;

    /**
     * Standard display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        ecrScript('ziper');

        ecrStylesheet('ziper');

        $this->ecr_project = JRequest::getCmd('ecr_project');

        $this->task = JRequest::getCmd('task');

        //--Get the project
        try
        {
            $this->project = EasyProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            ecrHTML::displayMessage($e);

            ecrHTML::easyFormEnd();

            return;
        }//try

        //-- Draw h1 header
        ecrHTML::header(jgettext('Component ZIPer'), $this->project, 'ecr_archive');

        if(in_array($this->task, get_class_methods($this)))
        {
            //--Execute the task
            $this->{$this->task}();
        }

        //--Draw the submenu
        echo $this->displayBar();

        parent::display($tpl);

        ecrHTML::easyFormEnd();
    }//function

    /**
     * Zipper view.
     *
     * @return void
     */
    private function ziper()
    {
        $this->setLayout('ziper');
    }//function

    /**
     * Archive view.
     *
     * @return void
     */
    private function archive()
    {
        $this->setLayout('archive');
    }//function

    /**
     * Zips the project.
     *
     * @return void
     */
    private function ziperzip()
    {
        ecrLoadHelper('ziper');

        $result = new stdClass;

        $this->buildopts = JRequest::getVar('buildopts', array());

        $ziper = new EasyZIPer;

        $result->result = $ziper->create($this->project);
        $result->errors = $ziper->getErrors();

        $result->downloadLinks = $ziper->getDownloadLinks();
        $result->log = $ziper->printLog();

        $this->zipResult = $result;

        $this->task = 'ziper';

        $this->setLayout('ziper');
    }//function

    /**
     * Deletes a zip file.
     *
     * @return void
     */
    private function delete()
    {
        $this->setLayout('ziper');
    }//function

    /**
     * Displays the submenu.
     *
     * @return string html
     */
    private function displayBar()
    {
        $subTasks = array(
        array('title' => jgettext('Package')
        , 'description' => jgettext('Automatically create a package of your extension.')
        , 'icon' => 'package'
        , 'task' => 'ziper'
        )
        , array('title' => jgettext('Archive')
        , 'description' => jgettext('View archived versions of your extension.')
        , 'icon' => 'archive'
        , 'task' => 'archive'
        )
        );

        return ecrHTML::getSubBar($subTasks);
    }//function
}//class
