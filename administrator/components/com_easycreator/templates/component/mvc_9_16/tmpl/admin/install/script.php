<?php
/**
 * Script file of HelloWorld component.
 */
class _ECR_COM_COM_NAME_InstallerScript
{
    /**
     * method to install the component
     *
     * @return void
     */
    public function install($parent)
    {
        // $parent is the class calling this method
        //	$parent->getParent()->setRedirectURL('index.php?option=com_helloworld');
        echo '<p>'.JText::_('COM_HELLOWORLD_INSTALL_TEXT').'</p>';
    }//function

    /**
     * method to uninstall the component
     *
     * @return void
     */
    public function uninstall($parent)
    {
        // $parent is the class calling this method
        echo '<p>'.JText::_('COM_HELLOWORLD_UNINSTALL_TEXT').'</p>';
    }//function

    /**
     * method to update the component
     *
     * @return void
     */
    public function update($parent)
    {
        // $parent is the class calling this method
        echo '<p>'.JText::_('COM_HELLOWORLD_UPDATE_TEXT').'</p>';
    }//function

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    public function preflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        echo '<p>'.JText::_('COM_HELLOWORLD_PREFLIGHT_'.$type.'_TEXT').'</p>';
    }//function

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    public function postflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        echo '<p>'.JText::_('COM_HELLOWORLD_POSTFLIGHT_'.$type.'_TEXT').'</p>';
    }//function
}//class
