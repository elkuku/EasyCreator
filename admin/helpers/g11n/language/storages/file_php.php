<?php
/**
 * @version SVN: $Id: file_php.php 298 2010-12-17 04:41:07Z elkuku $
 * @package    g11n
 * @subpackage Storage handler
 * @author     Nikolai Plath {@link http://nik-it.de}
 * @author     Created on 19-Sep-2010
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Storage handler @todo desc...
 *
 * @package g11n
 */
class g11nStorageFilePHP extends g11nStorage
{
    public $fileInfo = null;

    protected $parser = null;

    protected $ext = '.php';

    /**
     * Constructor.
     *
     * @param string $inputType The input type
     */
    public function __construct($inputType)
    {
        if( ! jimport('g11n.language.parsers.language.'.$inputType, JPATH_COMPONENT_ADMINISTRATOR . '/helpers'))
        throw new g11nException('Can not get the parser '.$inputType);//@Do_NOT_Translate

        $parserName = 'g11nParserLanguage'.ucfirst($inputType);

        if( ! class_exists($parserName))
        throw new g11nException('Required parser class not found: '.$parserName);//@Do_NOT_Translate

        //@todo not sure of lsb - let's create a new() one
        //        #$this->parserName = $parserName;
        $this->parser = new $parserName;

        //        #JProfiler::getInstance('Application')->mark('loaded '.$parserName);

        //        #    parent::__construct();
    }//function

    /**
     * Stores the strings into a storage.
     *
     * @param string $lang E.g. de-DE, es-ES etc.
     * @param string $extension E.g. joomla, com_weblinks, com_easycreator etc.
     * @param string $scope Must be 'admin' or 'site'.
     *
     * @return void
     * @throws Exception
     */
    public function store($lang, $extension, $scope = '')
    {
        $ext = $this->parser->getExt();

        /*
         * Parse language files
         */
        $fileName = g11nExtensionHelper::findLanguageFile($lang, $extension, $scope, $ext);

        $fileInfo = $this->parser->parse($fileName);

        $this->fileInfo = $fileInfo;

        /*
         * "Normal" strings
         */
        $stringsArray = array();

        foreach($fileInfo->strings as $key => $value)
        {
            $key = md5($key);
            $value = base64_encode($value->string);

            $stringsArray[] = "'".$key."'=>'".$value."'";
        }//foreach

        /*
         * Plural strings
         */
        $pluralsArray = array();

        foreach($fileInfo->stringsPlural as $key => $plurals)
        {
            $key = md5($key);
            $ps = array();

            foreach($plurals->forms as $keyP => $plural)
            {
                $value = base64_encode($plural);
                $ps[] = "'".$keyP."'=>'".$value."'";
            }//foreach

            $value = base64_encode($value);
            $pluralsArray[] = "'".$key."'=> array(".implode(',', $ps).")";
        }//foreach

        /*
         * JavaScript strings
         */
        $jsArray = array();
        $jsPluralsArray = array();

        try
        {
            $jsFileName = g11nExtensionHelper::findLanguageFile($lang, $extension, $scope, 'js.'.$ext);

            $jsInfo = $this->parser->parse($jsFileName);

            foreach($jsInfo->strings as $key => $value)
            {
                $key = md5($key);
                $value = base64_encode($value->string);
                $jsArray[] = "'".$key."'=>'".$value."'";
            }//foreach

            $jsPluralsArray = array();

            foreach($jsInfo->stringsPlural as $key => $plurals)
            {
                $key = md5($key);
                $ps = array();

                foreach($plurals as $keyP => $plural)
                {
                    $value = base64_encode($plural);
                    $ps[] = "'".$keyP."'=>'".$value."'";
                }//foreach

                $value = base64_encode($value);
                $jsPluralsArray[] = "'".$key."'=> array(".implode(',', $ps).")";
            }//foreach
        }
        catch(Exception $e)
        {
            //-- We did not found the javascript files...
            //-- Do nothing - for now..@todo do something :P
            echo '';
        }//try

        /* Process the results - Construct an ""array string""
         * Result:
         * '<?php $strings = array('a'=>'b', ...); ?>'
         */
        $resultString = '';
        $resultString .= '<?php ';
        $resultString .= '$info=array('
        ."'mode'=>'".$fileInfo->mode."'"
        .",'pluralForms'=>'".$this->translatePluralForms($fileInfo->pluralForms)."'"
        .");";
        $resultString .= ' $strings=array('.implode(',', $stringsArray).');';
        $resultString .= ' $stringsPlural=array('.implode(',', $pluralsArray).');';
        $resultString .= ' $stringsJs=array('.implode(',', $jsArray).');';
        $resultString .= ' $stringsJsPlural=array('.implode(',', $jsPluralsArray).');';
        /*$resultString .= ' ?>';*/

        $storePath = $this->getPath($lang, $extension, $scope).$this->ext;

        if( ! JFile::write($storePath, $resultString))
        throw new g11nException('Unable to write language storage file to '.$storePath);//@Do_NOT_Translate
    }//function

