<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Installer
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 16-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//@todo remove
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

/**
 * Script file for EasyCreator component.
 */
class Com_EasyCreatorInstallerScript
{
    private $extensionPaths = array();

    private $md5PathOld = 'install/MD5SUMS';

    private $md5PathNew = 'admin/install/MD5SUMS';

    /**
     * Method to run before an install/update/uninstall method.
     *
     * @param  string  $type    The type of change (install, update or discover_install)
     * @param  string  $parent  The class calling this method
     *
     * @return boolean
     */
    public function preflight($type, $parent)
    {
        /* @var JInstaller $grandParent */
        $grandParent = $parent->getParent();

        $PHPMinVersion = '5.3';

        if(version_compare(PHP_VERSION, $PHPMinVersion, '<'))
        {
            JFactory::getApplication()->enqueueMessage(sprintf('This script requires at least PHP version %s'
                , $PHPMinVersion), 'error');

            return false;
        }

        //-- This does not work :( - css..
        JFactory::getDocument()->addStylesheet(JURI::root(true)
            .'/media/com_easycreator/admin/css/default.css');

        if('update' == $type)
        {
            $this->extensionPaths = array(
                'admin' => $grandParent->getPath('extension_administrator')
            , 'site' => $grandParent->getPath('extension_administrator')
            );

            if(false === $this->updateWithMd5File($parent))
            {
                JFactory::getApplication()->enqueueMessage(
                    'Can not update your current EasyCreator version - Please uninstall first - sry ;(', 'error');

                return false;
            }

            $oldFolders = array(
                'builds' => 'data/builds'
            , 'exports' => 'data/exports'
            , 'logs' => 'data/logs'
            , 'results' => 'data/results'
            , 'scripts' => 'data/projects'
            );

            $extensionPath = $grandParent->getPath('extension_administrator');

            foreach($oldFolders as $oldName => $newName)
            {
                if(JFolder::exists($extensionPath.'/'.$oldName))
                {
                    if(JFolder::copy($extensionPath.'/'.$oldName, $extensionPath.'/'.$newName))
                    {
                        echo sprintf('The folder %s has been copied to %s', $oldName, $newName).'<br />';
                    }
                    else
                    {
                        echo '<strong style="color: red;">'
                            .sprintf('The folder %s could not be copied to %s', $oldName, $newName)
                            .'</strong><br />';
                    }
                }
            }
        }

        return true;
    }

