<?php
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
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
class EasyCreatorControllerHelp extends JController
{
	/**
	 * Standard display method.
	 *
	 * @param boolean    $cachable  If true, the view output will be cached
	 * @param array|bool $urlparams An array of safe url parameters and their variable types,
	 *                              for valid values see {
	 *
	 * @link JFilterInput::clean()}.
	 *
	 * @return void
	 * @see  JController::display()
	 */
    public function display($cachable = false, $urlparams = false)
    {
        JRequest::setVar('view', 'help');

        parent::display($cachable, $urlparams);
    }//function
}//class
