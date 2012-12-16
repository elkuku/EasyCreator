<?php
##*HEADER*##

jimport('joomla.plugin.plugin');

/**
 * ECR_COM_NAME Extension Plugin.
 *
 * @package    ECR_COM_NAME
 * @subpackage Plugin
 */
class plgExtensionECR_COM_NAME extends JPlugin
{
    /**
     * Handle post extension install update sites
     *
     * @param	JInstaller	Installer object
     * @param	int			Extension Identifier
     * @since	1.6
     */
    public function onExtensionAfterInstall($installer, $eid)
    {
        $msg = '';

        $msg .=($eid === false)
        ? 'Failed extension install: '.$installer->getError()
        : 'Extension install successful';

        $msg .=($eid)
        ? ' with new extension ID '.$eid
        : ' with no extension ID detected or multiple extension IDs assigned';

        JError::raiseWarning(-1, __METHOD__.': '.$msg);
    }//function

    /**
     * Allow to processing of extension data after it is saved.
     *
     * @param object $data The data representing the extension.
     * @param boolean True if this is new data, false if it is existing data.
     */
    public function onExtensionAfterSave($data, $isNew)
    {
    }//function

    /**
     * Handle extension uninstall
     *
     * @param $installer JInstaller Installer instance
     * @param $eid, integer Extension id
     * @param $result integer Installation result
     */
    public function onExtensionAfterUninstall($installer, $eid, $result)
    {
        $msg = '';

        $msg .= 'Uninstallation of '.$eid.' was a ';
        $msg .=($result) ? 'success' : 'failure';

        JError::raiseWarning(-1, __METHOD__.': '.$msg);
    }//function

    /**
     * After update of an extension
     *
     * @param	JInstaller	Installer object
     * @param	int			Extension identifier
     * @since	1.6
     */
    public function onExtensionAfterUpdate($installer, $eid)
    {
        $msg = '';

        $msg .=($eid === false)
        ? 'Failed extension update: '.$installer->getError()
        : 'Extension update successful';

        $msg .=($eid)
        ? ' with updated extension ID '.$eid
        : ' with no extension ID detected or multiple extension IDs assigned';

        JError::raiseWarning(-1, __METHOD__.': '.$msg);
    }//function

    /**
     * @since	1.6
     */
    public function onExtensionBeforeInstall($method, $type, $manifest, $eid)
    {
        $msg = '';

        $msg .= 'Installing '.$type.' from '.$method;
        $msg .=($method == 'install') ? ' with manifest supplied' : ' using discovered extension ID '.$eid;

        JError::raiseWarning(-1, __METHOD__.': '.$msg);
    }//function

    /**
     * Allow to processing of extension data before it is saved.
     *
     * @param	object	The data representing the extension.
     * @param	boolean	True is this is new data, false if it is existing data.
     * @since	1.6
     */
    public function onExtensionBeforeSave($data, $isNew)
    {
    }//function

    /**
     * @param	int			extension id
     * @since	1.6
     */
    public function onExtensionBeforeUninstall($eid)
    {
        JError::raiseWarning(-1, __METHOD__.': Uninstalling '.$eid);
    }//function

    /**
     * @since	1.6
     */
    public function onExtensionBeforeUpdate($type, $manifest)
    {
        JError::raiseWarning(-1, __METHOD__.': Updating a '.$type);
    }//function
}//class
