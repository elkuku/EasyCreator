<?php
##*HEADER*##

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class _ECR_COM_COM_NAME_InstallerScript
{
    public function install($parent)
    {
        echo '<p>'.JText::_('_ECR_COM_COM_NAME__CUSTOM_INSTALL_SCRIPT').'</p>';
    }//function

    public function uninstall($parent)
    {
        echo '<p>'.JText::_('_ECR_COM_COM_NAME__CUSTOM_UNINSTALL_SCRIPT').'</p>';
    }//function

    public function update($parent)
    {
        echo '<p>'.JText::_('_ECR_COM_COM_NAME__CUSTOM_UPDATE_SCRIPT').'</p>';
    }//function

    public function preflight($type, $parent)
    {
        echo '<p>'.JText::sprintf('_ECR_COM_COM_NAME__CUSTOM_PREFLIGHT', $type).'</p>';
    }//function

    public function postflight($type, $parent)
    {
        echo '<p>'.JText::sprintf('_ECR_COM_COM_NAME__CUSTOM_POSTFLIGHT', $type).'</p>';
        /*
         * An example of setting a redirect to a new location after the install is completed
         * $parent->getParent()->set('redirect_url', 'http://www.example.com');
         */
    }//function
}//class
