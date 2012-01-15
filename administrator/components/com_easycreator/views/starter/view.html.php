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
 * @package EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewStarter extends JView
{
    protected $infoLinks = array();

    /**
     * Standard display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $task = JRequest::getCmd('task');

        ecrLoadHelper('builder');

        $this->EasyBuilder = new EasyBuilder;

        ecrStylesheet('wizard');
        ecrScript('wizard');

        ecrLoadHelper('easytemplatehelper');

        //--JS for changing loader pic
        $img_base = JURI::root().'administrator/components/com_easycreator/assets/images';

        $js = "var loaderPic = new Image(); loaderPic.src = '$img_base/ajax-loader2.gif';";
        JFactory::getDocument()->addScriptDeclaration($js);

        $params = JComponentHelper::getParams('com_easycreator');

        $this->templateList = EasyTemplateHelper::getTemplateList();

        $tplType = JRequest::getCmd('tpl_type');
        $tplFolder = JRequest::getCmd('tpl_name');

        $desc = isset($this->templateList[$tplType][$tplFolder])
        ? $this->templateList[$tplType][$tplFolder]->description
        : '';

        $project = EasyProjectHelper::newProject('empty');

        $project->type = JRequest::getCmd('tpl_type', '', 'post');
        $project->tplName = JRequest::getCmd('tpl_name', '', 'post');
        $project->version = JRequest::getVar('version', '1.0', 'post');
        $project->description = JRequest::getVar('description', $desc, 'post');
        $project->listPostfix = JRequest::getCmd('list_postfix', 'List', 'post');
        $project->JCompat = JRequest::getVar('jcompat', '', 'post');

        //--Sanitize project name
        $project->name = JRequest::getCmd('com_name', '', 'post');
        $disallows = array('_');
        $project->name = str_replace($disallows, '', $project->name);

        //--Credits
        $s = JRequest::getVar('author', '', 'post');
        $project->author =($s) ? $s : $params->get('cred_author');
        $s = JRequest::getVar('authorEmail', '', 'post');
        $project->authorEmail =($s) ? $s : $params->get('cred_author_email');
        $s = JRequest::getVar('authorUrl', '', 'post');
        $project->authorUrl =($s) ? $s : $params->get('cred_author_url');
        $s = JRequest::getVar('license', '', 'post');
        $project->license =($s) ? $s : $params->get('cred_license');
        $s = JRequest::getVar('copyright', '', 'post');
        $project->copyright =($s) ? $s : $params->get('cred_copyright');

        $this->assignRef('project', $project);

        if($task && method_exists($this, $task))
        $this->$task();

        parent::display($tpl);

        EcrHtml::easyFormEnd();
    }//function

    private function starter()
    {
        $this->setUpInfoLinks();
    }//function

    private function wizard()
    {
        $this->setUpInfoLinks();
    }//function

    private function wizard2()
    {
        $this->setLayout('wizard2');
    }//function

    private function wizard3()
    {
        $this->setLayout('wizard3');
    }//function

    private function setUpInfoLinks()
    {
        $docBase = 'http://docs.joomla.org/';

        $this->infoLinks = array(
        'component' => array(
            'Category:Components'
        => $docBase.'Category:Components'
        )

        , 'module' => array(
          'Category:Modules' => $docBase.'Category:Modules'
        , 'Creating a Hello World Module'
        => $docBase.'Tutorial:Creating_a_Hello_World_Module_for_Joomla_1.5'
        )

        , 'plugin' => array(
          'Category:Plugins' => $docBase.'Category:Plugins'
        , 'Creating a Plugin for Joomla 1.5' => $docBase.'Tutorial:Creating_a_Plugin_for_Joomla_1.5'
        , 'How to create a content plugin' => $docBase.'How_to_create_a_content_plugin'
        , 'How to create a search plugin' => $docBase.'How_to_create_a_search_plugin'
        , 'How to create a system plugin' => $docBase.'How_to_create_a_system_plugin'
        , 'Joomla System Plugin Specification' => $docBase.'Reference:Joomla_System_Plugin_Specification'
        )

        , 'template' => array(
          'Category:Templates' => $docBase.'Category:Templates'
        , 'Category:Template_FAQ' => $docBase.'Category:Template_FAQ'
        , 'How to override the output from the Joomla! core'
        => $docBase.'How_to_override_the_output_from_the_Joomla!_core'
        , 'The Joomla! CSS explained' => 'http://www.joomla-css.nl'
        )

        , 'library' => array()

        , 'package' => array()
        );
    }//function
}//class
