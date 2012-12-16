<?php
##*HEADER*##

jimport('joomla.plugin.plugin');

/**
 * ECR_COM_NAME Extension Plugin.
 *
 * @package    ECR_COM_NAME
 * @subpackage Plugin
 */
class PlgExtensionECR_COM_NAME extends JPlugin
{
    /**
     * Handle post extension install update sites
     *
     * @param  JInstaller  $installer  Installer object
     * @param  integer     $eid        Extension Identifier
     *
     * @return  void
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

        JLog::add($msg, JLog::INFO, __METHOD__);
    }

    /**
     * Allow to processing of extension data after it is saved.
     *
     * @param object   $data   The data representing the extension.
     * @param boolean  $isNew  True if this is new data, false if it is existing data.
     *
     * @return  void
     */
    public function onExtensionAfterSave($data, $isNew)
    {
        JLog::add('onExtensionAfterSave', JLog::INFO, __METHOD__);
    }

    /**
     * Handle extension uninstall.
     *
     * @param  JInstaller  $installer Installer instance
     * @param  integer     $eid       Extension id
     * @param  integer     $result    Installation result
     *
     * @return  void
     */
    public function onExtensionAfterUninstall($installer, $eid, $result)
    {
        $msg = '';

        $msg .= 'Uninstallation of '.$eid.' was a ';
        $msg .=($result) ? 'success' : 'failure';

        JLog::add($msg, JLog::INFO, __METHOD__);
    }

    /**
     * After update of an extension.
     *
     * @param  JInstaller  $installer  Installer object
     * @param  integer     $eid        Extension identifier
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

        JLog::add($msg, JLog::INFO, __METHOD__);
    }

    /**
     * @param $method
     * @param $type
     * @param $manifest
     * @param $eid
     *
     * @return  void
     */
    public function onExtensionBeforeInstall($method, $type, $manifest, $eid)
    {
        $msg = '';

        $msg .= 'Installing '.$type.' from '.$method;
        $msg .=($method == 'install') ? ' with manifest supplied' : ' using discovered extension ID '.$eid;

        JLog::add($msg, JLog::INFO, __METHOD__);
    }

    /**
     * Allow to processing of extension data before it is saved.
     *
     * @param  object   $data   The data representing the extension.
     * @param  boolean  $isNew  True is this is new data, false if it is existing data.
     * @return  void
     */
    public function onExtensionBeforeSave($data, $isNew)
    {
        JLog::add('onExtensionBeforeSave', JLog::INFO, __METHOD__);
    }

    /**
     * @param  integer  $eid  extension id
     *
     * @return  void
     */
    public function onExtensionBeforeUninstall($eid)
    {
        JLog::add('Uninstalling '.$eid, JLog::INFO, __METHOD__);
    }

    /**
     * @param $type
     * @param $manifest
     *
     * @return  void
     */
    public function onExtensionBeforeUpdate($type, $manifest)
    {
        JLog::add('Updating a '.$type, JLog::INFO, __METHOD__);
    }
}
