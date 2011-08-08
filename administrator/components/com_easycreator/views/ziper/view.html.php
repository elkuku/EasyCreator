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
            $m =(JDEBUG || ECR_DEBUG) ? nl2br($e) : $e->getMessage();

            ecrHTML::displayMessage($m, 'error');

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
        //--Setup debugger
        $ecr_help = JComponentHelper::getParams('com_easycreator')->get('ecr_help');

        $subtasks = array(
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
