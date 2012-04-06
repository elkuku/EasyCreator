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
        $grandParent = $parent->getParent();

        $PHPMinVersion = '5.2.4';

        if(version_compare(PHP_VERSION, $PHPMinVersion, '<'))
        {
            JFactory::getApplication()->enqueueMessage(sprintf('This script requires at least PHP version %s'
                , $PHPMinVersion), 'error');

            return false;
        }

        JFactory::getDocument()->addStylesheet(JURI::root(true)
            .'/administrator/components/com_easycreator/assets/css/default.css');

        if('update' == $type)
        {
            $xx = $grandParent->getPath('extension_administrator');
            $this->extensionPaths = array(
                'admin' => JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easycreator'
            , 'site' => JPATH_SITE.DS.'components'.DS.'com_easycreator');

            if(false === $this->updateWithMd5File($parent))
            {
                JFactory::getApplication()->enqueueMessage(
                    'Can not update your current EasyCreator version - Please uninstall first - sry ;(', 'error');

                return false;
            }
        }

        return true;
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
        //    $parent->getParent()->setRedirectURL('index.php?option=_ECR_COM_COM_NAME_');
        //echo '<p>'.JText::_('_ECR_UPPER_COM_COM_NAME__INSTALL_TEXT').'</p>';
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
        echo '<p>'.JText::_('_ECR_UPPER_COM_COM_NAME__UPDATE_TEXT').'</p>';
    }

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @param string $type
     * @param  JAdapterInstance  $parent  The class calling this method
     *
     * @return bool
     */
    public function postflight($type, $parent)
    {
        if('update' != $type)
            return true;

        // $parent is
        // $type is the type of change (install, update or discover_install)

        $path = $parent->getParent()->getPath('extension_root').'/to-be-removed.txt';

        if(! JFile::exists($path))
            return true;

        $contents = JFile::read($path);

        $files = explode("\n", trim($contents));

        if(! count($files))
            return true;

        echo '<h2>Cleaning up</h2>';

        $count = 0;

        echo '<ul>';

        foreach($files as $file)
        {
            if(! trim($file))
                continue;

            if(! JFile::delete(trim($file)))
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

        return true;
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
        echo '<h2>EasyCreator has been removed from your system -- you\'re on your own now... :(</h2>';
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

        if(! count($pathsOld) || ! count($pathsNew))
            return false;

        $results = array();

        foreach(array_keys($pathsOld) as $shortPath)
        {
            if(! array_key_exists($shortPath, $pathsNew))
            {
                $parts = explode('/', $shortPath);

                if(! array_key_exists($parts[0], $this->extensionPaths))
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
        jimport('joomla.filesystem.file');

        $path = JPath::clean($path);

        $paths = array();

        if(! JFile::exists($path))
            return $paths;

        $lines = explode("\n", JFile::read($path));

        foreach($lines as $line)
        {
            if(! trim($line))
                continue;

            list($md5, $subPath) = explode(' ', $line);

            $pos = strpos($subPath, '@');

            $path = $subPath;

            // lines containing a @ are compressed.
            if($pos !== false)
            {
                $compressed = substr($subPath, 0, $pos);
                $path = $this->decompress($compressed).DS.substr($subPath, $pos + 1);
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

        if(! $previous) //-- Init
        {
            $previous = $path;

            return $previous;
        }

        $decompressed = $previous; //-- Same as previous path - maximun compression :)

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

}//class
