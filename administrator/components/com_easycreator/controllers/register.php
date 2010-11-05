<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 24-Mar-2010
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
class EasyCreatorControllerRegister extends JController
{
    /**
     * Register a project.
     *
     * @return void
     */
    public function register()
    {
        JRequest::setVar('view', 'register');

        parent::display();
    }//function
}//class
