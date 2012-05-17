<?php
/**
 * @package    EasyCreator
 * @subpackage Help
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
class EasyCreatorViewHelp extends JView
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

        $help_path = JPATH_COMPONENT.DS.'assets'.DS.'help';

        switch($task)
        {
            case 'jhelp':
                $help_file = '';
                $this->setLayout('jhelp');
                break;

            case 'help':
            default:
                $help_file = 'help.php';
                break;
        }//switch

        parent::display($tpl);
        EcrHtml::formEnd();
    }//function
}//class
