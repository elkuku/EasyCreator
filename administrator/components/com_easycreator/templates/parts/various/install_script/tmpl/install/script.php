<?php
##*HEADER*##

/**
 * Script file for _ECR_COM_NAME_ component.
 */
class _ECR_COM_COM_NAME_InstallerScript
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
        echo '<p>'.JText::_('_ECR_UPPER_COM_COM_NAME__PREFLIGHT_'.$type.'_TEXT').'</p>';
    }//function

    /**
     * Method to install the component.
     *
     * @return void
     */
    public function install($parent)
    {
        // $parent is the class calling this method
        //	$parent->getParent()->setRedirectURL('index.php?option=_ECR_COM_COM_NAME_');
        echo '<p>'.JText::_('_ECR_UPPER_COM_COM_NAME__INSTALL_TEXT').'</p>';
    }//function

    /**
     * Method to update the component.
     *
     * @return void
     */
    public function update($parent)
    {
        // $parent is the class calling this method
        echo '<p>'.JText::_('_ECR_UPPER_COM_COM_NAME__UPDATE_TEXT').'</p>';
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
        echo '<p>'.JText::_('_ECR_UPPER_COM_COM_NAME__POSTFLIGHT_'.$type.'_TEXT').'</p>';
    }//function

    /**
     * Method to uninstall the component.
     *
     * @return void
     */
    public function uninstall($parent)
    {
        // $parent is the class calling this method
        echo '<p>'.JText::_('_ECR_UPPER_COM_COM_NAME__UNINSTALL_TEXT').'</p>';
    }//function
}//class
