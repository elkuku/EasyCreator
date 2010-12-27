<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Installer
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 30-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

defined('NL') || define('NL', "\n");

define('ECR_XML_LOCATION', $this->parent->getPath('manifest'));

/**
 * Main installer.
 *
 * @return boolean
 */
function com_install()
{
    $PHPMinVersion = '5.2.4';

    if(version_compare(PHP_VERSION, $PHPMinVersion, '<'))
    {
        JError::raiseWarning(0, sprintf('This script requires at least PHP version %s'
        , $PHPMinVersion));//@Do_NOT_Translate

        return false;
    }

    try
    {
        if( ! jimport('g11n.language'))
        {
            echo 'The g11n language library is required to run this extension.';
            JError::raiseWarning(0, 'The g11n language library is required to run this extension.');

            return false;
        }

        if( ! $xml = simplexml_load_file(ECR_XML_LOCATION))
        {
            JError::raiseWarning(0, jgettext('Install manifest not found'));

            return false;
        }

        //-- Get our special language file
        g11n::loadLanguage();
    }
    catch(Exception $e)
    {
        JError::raiseWarning(0, $e->getMessage());

        return false;
    }//try

    define('ECR_VERSION', $xml->version);

    require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easycreator'.DS.'helpers'.DS.'html.php';

    JFactory::getDocument()->addStyleSheet(JURI::root().'administrator/components/com_easycreator/assets/css/default.css');
    JFactory::getDocument()->addStyleSheet(JURI::root().'administrator/components/com_easycreator/assets/css/icon.css');
    ?>

<div>

<div style="float: right"><img
	src="<?php echo JURI::root(); ?>administrator/components/com_easycreator/assets/images/easycreator-shadow.png"
	alt="EasyCreator Logo" title="EasyCreator Logo" /></div>

<h1>EasyCreator</h1>
    <?php echo jgettext("EasyCreator is a developer tool.\n"
    ."It tries to speed up the developing process of custom components, modules, plugin and templates.\n"
    ."You can create a \"frame\" for your extension and an installable zip package with just a few \"clicks\""); ?>

<p>Happy coding,<br />
<?php echo sprintf(jgettext('The %s Team.'), '<a href="http://joomlacode.org/gf/project/elkuku">EasyCreator</a>'); ?>
</p>

</div>

<h3 style="color: orange;">
    <?php echo jgettext('Please use this extension only in local development environments.'); ?>
</h3>
<p>
    <?php echo jgettext("See: <a href='http://docs.joomla.org/Setting_up_your_workstation_for_Joomla!_development'>"
    ."docs.joomla.org/Setting up your workstation for Joomla! development</a>"); ?>
</p>

    <?php

    /*
     * MD5 check
     */
    jimport('joomla.filesystem.file');

    $paths = array(
      'admin' => JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easycreator'
      , 'site' => JPATH_SITE.DS.'components'.DS.'com_easycreator');

      $md5Path = $paths['admin'].DS.'install'.DS.'MD5SUMS';

      if(JFile::exists($md5Path))
      {
          echo '<br />'.jgettext('Checking MD5 sums...');

          $md5Check = checkMD5File($md5Path, $paths);

          if(count($md5Check))
          {
              echo '<strong style="color: red;">'.jgettext('There have been errors').'</strong>';
              echo '<ul style="color: red;">';
              echo '<li>';
              echo implode('</li><li>', $md5Check);
              echo '</li>';
              echo '</ul>';
          }
          else
          {
              echo '<strong style="color: green;">'.jgettext('OK').'</strong>';
          }
      }

      ecrHTML::footer();

      return true;
}//function

/**
 * Checks an extension against a given MD5 checksum file.
 *
 * @param string $path Path to md5 file
 * @param array $extensionPaths Indexed array:
 *         First folder in md5 file path as key - extension path as value
 *         e.g. array(
 *            'admin' => JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easycreator'
 *          , 'site' => JPATH_SITE.DS.'components'.DS.'com_easycreator');
 *
 * @return array Array of errors
 */
function checkMD5File($path, $extensionPaths)
{
    jimport('joomla.filesystem.file');

    $lines = explode("\n", JFile::read($path));

    $errors = array();

    foreach($lines as $line)
    {
        if( ! trim($line))
        continue;

        list($md5, $file) = explode(' ', $line);

        $parts = explode(DS, $file);

        if( ! array_key_exists($parts[0], $extensionPaths))
        continue;

        $path = $extensionPaths[$parts[0]].DS.substr($file, strlen($parts[0]) + 1);

        if(JFile::exists($path))
        {
            if(md5_file($path) != $md5)
            {
                $errors[] = sprintf(jgettext('MD5 check failed on file: %s'), $path);
            }
        }
        else
        {
            $errors[] = sprintf(jgettext('File not found: %s'), $path);
        }
    }//foreach

    return $errors;
}//function
