<?php
/**
 * @version SVN: $Id: language.php 346 2010-12-30 23:21:40Z elkuku $
 * @package    g11n
 * @subpackage Base
 * @author     Nikolai Plath {@link http://nik-it.de}
 * @author     Created on 19-Sep-2010
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$base = JPATH_COMPONENT_ADMINISTRATOR . '/helpers';

jimport('g11n.exception', $base);//@@DEBUG
jimport('g11n.language.debug', $base);//@@DEBUG

jimport('g11n.language.methods', $base);
jimport('g11n.language.storage', $base);

jimport('g11n.extensionhelper', $base);

/**
 * The g11n - "globalization" class.
 *
 * Language handling class.
 *
 * @package g11n
 */
abstract class g11n//-- Joomla!'s Alternative Language Handler oO
{
    /** Language tag - e.g.en-GB, de-DE
     * @var string
     */
    protected static $lang = '';

    /**
     * Array of defined strings for PHP and their translations
     * @var array()
     */
    protected static $strings = array();

    /**
     * Array of defined strings for JavaScript and their translations
     * @var array()
     */
    protected static $stringsJs = array();

    /**
     * Array of defined plural forms for PHP and their translations
     * @var array
     */
    protected static $stringsPlural = array();

    /**
     * Plural form for a specific language.
     * @var string
     */
    protected static $pluralForms = '';

    /**
     * Cache function that chooses plural forms.
     * @var object
     */
    protected static $pluralFunction = null;

    /**
     * The pluralization function for Javascript as a string.
     * @var string
     */
    protected static $pluralFunctionJsStr = '';

    /**
     * The type of the document to be rendered. E.g. html, json, console, etc.
     * According to the doctype \n will be converted to <br /> - or not.
     * @var string
     */
    protected static $docType = '';

    /**
     *  For debugging purpose
     * @var array()
     */
    protected static $processedItems = array();

    /**
     * This handels how different cases (upper/lower) are treated in ini files
     * @var string
     */
    protected static $flexibility = '';

    /** This is for, well... debugging =;)
     * @var boolean
     */
    protected static $debug = false;

    protected static $events = array();

    protected static $extensionsLoaded = array();

    /**
     * Provide access to everything we have inside ;).
     *
     * Provided for 3pd use or whatever..
     *
     * @param string $property Property name
     *
     * @return mixed
     */
    public static function get($property)
    {
        if(isset(self::$$property))
        return self::$$property;

        JError::raiseWarning(0, 'Undefined property '.__CLASS__.'::'.$property);
    }//function

    /**
     * Load the language.
     *
     * @param string $extension E.g. joomla, com_weblinks, com_easycreator etc.
     * @param string $scope Must be 'admin' or 'site' / empty to use the actual.
     * @param string $inputType The input type e.g. "ini" or "po"
     * @param string $storageType The store type - e.g. 'file_php'
     *
     * @return void
     * @throws Exception
     */
    public static function loadLanguage($extension = '', $scope = ''
    , $inputType = 'po', $storageType = 'file_php')
    {
        if( ! $extension
        && ! $extension = JFactory::getApplication()->input->getCmd('option'))
        throw new g11nException('Invalid extension');

        if(empty($scope))
        $scope = JFactory::getApplication()->isAdmin()
        ? 'admin' : 'site';

        $key = $extension.'.'.$scope;

        if(array_key_exists($key, self::$extensionsLoaded))
        return;

        if( ! self::$lang)
        self::detectLanguage();

        if( ! self::$docType)
        self::detectDocType();

        $handler = g11nStorage::getHandler($inputType, $storageType);

        $store = $handler->retrieve(self::$lang, $extension, $scope);

        self::$strings = array_merge(self::$strings, $store->get('strings'));

        self::$stringsPlural = $store->get('stringsPlural');

        self::setPluralFunction($store->get('pluralForms'));

        self::addJavaScript($store->get('stringsJs'), $store->get('stringsJsPlural'));

        $dbgMsg = '';

        if(self::$debug)
        {
            $dbgMsg = sprintf('Found %d strings'
            , count($store->get('strings')));
        }

        self::logEvent(__CLASS__.'::loadLanguage()', $extension, $scope, $inputType, $storageType, $dbgMsg);

        self::$extensionsLoaded[$key] = 1;
    }//function

    public static function getDefault()
    {
        if( ! self::$lang)
        self::detectLanguage();

        return self::$lang;
    }//function

    /**
     * Try to translate a string.
     *
     * @param string $original The string to translate.
     *
     * @return string Translated string or original if not found.
     */
    public static function translate($original)
    {
        if(self::$debug)
        return self::debugTranslate($original);

        $key = md5($original);

        if(isset(self::$strings[$key])
        && self::$strings[$key])
        {
            //-- Translation found
            return self::process(self::$strings[$key]);
        }

        //-- Search for alternatives - L for legacy
        if(self::$flexibility == 'mixed'
        || ( ! self::$flexibility))
        {
            $key = md5(strtoupper($original));

            if(isset(self::$strings[$key]))
            {
                //-- Translation found - key is upper cased, requested string is not..
                return self::process(self::$strings[$key]);
            }
        }

        //-- Worst case - No translation found !

        if(self::$docType == 'html')
        {
            $original = str_replace(array("\n", "\\n"), '<br />', $original);
        }

        return $original;
    }//function

