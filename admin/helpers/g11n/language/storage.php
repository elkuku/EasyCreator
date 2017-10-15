<?php
/**
 * @version SVN: $Id: storage.php 303 2010-12-18 15:46:56Z elkuku $
 * @package    g11n
 * @subpackage Storage
 * @author     Nikolai Plath {@link http://nik-it.de}
 * @author     Created on 19-Sep-2010
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * The g11n storage base class.
 *
 * @package g11n
 */
class g11nStorage
{
    protected static $handler = '';

    protected static $cacheDir = 'cache/g11n';

    /**
     * Constructor.
     */
    protected function __construct()
    {
        self::$cacheDir = 'cache/'.g11nExtensionHelper::langDirName;
    }//function

    /**
     * Get a storage handler.
     *
     * @param string $inputType A valid input type
     * @param string $storageType A valid storage type
     *
     * @return object Class extending g11nStorage
     * @throws Exception
     */
    public static function getHandler($inputType, $storageType)
    {
        if( ! jimport('g11n.language.storages.'.$storageType, JPATH_COMPONENT_ADMINISTRATOR . '/helpers'))
        throw new g11nException('Can not get the storage handler '.$storageType);//@Do_NOT_Translate

        $parts = g11nExtensionHelper::split($storageType, '_');
        $storageName = 'g11nStorage'.ucfirst($parts[0]).ucfirst($parts[1]);

        if( ! class_exists($storageName))
        throw new g11nException('Required class not found: '.$storageName);//@Do_NOT_Translate

        return new $storageName($inputType);
    }//function

    public static function getCacheDir()
    {
        return self::$cacheDir;
    }//function

    /**
     * Get the path of a storage file.
     *
     * @param string $lang Language tag e.g. en-GB.
     * @param string $extension Extension name e.g. com_component.
     * @param string $scope Must be 'admin' or 'site' / blank to use actual.
     *
     * @return string
     */
    protected function getPath($lang, $extension, $scope = '')
    {
        if(empty($scope))
        {
            $path =(JFactory::getApplication()->isAdmin())
            ? JPATH_ADMINISTRATOR : JPATH_SITE;
        }
        else//
        {
            $path =('admin' == $scope) ? JPATH_ADMINISTRATOR : JPATH_SITE;
        }

        $parts = g11nExtensionHelper::split($extension, '.');

        $dirName = $extension;

        if(count($parts) != 1)
        {
            $dirName = $parts[0];
        }

        $path .= '/'.self::$cacheDir.'/'.$dirName;
        $path .= '/'.$lang.'.'.$extension;

        return $path;
    }//function

    public static function templateExists($extension, $scope)
    {
        return (file_exists(self::getTemplatePath($extension, $scope))) ? true : false;
    }//function

    public static function getTemplatePath($extension, $scope)
    {
        static $templates = array();

        if(array_key_exists($extension, $templates)
        && array_key_exists($scope, $templates[$extension]))
        return $templates[$extension][$scope];

        $base = g11nExtensionHelper::getScopePath($scope);

        $parts = g11nExtensionHelper::split($extension);

        $subType = '';

        if(count($parts) == 1)
        {
            $parts = g11nExtensionHelper::split($extension, '_');
            $prefix = $parts[0];
        }
        else//
        {
            //-- We have a subType
            $subType = $parts[1];

            $parts = g11nExtensionHelper::split($parts[0], '_');
            $prefix = $parts[0];
        }

        $fileName = $extension.'.pot';

        $extensionDir = g11nExtensionHelper::getExtensionPath($extension);

        return JPath::clean("$base/$extensionDir/"
        .g11nExtensionHelper::langDirName."/templates/$fileName");
    }//function

    /**
     * Translate a gettext PluralForms string to pcre.
     *
     * E.g.: nplurals=2; plural=(n != 1)
     *
     * @param string $gettextPluralForms Gettext format
     *
     * @return string pcre type PluralForms
     */
    protected static function translatePluralForms($gettextPluralForms)
    {
        $expr = $gettextPluralForms.';';
        $res = '';
        $p = 0;

        for($i = 0; $i < strlen($expr); $i ++)
        {
            $ch = $expr[$i];

            switch($ch)
            {
                case '?':
                    $res .= ' ? (';
                    $p ++;
                    break;
                case ':':
                    $res .= ') : (';
                    break;
                case ';':
                    $res .= str_repeat(')', $p).';';
                    $p = 0;
                    break;
                default:
                    $res .= $ch;
            }//switch
        }//for

        return $res;
    }//function
}//class

/**
 * The g11n store description class.
 *
 * @package g11n
 */
class g11nStore
{
    private $strings = array();

    private $stringsPlural = array();

    private $stringsJs = array();

    private $stringsJsPlural = array();

    private $pluralForms = '';

    /**
     * Get a property.
     *
     * @param string $property Property name
     *
     * @return string
     */
    public function get($property)
    {
        if(isset($this->$property))
        return $this->$property;

        JError::raiseWarning(0, 'Undefined property '.__CLASS__.'::'.$property);
    }//function

    /**
     * Set a property.
     *
     * @param string $property Property name
     * @param mixed $value The value to set
     *
     * @return void
     */
    public function set($property, $value)
    {
        if( ! isset($this->$property))
        {
            JError::raiseWarning(0, 'Undefined property '.__CLASS__.'::'.$property);

            return;
        }

        $this->$property = $value;
    }//function
}//class

/**
 * FileInfo description class.
 *
 * @package g11n
 */
class g11nFileInfo
{
    public $fileName = '';

    public $mode = '';

    public $head = '';

    public $pluralForms = '';

    public $strings = array();

    public $stringsPlural = array();

    public $isCore = false;

    public $lines = '';

    public $langTag = '';

    /**
     * Get a property.
     *
     * @param string $property Property name
     *
     * @return mixed
     */
    public function get($property)
    {
        if(isset($this->$property))
        {
            return $this->$property;
        }

        JError::raiseWarning(0, 'Undefined property '.__CLASS__.'::'.$property);
    }//function
}//class

/**
 * File info class.
 */
class g11nTransInfo
{
    public $info = '';

    public $string = '';
}