    /**
     * Retrieve the storage content.
     *
     * @param string $lang E.g. de-DE, es-ES etc.
     * @param string $extension E.g. joomla, com_weblinks, com_easycreator etc.
     * @param string $scope Must be 'admin' or 'site'.
     *
     * @return boolean
     */
    public function retrieve($lang, $extension, $scope = '')
    {
        $parts = g11nExtensionHelper::split($extension, '_');

        $prefix = $parts[0];

        if('joomla' != $prefix)
        {
            if( ! array_key_exists($prefix, g11nExtensionHelper::getExtensionTypes()))
            throw new g11nException('Unknown extension type: '.$prefix);//@Do_NOT_Translate

            $extensionName = $parts[1];
        }

       # $parts = $this->split($extensionName, '.');

        $path = $this->getPath($lang, $extension, $scope).$this->ext;
        $path = JPath::clean($path);

        //-- File has not being cached
        if( ! file_exists($path))
        {
            //-- Try to store
            $this->store($lang, $extension, $scope);

            //-- Failed ?
            if( ! file_exists($path))
            throw new g11nException('Unable to retrieve the strings');//@Do_NOT_Translate
        }

        /*
         * Include the file
         * This file should contain the arrays
         * # $info()
         * # $strings()
         * # $jsStrings()
         */
        include $path;

        $store = new g11nStore;

        if(isset($info))
        {
//            $this->fileInfo->mode = null;
//
//            if(isset($info['mode'])
//            && $info['mode'])
//            $this->fileInfo->mode = $info['mode'];//Legacy ?

            if(isset($info['pluralForms']))
            $store->set('pluralForms', $info['pluralForms']);
        }

        if( ! empty($strings))
        $store->set('strings', $strings);

        if( ! empty($stringsPlural))
        $store->set('stringsPlural', $stringsPlural);

        if( ! empty($stringsJs))
        $store->set('stringsJs', $stringsJs);

        if( ! empty($stringsJsPlural))
        $store->set('stringsJsPlural', $stringsJsPlural);

        return $store;
    }//function

    /**
     * Cleans the storage.
     *
     * @param string $lang E.g. de-DE, es-ES etc.
     * @param string $extension E.g. joomla, com_weblinks, com_easycreator etc.
     * @param string $scope Must be 'admin' or 'site'.
     *
     * @return void
     * @throws Exception
     */
    public function clean($lang, $extension, $scope = '')
    {
        $storePath = $this->getPath($lang, $extension, $scope).$this->ext;

        if( ! JFile::exists($storePath))
        return;//-- Storage file does not exist

        if( ! JFile::delete($storePath))
        throw new g11nException('Unable to clean storage in: '.$storePath);//@Do_NOT_Translate
    }//function
}//class