    /**
     * Try to translate a plural string.
     *
     * @param string $singular Singular form
     * @param string $plural Plural form
     * @param integer $count How many times..
     *
     * @return string
     */
    public static function translatePlural($singular, $plural, $count)
    {
        $key = $singular;

        $key = md5($key);

        $index = (int)call_user_func(self::$pluralFunction, $count);

        if(array_key_exists($key, self::$stringsPlural)
        && array_key_exists($index, self::$stringsPlural[$key]))
        {
            return self::process(self::$stringsPlural[$key][$index]);
        }

        //-- Fallback - english: singular == 1
        return ($count == 1) ? $singular : $plural;
    }//function

    /**
     * Clean the storage device.
     *
     * @param string $extension E.g. joomla, com_weblinks, com_easycreator etc.
     * @param boolean $JAdmin Set true for administrator
     * @param string $inputType The input type e.g. "ini" or "po"
     * @param string $storageType The story type
     *
     * @return void
     * @throws Exception
     */
    public static function cleanStorage($extension = '', $JAdmin = ''
    , $inputType = 'po', $storageType = 'file_php')
    {
        if( ! self::$lang)
        self::detectLanguage();

        if( ! $extension
        && !  $extension = JFactory::getApplication()->input->getCmd('option'))
        throw new g11nException('Invalid extension');

        if($JAdmin == '')
        $JAdmin = JFactory::getApplication()->isAdmin()
        ? true : false;

        ##self::$strings = array();

        g11nStorage::getHandler($inputType, $storageType)->clean(self::$lang, $extension, $JAdmin);
    }//function

    /**
     * Switch the debuggin feature on or off.
     *
     * Provided for 3pd use ore whatever..
     *
     * @param boolean $bool Set true to turn the debugger on
     *
     * @return void
     */
    public static function setDebug($bool)
    {
        self::$debug = (bool)$bool;
    }//function

    /**
     * Debug output translated and untranslated items.
     *
     * @param boolean $untranslatedOnly Set true to ouput only untranslated strings
     *
     * @return void
     */
    public static function debugPrintTranslateds($untranslatedOnly = false)
    {
        g11nDebugger::debugPrintTranslateds($untranslatedOnly);
    }//function

    /**
     * Print out recorded events.
     *
     * @return void
     */
    public static function printEvents()
    {
        foreach(self::$events as $e)
        {
            var_dump($e);
        }//foreach
    }//function

    /**
     * For 3PD use.
     *
     * You may use this function for manupulation of language files.
     * Parsers support parsing and generating language files.
     *
     * @param string $type Parser type
     * @param string $name Parser name
     *
     * @return object g11nParser of a specific type
     * @throws Exception If requested parser is not found
     */
    public static function getParser($type, $name)
    {
        if( ! jimport('g11n.language.parsers.'.$type.'.'.$name, JPATH_COMPONENT_ADMINISTRATOR . '/helpers'))
        throw new g11nException('Can not get the parser '.$type.'.'.$name);//@Do_NOT_Translate

        $parserName = 'g11nParser'.ucfirst($type).ucfirst($name);

        if( ! class_exists($parserName))
        throw new g11nException('Required class not found: '.$parserName);//@Do_NOT_Translate

        return new $parserName;
    }//function

    /**
     * Translation in debug mode.
     *
     * @param string $original Original string to be translated
     *
     * @return string
     */
    protected static function debugTranslate($original)
    {
        $key = md5($original);

        $addOK       = '+-%s-+';
        $addMissing  = '¿-%s-¿';
        $addLegacy   = 'L-%s-L';

        if(isset(self::$strings[$key])
        && self::$strings[$key])
        {
            //-- Translation found
            self::recordTranslated($original, '+');

            return sprintf($addOK, self::process(self::$strings[$key]));
        }
        else if(self::$flexibility == 'mixed'
        || ( ! self::$flexibility))
        {
            //-- Search for alternatives - upper cased key
            $key = md5(strtoupper($original));

            if(isset(self::$strings[$key]))
            {
                //-- Translation found - key is upper cased, value is not..
                self::recordTranslated($original, 'L');

                return sprintf($addLegacy, self::process(self::$strings[$key]));
            }
        }

        //-- Worst case - No translation found !

        self::recordTranslated($original, '-');

        return sprintf($addMissing, str_replace(array("\n", "\\n"), '<br />', $original));
    }//function

