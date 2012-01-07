<?php
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 23-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.controller');

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerLogfiles extends JController
{
    /**
     * Standard display method.
     *
     * @param boolean $cachable If true, the view output will be cached
     * @param array $urlparams An array of safe url parameters and their variable types,
     * for valid values see {@link JFilterInput::clean()}.
     *
     * @return void
     * @see JController::display()
     */
    public function display($cachable = false, $urlparams = false)
    {
        JRequest::setVar('view', 'logfiles');
        parent::display($cachable, $urlparams);
    }//function

    /**
     * Deletes ALL log files (no warning..).
     *
     * @return void
     */
    public function clear_log()
    {
        $logfiles = JFolder::files(ECRPATH_LOGS, 'log', false, true);

        if(count($logfiles))
        {
            if(JFile::delete($logfiles))
            {
                JFactory::getApplication()->enqueueMessage(jgettext('The logfiles have been deleted'));
                JRequest::setVar('view', 'easycreator');
            }
            else
            {
                JError::raiseWarning(100, jgettext('The logfiles could not be deleted'));
                JRequest::setVar('view', 'logfiles');
            }
        }

        parent::display();
    }//function
}//class
