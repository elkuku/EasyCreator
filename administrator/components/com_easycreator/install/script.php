<?php
/**
 * @package    EasyCreator
 * @subpackage Installer
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 16-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Script file for _ECR_COM_NAME_ component.
 */
class Com_EasyCreatorInstallerScript
{
    /**
     * Method to run before an install/update/uninstall method.
     *
     * @return void
     */
    public function preflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)

            JFactory::getDocument()->addStylesheet(JURI::root(true)
    .'/administrator/components/com_easycreator/assets/css/default.css');

        $PHPMinVersion = '5.2.4';

        if(version_compare(PHP_VERSION, $PHPMinVersion, '<'))
        {
            JFactory::getApplication()->enqueueMessage(sprintf('This script requires at least PHP version %s'
            , $PHPMinVersion), 'error');//@Do_NOT_Translate

            return false;
        }
    }//function

    /**
     * Method to install the component.
     *
     * @return void
     */
    public function install($parent)
    {
        // $parent is the class calling this method
        //    $parent->getParent()->setRedirectURL('index.php?option=_ECR_COM_COM_NAME_');
        //echo '<p>'.JText::_('_ECR_UPPER_COM_COM_NAME__INSTALL_TEXT').'</p>';
    }//function

    /**
     * Method to update the component. DISABLED...
     *
     * @return void
     */
    public function NOupdate($parent)
    {
        // $parent is the class calling this method
        //echo '<p>'.JText::_('_ECR_UPPER_COM_COM_NAME__UPDATE_TEXT').'</p>';
    }//function

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @return void
     */
    public function postflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        //echo '<p>'.JText::_('_ECR_UPPER_COM_COM_NAME__POSTFLIGHT_'.$type.'_TEXT').'</p>';
    }//function

    /**
     * Method to uninstall the component.
     *
     * @return void
     */
    public function uninstall($parent)
    {
        echo '<p>EasyCreator has been removed from your system -- you\'re on your own now... :( :( :(</p>';
        // $parent is the class calling this method
        //echo '<p>'.JText::_('_ECR_UPPER_COM_COM_NAME__UNINSTALL_TEXT').'</p>';
    }//function
}//class
