<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Base
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 06-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/*
 This is our SQL INSERT query (for manual install)

** J! 1.5
INSERT INTO `#__components`
(`name`, `link`, `menuid`, `parent`, `admin_menu_link`
, `admin_menu_alt`, `option`, `ordering`
, `admin_menu_img`, `iscore`, `params`, `enabled`)
VALUES
('EasyCreator', 'option=com_easycreator', 0, 0, 'option=com_easycreator'
, 'EasyCreator', 'com_easycreator', 0
, 'components/com_easycreator/assets/images/ico/icon-16-easycreator.png', 0, '', 1);

** J! >= 1.6 +
Use the new 'Discover' feature from the Joomla! installer - works great =;)
*/

/*
$file = '/home/elkuku/test/foo2.txt';

$ch = curl_init();

$data = array(
    'name' => JFile::getName($file)
, 'size' => filesize($file)
);

$data = json_encode($data);

$uri = 'https://api.github.com';
$user = 'jlover';
$repo = 'AggaBagga';

$path = "repos/$user/$repo/downloads";

curl_setopt($ch, CURLOPT_URL, $uri.'/'.$path);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_USERPWD, "jlover:kuku666");

$result = curl_exec($ch);

var_dump($result);
$decoded = json_decode($result);

var_dump($decoded);

curl_close($ch);
/*
 *  public 'download_count' => int 0
  public 'html_url' => string 'https://github.com/downloads/jlover/AggaBagga/Foo.jpg' (length=53)
  public 'bucket' => string 'github' (length=6)
  public 'redirect' => boolean false
  public 'mime_type' => string 'image/jpeg' (length=10)
  public 'content_type' => string 'image/jpeg' (length=10)
  public 'prefix' => string 'downloads/jlover/AggaBagga' (length=26)
  public 'acl' => string 'public-read' (length=11)
  public 'accesskeyid' => string '1DWESVTPGHQVTX38V182' (length=20)
  public 'size' => int 122880
  public 'created_at' => string '2012-04-24T03:19:01Z' (length=20)
  public 'policy' => string 'ewogICAgJ2V4cGlyYXRpb24nOiAnMjExMi0wNC0yNFQwMzoxOTowMS4wMDBaJywKICAgICdjb25kaXRpb25zJzogWwogICAgICAgIHsnYnVja2V0JzogJ2dpdGh1Yid9LAogICAgICAgIHsna2V5JzogJ2Rvd25sb2Fkcy9qbG92ZXIvQWdnYUJhZ2dhL0Zvby5qcGcnfSwKICAgICAgICB7J2FjbCc6ICdwdWJsaWMtcmVhZCd9LAogICAgICAgIHsnc3VjY2Vzc19hY3Rpb25fc3RhdHVzJzogJzIwMSd9LAogICAgICAgIFsnc3RhcnRzLXdpdGgnLCAnJEZpbGVuYW1lJywgJyddLAogICAgICAgIFsnc3RhcnRzLXdpdGgnLCAnJENvbnRlbnQtVHlwZScsICcnXQogICAgXQp9' (length=428)
  public 'name' => string 'Foo.jpg' (length=7)
  public 'expirationdate' => string '2112-04-24T03:19:01.000Z' (length=24)
  public 'path' => string 'downloads/jlover/AggaBagga/Foo.jpg' (length=34)
  public 'signature' => string 'zYloIUfY7FIxQRn+IsqdUpCUvF0=' (length=28)
  public 's3_url' => string 'https://github.s3.amazonaws.com/' (length=32)
  public 'description' => null
  public 'id' => int 225888
  public 'url' => string 'https://api.github.com/repos/jlover/AggaBagga/downloads/225888' (length=62)
 */

/*
$data = array(
    'key' => $decoded->path
, 'acl' => $decoded->acl
, 'success_action_status' => 201
, 'Filename' => $decoded->name
, 'AWSAccessKeyId' => $decoded->accesskeyid
, 'Policy' => $decoded->policy
, 'Signature' => $decoded->signature
, 'Content-Type' => $decoded->mime_type
, 'file' => '@'.$file
);

$ch = curl_init();

$uri = $decoded->s3_url;

$curlOptions = array(
    CURLOPT_URL => $uri
, CURLOPT_POST => true
, CURLOPT_POSTFIELDS => $data
, CURLOPT_RETURNTRANSFER => true
    , CURLOPT_HEADER, true

);

curl_setopt_array($ch, $curlOptions);

$result = curl_exec($ch);

var_dump($result);

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

var_dump($http_code);
*/
//-- Dev mode - internal use =;)
//-- @@DEBUG
define('ECR_DEV_MODE', 1);

jimport('joomla.error.profiler');

$profiler = JProfiler::getInstance('Application');

JDEBUG ? $profiler->mark('com_easycreator starting') : null;

//-- Global constants
require JPATH_COMPONENT.'/includes/defines.php';

//-- Global functions
require JPATH_COMPONENT.'/includes/functions.php';

//-- Global functions
require JPATH_COMPONENT.'/includes/exceptions.php';

