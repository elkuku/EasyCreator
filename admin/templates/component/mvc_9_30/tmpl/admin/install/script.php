<?php
##*HEADER*##

/**
 * Script file for ECR_COM_NAME component.
 */
class ECR_COM_COM_NAMEInstallerScript
{
    /**
     * Method to run before an install/update/uninstall method.
     *
     * @param $type
     * @param $parent
     *
     * @return void
     */
    public function preflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        echo '<p>'.JText::_('ECR_UPPER_COM_COM_NAME_PREFLIGHT_'.$type.'_TEXT').'</p>';
    }

    /**
     * Method to install the component.
     *
     * @param $parent
     *
     * @return void
     */
    public function install($parent)
    {
        // $parent is the class calling this method
        //	$parent->getParent()->setRedirectURL('index.php?option=ECR_COM_COM_NAME');
        echo '<p>'.JText::_('ECR_UPPER_COM_COM_NAME_INSTALL_TEXT').'</p>';
    }

    /**
     * Method to update the component.
     *
     * @param $parent
     *
     * @return void
     */
    public function update($parent)
    {
        // $parent is the class calling this method
        echo '<p>'.JText::_('ECR_UPPER_COM_COM_NAME_UPDATE_TEXT').'</p>';
    }

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @param $type
     * @param $parent
     *
     * @return void
     */
    public function postflight($type, $parent)
    {
        // $parent is the class calling this method
        // $type is the type of change (install, update or discover_install)
        echo '<p>'.JText::_('ECR_UPPER_COM_COM_NAME_POSTFLIGHT_'.$type.'_TEXT').'</p>';
    }

    /**
     * Method to uninstall the component.
     *
     * @param $parent
     *
     * @return void
     */
    public function uninstall($parent)
    {
        // $parent is the class calling this method
        echo '<p>'.JText::_('ECR_UPPER_COM_COM_NAME_UNINSTALL_TEXT').'</p>';
    }
}
