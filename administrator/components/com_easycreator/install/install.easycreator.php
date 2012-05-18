<?php
/**
 * @package    EasyCreator
 * @subpackage Installer
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 30-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

defined('NL') || define('NL', "\n");

define('ECR_XML_LOCATION', $this->parent->getPath('manifest'));
//define('ECR_XML_LOCATION', JPATH_COMPONENT_ADMINISTRATOR.'/easycreator.xml');

/**
 * Main installer.
 *
 * @deprecated @todo remove when J! 1.5 is dropped.
 *
 * @return boolean
 */
function com_install()
{
  //  define('EASY_CREATOR_INSTALL_SCRIPT_HAS_BEEN_EXECUTED', 1);

    $PHPMinVersion = '5.2.4';

    if(version_compare(PHP_VERSION, $PHPMinVersion, '<'))
    {
        JFactory::getApplication()->enqueueMessage(sprintf('This script requires at least PHP version %s'
            , $PHPMinVersion), 'error');
        //@Do_NOT_Translate

        return false;
    }

    try
    {
        //-- @todo remove JFolder::exists when dropping 1.5 support
        if(! JFolder::exists(JPATH_LIBRARIES.'/g11n')
            || ! jimport('g11n.language')
        )
        {
            //-- Get our special language file
            JLoader::import('helpers.g11n_dummy', JPATH_ADMINISTRATOR.'/components/com_easycreator');
            ?>
        <div style="padding: 0.3em; background-color: #ffc;">
            <h3 style="color: red;">EasyCreator is in "English ONLY" mode !</h3>

            <h3 style="color: red;">
                If you like EasyCreator in your language, just install the g11n language library :
            </h3>

            <h3 style="color: red;">
                <a href="http://joomlacode.org/gf/project/elkuku/frs/?action=FrsReleaseBrowse&frs_package_id=5915">
                    Download lib_g11n
                </a>
            </h3>
        </div>
        <?php
        }
        else
        {
            g11n::loadLanguage('com_easycreator');
        }

        if(! $xml = simplexml_load_file(ECR_XML_LOCATION))
        {
            JFactory::getApplication()->enqueueMessage(jgettext('Install manifest not found'), 'error');

            return false;
        }
    }
    catch(Exception $e)
    {
        JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

        return false;
    }

    define('ECR_VERSION', $xml->version);

    require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easycreator'.DS.'helpers'.DS.'html.php';

    JFactory::getDocument()->addStyleSheet(JURI::root().'media/com_easycreator/admin/css/default.css');
    JFactory::getDocument()->addStyleSheet(JURI::root().'media/com_easycreator/admin/css/icon.css');
    ?>

<div>

    <div style="float: right">
        <img
            src="<?php echo JURI::root(true); ?>/media/com_easycreator/admin/images/ico/icon-128-easycreator.png"
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
    ##ECR_MD5CHECK##

    EcrHtml::footer();

    return true;
}//function
##ECR_MD5CHECK_FNC##
