<?php
/**
 * @package    LanguageChecker
 * @subpackage Base
 * @author     Nikolai Plath {@link http://easy-joomla.org}
 * @author     Created on 12-Sep-2010
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Languages Controller.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * @since		1.5
 */
class LanguageCheckerController extends JController
{
    /**
     * @var		string	The default view.
     * @since	1.6
     */
//    #protected $default_view = 'installed';

    /**
     * task to display the view
     */
    public function display()
    {
        require_once JPATH_COMPONENT.'/helpers/languagechecker.php';

        // Load the submenu.
        LanguageCheckerHelper::addSubmenu(JRequest::getCmd('view'));

        parent::display();
    }//function
}//class
