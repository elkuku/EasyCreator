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
 * Enter description here ...@todo class comment.
 *
 */
class EasyCreatorViewZiper extends JView
{
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

        $task = JRequest::getCmd('task');
        $this->ecr_project = JRequest::getCmd('ecr_project');

        //--Get the project
        try
       {
            $this->project = EasyProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            $m =(JDEBUG || ECR_DEBUG) ? nl2br($e) : $e->getMessage();

            ecrHTML::displayMessage($m, 'error');

            ecrHTML::easyFormEnd();

            return;
        }//try

        //-- Draw h1 header
        ecrHTML::header(jgettext('Component ZIPer'), $this->project, 'ecr_archive');

        if(in_array($task, get_class_methods($this)))
        {
            //--Execute the task
            $this->$task();
        }

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
     * Zips the project.
     *
     * @return void
     */
    private function ziperzip()
    {
        ecrLoadHelper('ziper');

        $this->buildopts = JRequest::getVar('buildopts', array());

        $this->EasyZiper = new EasyZIPer;

        $this->setLayout('ziperresult');
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
}//class
