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
 * @package    EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewStarter extends JViewLegacy
{
    protected $infoLinks = array();

    /**
     * @var EcrProjectBuilder
     */
    protected $builder;

    protected $templateList;

    protected $notes = array();

    /**
     * Standard display method.
     *
     * @param null|string $tpl The name of the template file to parse;
     *
     * @return mixed|void
     */
    public function display($tpl = null)
    {
        $input = JFactory::getApplication()->input;

        $task = $input->get('task');

        $this->builder = new EcrProjectBuilder;

        ecrLoadMedia('wizard');

        $params = JComponentHelper::getParams('com_easycreator');

        $this->templateList = EcrProjectTemplateHelper::getTemplateList();

        $tplType = $input->get('tpl_type');
        $tplFolder = $input->get('tpl_name');

        $desc = isset($this->templateList[$tplType][$tplFolder])
            ? $this->templateList[$tplType][$tplFolder]->description
            : '';

        $project = EcrProjectHelper::newProject('empty');

        $project->type = $input->get('tpl_type');
        $project->tplName = $input->get('tpl_name');
        $project->version = $input->getString('version', '1.0');
        $project->description = $input->getString('description', $desc);
        $project->listPostfix = $input->get('list_postfix', 'List');
        $project->JCompat = $input->getString('jcompat');

        //-- Sanitize project name
        $project->name = $input->get('com_name');
        $disallows = array('_');
        $project->name = str_replace($disallows, '', $project->name);

        //-- Credits
        $s = $input->getString('author');
        $project->author = ($s) ? $s : $params->get('cred_author');
        $s = $input->getString('authorEmail');
        $project->authorEmail = ($s) ? $s : $params->get('cred_author_email');
        $s = $input->getString('authorUrl');
        $project->authorUrl = ($s) ? $s : $params->get('cred_author_url');
        $s = $input->getString('license');
        $project->license = ($s) ? $s : $params->get('cred_license');
        $s = $input->getString('copyright');
        $project->copyright = ($s) ? $s : $params->get('cred_copyright');

        $this->project = $project;

        if($task && method_exists($this, $task))
            $this->$task();

        parent::display($tpl);

        EcrHtml::formEnd();
    }

    private function starter()
    {
        $this->setUpInfoLinks();
    }

    private function wizard()
    {
        $this->setUpInfoLinks();
    }

    private function wizard2()
    {
        $this->setLayout('wizard2');
    }

    private function wizard3()
    {
        $this->setLayout('wizard3');
    }

    private function setUpInfoLinks()
    {
        $docBase = 'http://docs.joomla.org/';

        $this->infoLinks = array(
            'component' => array(
                'JDocs - Component' => $docBase.'Component'
            , 'JDocs - Category:Components' => $docBase.'Category:Components'
            )

        , 'module' => array(
                'JDocs - Module' => $docBase.'Modules'
            , 'JDocs - Category:Modules' => $docBase.'Category:Modules'
            , 'JDocs - Creating a Hello World Module'
                => $docBase.'Tutorial:Creating_a_Hello_World_Module_for_Joomla_1.5'
            )

        , 'plugin' => array(
                'JDocs - Plugin' => $docBase.'Plugin'
            , 'JDocs - Category:Plugins' => $docBase.'Category:Plugins'
            , 'JDocs - Creating a Plugin for Joomla 1.5' => $docBase.'Tutorial:Creating_a_Plugin_for_Joomla_1.5'
            , 'JDocs - How to create a content plugin' => $docBase.'How_to_create_a_content_plugin'
            , 'JDocs - How to create a search plugin' => $docBase.'How_to_create_a_search_plugin'
            , 'JDocs - How to create a system plugin' => $docBase.'How_to_create_a_system_plugin'
            , 'JDocs - Joomla System Plugin Specification' => $docBase.'Reference:Joomla_System_Plugin_Specification'
            )

        , 'template' => array(
                'JDocs - Template' => $docBase.'Template'
            , 'JDocs - Category:Templates' => $docBase.'Category:Templates'
            , 'JDocs - Category:Template_FAQ' => $docBase.'Category:Template_FAQ'
            , 'JDocs - How to override the output from the Joomla! core'
                => $docBase.'How_to_override_the_output_from_the_Joomla!_core'
            , 'The Joomla! CSS explained' => 'http://www.joomla-css.nl'
            )

        , 'library' => array(
                'JDocs - Library' => $docBase.'Library'
            )

        , 'package' => array(
                'JDocs - Package' => $docBase.'Package')

        , 'cliapp' => array(
                'JDocs - Create a stand-alone application'
                => $docBase.'How_to_create_a_stand-alone_application_using_the_Joomla!_Platform'
            , 'JDocs - Tips and Tricks'
                => $docBase.'Platform_Applications_Tips_and_Tricks'
            )

        , 'webapp' => array(
                'JDocs - Create a stand-alone application'
                => $docBase.'How_to_create_a_stand-alone_application_using_the_Joomla!_Platform'
            , 'JDocs - Tips and Tricks'
                => $docBase.'Platform_Applications_Tips_and_Tricks'
            )
        );

        $this->notes = array(
            'component' => ''
        , 'module' => ''
        , 'plugin' => ''
        , 'template' => ''
        , 'library' => ''
        , 'package' => jgettext('Packages are containers to group other extension types')
        , 'cliapp' => jgettext('JApplications created with EasyCreator will look for an environment variable called JOOMLA_PATFORM_PATH. Otherwise you have to adjust the path in the entry file by hand !')
        , 'webapp' => jgettext('JApplications created with EasyCreator will look for an environment variable called JOOMLA_PATFORM_PATH. Otherwise you have to adjust the path in the entry file by hand !')
        );
    }
}//class