    /**
     * Method to install the component.
     *
     * @param $parent
     *
     * @return boolean
     */
    public function install($parent)
    {
        // $parent is the class calling this method
        //    $parent->getParent()->setRedirectURL('index.php?option=ECR_COM_COM_NAME');
        //echo '<p>'.JText::_('ECR_UPPER_COM_COM_NAME_INSTALL_TEXT').'</p>';

        $logo = JURI::root(true).'/media/com_easycreator/admin/images/ico/icon-128-easycreator.png';

        try
        {
            if( ! jimport('g11n.language'))
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

            //$xml = simplexml_load_file(ECR_XML_LOCATION);
            $xml = simplexml_load_file($parent->getParent()->getPath('manifest'));

            if(false == $xml)
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

        require_once JPATH_ADMINISTRATOR.'/components/com_easycreator/helpers/html.php';

        JFactory::getDocument()->addStyleSheet(JURI::root().'media/com_easycreator/admin/css/default.css');
        JFactory::getDocument()->addStyleSheet(JURI::root().'media/com_easycreator/admin/css/icon.css');
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
        ##ECR_MD5CHECK##

        EcrHtml::footer();
    }

    /**
     * Method to update the component. DISABLED...
     *
     * @param $parent
     *
     * @return void
     */
    public function WTFupdate($parent)
    {
        // $parent is the class calling this method
        echo '<p>'.JText::_('ECR_UPPER_COM_COM_NAME_UPDATE_TEXT').'</p>';
    }

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @param string             $type    is the type of change (install, update or discover_install)
     * @param  JAdapterInstance  $parent  The class calling this method
     *
     * @return bool
     */
    public function postflight($type, $parent)
    {
        if('update' != $type)
            return true;

        $extensionPath = $parent->getParent()->getPath('extension_administrator');

        $this->removeObsoleteFiles($extensionPath.'/to-be-removed.txt');

        return true;
    }

    /**
     * @param $path
     *
     * @return Com_EasyCreatorInstallerScript
     */
    private function removeObsoleteFiles($path)
    {
        if(false == JFile::exists($path))
            return $this;

        $contents = JFile::read($path);

        $files = explode("\n", trim($contents));

        if(0 == count($files))
            return $this;

        echo '<h2>Cleaning up</h2>';

        $count = 0;

        echo '<ul>';

        foreach($files as $file)
        {
            $file = trim($file);

            if('' == $file)
                continue;

            if(false == JFile::exists($file))
                continue;

            if(false == JFile::delete($file))
            {
                echo '<li style="color: red;">Unable to delete obsolete file: '.$file.'</li>';
            }
            else
            {
                echo '<li style="color: green;">Obsolete file deleted: '.$file.'</li>';

                $count ++;
            }
        }

        echo '</ul>';

        echo sprintf('%d obsolete files deleted.', $count);

        return $this;
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
        echo '<div class="alert alert-error">';
        echo '<h2>EasyCreator has been removed from your system</h2>';
        echo '<h3>you\'re on your own now... :(</h3>';
        echo '</div>';
    }

    /**
     * @param $parent
     *
     * @return bool
     */
    private function updateWithMd5File($parent)
    {
        $pathsOld = $this->readMd5File($parent->getParent()->getPath('extension_root').'/'.$this->md5PathOld);
        $pathsNew = $this->readMd5File($parent->getParent()->getPath('source').'/'.$this->md5PathNew);

        if(0 == count($pathsOld) || ! count($pathsNew))
            return false;

        $results = array();

        foreach(array_keys($pathsOld) as $shortPath)
        {
            if(false == array_key_exists($shortPath, $pathsNew))
            {
                $parts = explode('/', $shortPath);

                if(false == array_key_exists($parts[0], $this->extensionPaths))
                    continue;

                $path = $this->extensionPaths[$parts[0]].'/'.substr($shortPath, strlen($parts[0]) + 1);

                $results[] = $path;
            }
        }

        $contents = implode("\n", $results);

        JFile::write($parent->getParent()->getPath('extension_root').'/to-be-removed.txt', $contents);

        return true;
    }

    /**
     * @param $path
     *
     * @throws Exception
     * @return array
     */
    private function readMd5File($path)
    {
        //-- @todo remove
        jimport('joomla.filesystem.file');

        $path = JPath::clean($path);

        $paths = array();

        if(false == JFile::exists($path))
            return $paths;

        $lines = explode("\n", JFile::read($path));

        foreach($lines as $line)
        {
            if('' == trim($line))
                continue;

            list($md5, $subPath) = explode(' ', $line);

            $pos = strpos($subPath, '@');

            $path = $subPath;

            // lines containing a @ are compressed.
            if($pos !== false)
            {
                $compressed = substr($subPath, 0, $pos);
                $path = $this->decompress($compressed).'/'.substr($subPath, $pos + 1);
            }

            $paths[$path] = $md5;
        }

        return $paths;
    }

    /**
     * Decompress a KuKuKompress compressed path
     *
     * @param string $path
     *
     * @return string decompressed path
     */
    private function decompress($path)
    {
        static $previous = '';

        if('' == $previous) //-- Init
        {
            $previous = $path;

            return $previous;
        }

        $decompressed = $previous; //-- Same as previous path - maximum compression :)

        if($path != '=') //-- Different path - too bad..
        {
            $pos = strpos($path, '|'); //-- Separates previous path info from new path

            if($pos)
            {
                $command = substr($path, 0, $pos);

                $c = count(explode('-', $command)) - 1;

                $parts = explode('/', $previous);

                $decompressed = '';

                for($i = 0; $i < $c; $i ++)
                {
                    $decompressed .= $parts[$i].'/';
                }

                $addPath = substr($path, $pos + 1);

                $decompressed .= $addPath;

                $decompressed = trim($decompressed, '/');

                $previous = $decompressed;

                return $decompressed;
            }

            $decompressed = $path;
        }

        $decompressed = trim($decompressed, '/');

        $previous = $decompressed;

        return $decompressed;
    }
}

##ECR_MD5CHECK_FNC##