if(ECR_DEV_MODE)
{
    //-- Setup debugger
    if(JComponentHelper::getParams('com_easycreator')->get('ecr_debug'))
    {
        //-- Set debugging ON
        define('ECR_DEBUG', 1);
    }
    else
    {
        define('ECR_DEBUG', 0);
    }

    if(JComponentHelper::getParams('com_easycreator')->get('ecr_debug_lang', 0))
    {
        //-- Set debugging ON
        define('ECR_DEBUG_LANG', 1);
    }
    else
    {
        define('ECR_DEBUG_LANG', 0);
    }
}
else
{
    define('ECR_DEBUG', 0);
}

//-- Load the special Language

//-- 1) Check if g11n is installed as a PEAR package - see: http://elkuku.github.com/pear/

//-- @todo: check for installed g11n PEAR package to remove the "shut-up"
//-- @require_once 'elkuku/g11n/language.php';

try
{
    if(! class_exists('g11n'))
    {
        //-- 2) Check the libraries folder

        //-- @todo remove JFolder::exists when dropping 1.5 support
        if(! JFolder::exists(JPATH_LIBRARIES.'/g11n')
            || ! jimport('g11n.language')
        )
        {
            //-- 3) Load a dummy language handler -> english only !

            ecrLoadHelper('g11n_dummy');

            ecrScript('g11n_dummy', 'php2js');
        }
    }

    if(class_exists('g11n'))
    {
        //-- TEMP@@debug
        if(ECR_DEV_MODE && ECR_DEBUG_LANG)
        {
            //-- @@DEBUG
            g11n::cleanStorage();
            g11n::setDebug(ECR_DEBUG_LANG);
        }

        //-- Get our special language file
        g11n::loadLanguage();
    }
}
catch(Exception $e)
{
    JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

    return;
}
//try

/**
 * EasyCreator Version
 */
define('ECR_VERSION', EcrProjectHelper::parseXMLInstallFile(
    JPATH_COMPONENT_ADMINISTRATOR.DS.'easycreator.xml')->version);

/**
 * Check the Joomla! version
 */
switch(ECR_JVERSION)
{
    case '3.0': //-- Get prepared
        JFactory::getApplication()->enqueueMessage(sprintf(
            jgettext('EasyCreator version %s is in testing stage with your Joomla! version %s')
            , ECR_VERSION, ECR_JVERSION), 'warning');
        break;

    case '2.5':
    case '1.7':
    case '1.6':
    case '1.5':
        //-- We're all OK
        break;

    default:
        JFactory::getApplication()->enqueueMessage(sprintf(
            jgettext('EasyCreator version %s may not work well with your Joomla! version %s')
            , ECR_VERSION, ECR_JVERSION), 'warning');
        break;
}
//switch

//-- Add CSS
ecrStylesheet('default', 'toolbar', 'icon');

//-- Add JavaScript
ecrScript('global_vars', 'easycreator');

JFactory::getDocument()->addScriptDeclaration("var ECR_JVERSION = '".ECR_JVERSION."';".NL);
JFactory::getDocument()->addScriptDeclaration("var ECR_VERSION = '".ECR_VERSION."';".NL);

//-- Setup tooltips - used almost everywhere..
JHTML::_('behavior.tooltip');
JHTML::_('behavior.tooltip', '.hasEasyTip', array('className' => 'easy'));

if(version_compare(JVERSION, '1.6', '>'))
{
    //-- Joomla! 1.6+ compat
    $prevErrorReporting = error_reporting(E_ALL);

    //-- $prevErrorReporting = error_reporting(E_STRICT);//...when ¿
    $prevErrorReporting = error_reporting(- 1);
}
else
{
    /*
     * Joomla! 1.5 legacy stuff
     */

    $prevErrorReporting = error_reporting(E_ALL);

    $MTVersion = JFactory::getApplication()->get('MooToolsVersion');

    if(! $MTVersion)
        JFactory::getApplication()->enqueueMessage(
            jgettext('Please activate the MooTools Upgrade Plugin in Extensions->Plugin manager'), 'error');

    //-- J! 1.6 stuff not present in J! 1.5
    ecrLoadHelper('databasequery');
}

$controller = EcrEasycreator::getController();

if('component' == JRequest::getCmd('tmpl'))
{
    //-- Perform the Request task only - raw view
    $controller->execute(JRequest::getCmd('task'));
}
else
{
    //-- Display the menu
    EcrHtml::easyMenu();

    //-- Perform the Request task
    $controller->execute(JRequest::getCmd('task'));

    if(ECR_DEV_MODE && ECR_DEBUG_LANG
        && class_exists('g11n')
    )
    {
        g11n::debugPrintTranslateds(true);
        g11n::debugPrintTranslateds();
    }

    //-- Display the footer
    EcrHtml::footer();

    JDEBUG ? $profiler->mark('com_easycreator finished') : null;
}

//-- Restore error_reporting
error_reporting($prevErrorReporting);

//-- Redirect if set by the controller
//-- We don't do this very often =;)
$controller->redirect();
