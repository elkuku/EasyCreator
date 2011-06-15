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
class EasyCreatorViewStarter extends JView
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
        $task = JRequest::getCmd('task');

        ecrLoadHelper('builder');
        $this->EasyBuilder = new EasyBuilder;

        ecrStylesheet('wizard');
        ecrScript('wizard');

        //--JS for changing loader pic
        $img_base = JURI::root().'administrator/components/com_easycreator/assets/images';

        $js = "var loaderPic = new Image(); loaderPic.src = '$img_base/ajax-loader2.gif';";
        JFactory::getDocument()->addScriptDeclaration($js);

        $params = JComponentHelper::getParams('com_easycreator');

        $project = EasyProjectHelper::newProject('empty');

        $project->type = JRequest::getCmd('tpl_type', '', 'post');
        $project->tplName = JRequest::getCmd('tpl_name', '', 'post');
        $project->version = JRequest::getVar('version', '1.0', 'post');
        $project->description = JRequest::getVar('description', '', 'post');
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

        if($task != 'starter'
        && $task != 'wizard'
        && $task)
        {
            $this->setLayout($task);
        }

        parent::display($tpl);

        ecrHTML::easyFormEnd();
    }//function
}//class