    /**
     * Set a plural function.
     *
     * @param string $pcrePluralForm The PCRE plural form to be parsed.
     *
     * @return void
     */
    protected static function setPluralFunction($pcrePluralForm)
    {
        if(preg_match("/nplurals\s*=\s*(\d+)\s*\;\s*plural\s*=\s*(.*?)\;+/", $pcrePluralForm, $matches))
        {
            $nplurals = $matches[1];
            $expression = $matches[2];

            $PHPexpression = str_replace('n', '$n', $expression);
        }
        else//
        {
            $nplurals = 2;
            $expression = 'n == 1 ? 0 : 1';
            $PHPexpression = '$n == 1 ? 0 : 1';
        }

        $func_body = '$plural = ('.$PHPexpression.');'
        . ' return ($plural <= '.$nplurals.')? $plural : $plural - 1;';

        $js_func_body = 'plural = ('.$expression.');'
        . ' return (plural <= '.$nplurals.')? plural : plural - 1;';

        self::$pluralFunction = create_function('$n', $func_body);

        self::$pluralFunctionJsStr = "phpjs.create_function('n', '".$js_func_body."')";
    }//function

    /**
     * Add the strings designated to JavaScript to the page <head> section.
     *
     * @param array $strings These strings will be added to the HTML source of your page
     * @param array $stringsPlural The plural strings
     *
     * @return void
     */
    protected static function addJavaScript($strings, $stringsPlural)
    {
        static $hasBeenAdded = false;

        //-- To be called only once
        if( ! $hasBeenAdded)
        {
            $path = 'administrator/components/com_easycreator/helpers/g11n/language/javascript';
            $document = JFactory::getDocument();

            $document->addScript(JURI::root(true).'/'.$path.'/methods.js');
            $document->addScript(JURI::root(true).'/'.$path.'/language.js');
            $document->addScript(JURI::root(true).'/'.$path.'/phpjs.js');

            $document->addScriptDeclaration("g11n.debug = '".self::$debug."'\n");

            $hasBeenAdded = true;
        }

        //-- Add the strings to the page <head> section
        $js = array();
        $js[] = '<!--';
        $js[] = '/* JavaScript translations */';
        $js[] = 'g11n.loadLanguageStrings('.json_encode($strings).');';
        $js[] = "g11n.legacy = '".self::$flexibility."'";

        if(self::$pluralFunctionJsStr)
        {
            $js[] = 'g11n.loadPluralStrings('.json_encode($stringsPlural).');';

            if( ! $hasBeenAdded)
            $js[] = 'g11n.setPluralFunction('.self::$pluralFunctionJsStr.')';
        }

        $js[] = '-->';

        self::$stringsJs = array_merge(self::$stringsJs, $strings);

        JFactory::getDocument()->addScriptDeclaration(implode("\n", $js));
    }//function

    /**
     * Processes the final translation. Decoding and converting \n to <br /> if nessesary.
     *
     * @param string $string The string to process
     *
     * @return string
     */
    private static function process($string)
    {
        $string = base64_decode($string);

        if(self::$docType == 'html')
        {
            $string = str_replace(array("\n", "\\n"), '<br />', $string);
        }

        return $string;
    }//function

    /**
     * Try to detect the current language.
     *
     * This is done with a little help .. from JFactory::getLanguage()
     *
     * @return void
     * @throws Exception
     */
    private static function detectLanguage()
    {
        $reqLang = JFactory::getApplication()->input->getCmd('lang');

        if($reqLang != '')
        {
            //@todo CHECKif language exists..
            self::$lang = $reqLang;

            JFactory::getApplication()->setUserState('lang', $reqLang);

            return;
        }

        $stateLang = JFactory::getApplication()->getUserState('lang');

        if($stateLang != '')
        {
            //@todo CHECKif language exists..
            self::$lang = $stateLang;

            return;
        }

        //-- OK.. first let's do a
        self::$lang = JFactory::getLanguage()->getTag();
        //that should be enough..

        if( ! self::$lang)
        throw new g11nException('Something wrong with JLanguage :(');
    }//function

    /**
     * Try to detect the current document type.
     *
     * This is done with a little help .. from JFactory::getLanguage()
     *
     * @return void
     * @throws Exception
     */
    private static function detectDocType()
    {
        self::$docType = JFactory::getDocument()->getType();

        if( ! self::$docType)
        throw new g11nException('Unable to detect the document type :(');
    }//function

    /**
     * Record translated and untranslated strings.
     *
     * @param string $string The string to record
     * @param string $mode Parsing mode strict/legacy
     *
     * @return void
     */
    private static function recordTranslated($string, $mode)
    {
        if(array_key_exists($string, self::$processedItems))
        {
            return;//-- Already recorded
        }

        $info = new stdClass();
        $info->status = $mode;
        $info->file = '';
        $info->line = 0;
        $info->function = '';
        $info->args = array();

        if(function_exists('debug_backtrace'))
        {
            $trace = debug_backtrace();

            //-- Element no. 3 must be our jgettext() caller
            $trace = $trace[3];

            $info->file = $trace['file'];
            $info->line = $trace['line'];
            $info->function = $trace['function'];
            $info->args = $trace['args'];
        }

        self::$processedItems[$string] = $info;
    }//function

    /**
     * Logs events.
     *
     * Accepts multiple arguments
     *
     * @return void
     */
    private static function logEvent()
    {
        $args = func_get_args();

        $e = new stdClass();

        foreach($args as $k => $v)
        {
            $e->$k = $v;
        }//foreach

        self::$events[] = $e;
    }//function
}//class
