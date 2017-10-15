<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Installer
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 16-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Script file for EasyCreator component.
 *
 * @since 0.0.1
 */
class Com_EasyCreatorInstallerScript
{
    /**
     * Method to install the component.
     *
     * @param $parent
     *
     * @since 0.0.1
     *
     * @return boolean
     */
    public function install($parent)
    {
        $logo = JURI::root(true) . '/media/com_easycreator/admin/images/ico/icon-128-easycreator.png';

        try
        {
	        // Load a dummy language loader - @todo REMOVE
            JLoader::import('helpers.g11n_dummy', JPATH_ADMINISTRATOR . '/components/com_easycreator');

            $xml = simplexml_load_file($parent->getParent()->getPath('manifest'));

            if (false == $xml) {
                JFactory::getApplication()->enqueueMessage(jgettext('Install manifest not found'), 'error');

                return false;
            }
        } catch (Exception $e) {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

            return false;
        }

        define('ECR_VERSION', $xml->version);

        require_once JPATH_ADMINISTRATOR . '/components/com_easycreator/helpers/html.php';

        JFactory::getDocument()->addStyleSheet(JURI::root() . 'media/com_easycreator/admin/css/default.css');
        JFactory::getDocument()->addStyleSheet(JURI::root() . 'media/com_easycreator/admin/css/icon.css');
        ?>

        <div>

            <div style="float: right">
                <img
                        src="<?php echo $logo; ?>"
                        alt="EasyCreator Logo" title="EasyCreator Logo"/>
            </div>

            <h1>EasyCreator</h1>
            <?php echo jgettext('EasyCreator is a developer tool.'); ?><br/>
            <?php echo jgettext('It tries to speed up the developing process of custom Joomla! extensions.'); ?><br/>
            <?php echo jgettext('You can create a "frame" for your extension and an installable zip package with just a few "clicks"'); ?>

            <p>Happy coding,<br/>
                <?php echo sprintf(jgettext('The %s Team.'), '<a href="https://github.com/elkuku/EasyCreator">EasyCreator</a>'); ?>
            </p>

        </div>

        <h3 style="color: orange;">
            <?php echo jgettext('Please use this extension only in local development environments.'); ?>
        </h3>
        <p>
            <?php echo sprintf(jgettext('See: <a %s>docs.joomla.org/Setting up your workstation for Joomla! development</a>')
                , 'href="http://docs.joomla.org/Setting_up_your_workstation_for_Joomla!_development"'); ?>
        </p>

        <?php

        EcrHtml::footer();
    }

    /**
     * Method to uninstall the component.
     *
     * @param $parent
     *
     * @since 0.0.1
     *
     * @return void
     */
    public function uninstall($parent)
    {
        echo '<div class="alert alert-error">';
        echo '<h2>EasyCreator has been removed from your system</h2>';
        echo '<h3>you\'re on your own now... :(</h3>';
        echo '</div>';
    }
}
