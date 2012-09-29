<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerRegister extends JControllerLegacy
{
    /**
     * Register a project.
     *
     * @return void
     */
    public function register()
    {
        JFactory::getApplication()->input->set('view', 'register');

        parent::display();
    }//function
}//class
