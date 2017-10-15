<?php
/**
 * @version SVN: $Id: extensionhelper.php 369 2011-01-09 00:49:27Z elkuku $
 * @package    g11n
 * @subpackage Base
 * @author     Nikolai Plath {@link http://nik-it.de}
 * @author     Created on 19-Sep-2010
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Extension helper class.
 *
 * @package g11n
 */
class g11nExtensionHelper
{
    protected static $extensionTypes = array(
    'com' => 'component'
    , 'mod' => 'module'
    , 'tpl' => 'template'
    , 'plg' => 'plugin'
    , 'lib' => 'library');

    const langDirName = 'g11n';

    public static function getExtensionPath($extension)
    {
        static $dirs = array();

        if(in_array($extension, $dirs))
        return $dirs[$extension];

        if('joomla' == $extension)
        return;

        $prfx_extension = $extension;

        $parts = self::split($extension);

        $subType = '';

        if(count($parts) == 1)
        {
            $parts = self::split($extension, '_');
            $prefix = $parts[0];
            $extensionName = $parts[1];
        }
        else//
        {
            //-- We have a subType
            $subType = $parts[1];

            $prfx_extension  = $parts[0];

            $parts = self::split($parts[0], '_');
            $prefix = $parts[0];
            $extensionName = $parts[1];
        }

        if( ! array_key_exists($prefix, self::$extensionTypes))
        throw new g11nException(sprintf('Undefined extension type: %s', $prefix));

        if('tpl' == $prefix)
        {
            //-- Templates reside in a directory *without* the prefix 'tpl'
            $extensionDir = self::$extensionTypes[$prefix].'s/'.$extensionName;
        }
        else if('plg' == $prefix)
        {
            $parts = explode('_', $extensionName);

            if( ! isset($parts[1]))
            throw new g11nException('Unable to parse plugin name');

            $extensionDir = self::$extensionTypes[$prefix].'s/'.$parts[0].'/'.$parts[1];
        }
        else//
        {
            $extensionDir = self::$extensionTypes[$prefix].'s/'.$prfx_extension;
        }

        $dirs[$extension] = $extensionDir;

        return $extensionDir;
    }//function

    public static function getExtensionLanguagePath($extension)
    {
        $path = self::getExtensionPath($extension);

        return $path.'/'.self::langDirName;
    }//function

    public static function isExtension($extension, $scope = 'admin')
    {
        try//
        {
            $extensionPath = self::getExtensionPath($extension);
            $scopePath = self::getScopePath($scope);

            return is_dir($scopePath.'/'.$extensionPath);
        }
        catch(Exception $e)
        {
            JError::raiseWarning(0, $e->getMessage());
        }//try
    }//function

    public static function getScopePath($scope)
    {
        if($scope != 'admin'
        && $scope != 'site')
        throw new g11nException('Scope must be "admin" or "site"');

        return ($scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;
    }//function

    public static function getExtensionTypes()
    {
        return self::$extensionTypes;
    }//function

    /**
     * Searches the system for language files.
     *
     * @param string $lang Language
     * @param string $extension Extension
     * @param boolean $JAdmin True for administrator.
     * @param string $type Language file type - e.g. 'ini', 'po' etc.
     *
     * @return mixed Full path to file | false if none found
     *
     * @throws Exception
     */
    public static function findLanguageFile($lang, $extension, $scope = '', $type = 'po')
    {
        if($scope == '')
        {
            $base =(JFactory::getApplication()->isAdmin())
            ? JPATH_ADMINISTRATOR : JPATH_SITE;
        }
        else//
        {
            $base = g11nExtensionHelper::getScopePath($scope);
        }

if('joomla' == $extension)
{
        $fileName = $lang.'.'.$type;
}
else//
{
        $fileName = $lang.'.'.$extension.'.'.$type;
}

        $extensionDir = self::getExtensionPath($extension);

        $extensionLangDir = self::getExtensionLanguagePath($extension);

        //-- First try our special dir
        $path = JPath::clean("$base/$extensionLangDir/$lang/$fileName");

        if(file_exists($path))
        return $path;

        //-- Next try extension/language directory
        $path = JPath::clean("$base/$extensionDir/language/$lang/$fileName");

        if(file_exists($path))
        return $path;

        //-- Now try the base language dir
        $path = JPath::clean("$base/language/$lang/$fileName");

        if(file_exists($path))
        return $path;

        //-- Found nothing :(

        /* @Do_NOT_Translate */
        //        JError::raiseNotice(0, 'No language files found for [lang] [extension] [scope] [type]');
        //        JError::raiseNotice(0, sprintf('[%s] [%s] [%s] [%s]', $lang, $extension, $JAdmin, $type));

        return false;

        //throw new Exception('No language files found');//@Do_NOT_Translate
    }//function

    /**
     * Splits a string by a separator.
     *
     * Expects exactly two parts. Otherwise it will fail.
     *
     * @param string $string The string to split
     * @param string $delimiter The delimiter character
     *
     * @return array
     *
     * @throws Exception
     */
    public static function split($string, $delimiter = '.')
    {
        $parts = explode($delimiter, $string);

        if('mod' == $parts[0]
        || 'plg' == $parts[0]
        || 'tpl' == $parts[0])
        {
            $parts = array();

            $pos = strpos($string, '_');
            $parts[0] = substr($string, 0, $pos);
            $parts[1] = substr($string, $pos + 1);

            return $parts;
        }

        if(count($parts) < 1
        || count($parts) > 2)
        throw new g11nException('Invalid type - must be xx'.$delimiter.'[xx]: '.$string);

        return $parts;
    }//function
}//class
