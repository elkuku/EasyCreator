<?php
/**
 * @version SVN: $Id$
 * @package    LanguageChecker
 * @subpackage Helpers
 * @author     Nikolai Plath {@link http://easy-joomla.org}
 * @author     Created on 12-Sep-2010
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * LanguageCheckerHelper.
 *
 */
class LanguageCheckerHelper
{
    private $paths = array(
      'admin' => JPATH_ADMINISTRATOR
    , 'site' => JPATH_SITE);

    private $component = '';

    private $scope = '';

    private $subScope = '';

    private $lang = '';

    //--RegEx pattern for JText in PHP files
    private $patternPHP = '';

    private $patternPHPPlural = '';

    //--RegEx pattern for Joomla.JText in Javascript files
    private $patternJs = '';

    private $patternJsPlural = '';

    private $translations = array();

    private $translationsPlural = array();

    private $head = '';

    private $strings = array();

    private $stringsPlural = array();

    private $strangeTHINGS = array();

    private $fileFilter = '';

    private $excludeDirs = array();

    private $langFormatIn = '';

    private $langFormatOut = '';

    private $loadCore = false;

    private $strangeThings = array();

    public $loadedFiles = array();

    public function __construct($component, $scope, $subScope, $lang, $fileFilter = ''
    , $excludeDirs = array(), $langFormatIn = 'ini', $langFormatOut = 'ini'
    , $loadCore = false)
    {
        if( ! $component || ! $scope || ! $lang)
        throw new Exception('Missing values', 0);

        $this->component = $component;
        $this->scope = $scope;
        $this->subScope = $subScope;
        $this->lang = $lang;
        $this->fileFilter = $fileFilter;
        $this->excludeDirs = explode(',', $excludeDirs);
        $this->langFormatIn = $langFormatIn;
        $this->langFormatOut = $langFormatOut;
        $this->loadCore = $loadCore;

        $cmds = array();

        if($langFormatIn == 'ini')
        {
            $cmds['php1'] = 'JText::_';
            $cmds['php2'] = 'JText::sprintf';
            $cmds['php3'] = 'JText::printf';
            $cmds['php_plural'] = 'JText::plural';
            $cmds['js'] = 'Joomla.JText._';
        }
        else//
        {
            $cmds['php1'] = 'jgettext';
            $cmds['php2'] = 'jgettext';
            $cmds['php3'] = 'jgettext';
            $cmds['php_plural'] = 'jngettext';
            $cmds['js'] = 'jgettext';
            $cmds['js_plural'] = 'jngettext';
        }

        //--RegEx pattern for JText in PHP files
        $this->patternPHP = '/';
        $this->patternPHP2 = '/';
        //-- Regular JText JText_('foo')
        //-- JText with parameters JText_('foo', ...)
        //        	"|".
        $this->patternPHP .= $cmds['php1']."\(\s*\'(.*)\'\s*\,|".$cmds['php1']."\(\s*\"(.*)\"\s*\,";
        $this->patternPHP2 .= $cmds['php1']."\(\s*\'(.*)\'\)\s*[\,|\.]|".$cmds['php1']."\(\s*\"(.*)\"\)\s*[\,|\.]";
        $this->patternPHP2 .= "|".$cmds['php1']."\(\s*\'(.*)\'\s*\)";//|".$cmds['php1']."\(\s*\"(.*)\"\s*\)";//.
//        #$this->patternPHP2 = '//';
        if($langFormatIn == 'ini')
        {
            $this->patternPHP .=
            //-- JText sprintf
            "|".$cmds['php2']."\(\s*\'(.*)\'|".$cmds['php2']."\(\s*\"(.*)\""
            //-- JText printf
            ."|".$cmds['php3']."\(\s*\'(.*)\'|".$cmds['php3']."\(\s*\"(.*)\"";

            // JHtml::_('grid.sort', 'FOO', ...)
            $this->patternPHP .= "|JHtml::_\(\'grid\.sort\'\, \'(.*)\'"
            //-- JToolBarHelper::custom('users.activate', 'xxx.png', 'xxx.png', 'FOO'...
            ."|JToolBarHelper::custom\(\'.*\'\,\s*\'.*\'\,\s*\'.*'\,\s*\'(.*)\'";
            //(.*))\'/iU";//, 'publish.png', 'publish_f2.png', 'COM_...', true);/iU";
        }

        $this->patternPHP .= '/iU';
        $this->patternPHP2 .= '/iU';

//        var_dump($this->patternPHP);
//        var_dump($this->patternPHP2);
        $this->patternPHPPlural =
         "/".$cmds['php_plural']."\(\s*\'(.*)\'\s*,\s*\'(.*)\',"
         ."|".$cmds['php_plural']."\(\s*\"(.*)\"\s*,\s*\"(.*)\"/iU";
//         #echo $this->patternPHPPlural.'<br />';
         //--RegEx pattern for Joomla.JText in Javascript files
         $this->patternJs =
         //--In case there is the second parameter (default) set
            "/".$cmds['js']."\(\s*\"(.*)\"|".$cmds['js']."\(\s*\'(.*)\'"
         //--'''normal''' use...
           ."|".$cmds['js']."\(\s*\'(.*)\'\s*\)|".$cmds['js']."\(\s*\"(.*)\"\s*\)/iU";

         //-- Parse core language files
         if($loadCore)
         {
//             #    $this->parseLanguageFile('', $scope, $lang);
         }

         //-- Parse extension language files
         $this->parseLanguageFile();

         //-- Parse extension code files
         $this->parseCodeFiles();

//         #     echo'<pre>'.print_r($this->stringsPlural, 1).'</pre>';
    }//function

    public function getHead()
    {
        return $this->head;
    }

    public function getStrings()
    {
        return $this->strings;
    }//function

    public function getStringsPlural()
    {
        return $this->stringsPlural;
    }//function

    public function getTranslations()
    {
        return $this->translations;
    }//function

    public function getStrangeTHINGS()
    {
        return $this->strangeTHINGS;
    }//function

    public function getLang()
    {
        return $this->lang;
    }//function

    // @codingStandardsIgnoreStart
    private function parseCodeFiles()
    {
        $path = $this->paths[$this->scope];
        $comPath = $path.DS.'components'.DS.$this->component;

        $parseExtensions = array('php', 'js', 'xml');
        $parsers = array();

        $this->comPath = $comPath;

        $files = array();

        if( ! JFolder::exists($comPath))
        {
            return;
        }

        $files = JFolder::files($comPath, '.', true, true, array('index.html'));//@todo: report a bug :P

        try
        {
            foreach($parseExtensions as $ext)
            {
                $parsers[$ext] = JALHOO::getParser('code', $ext);
                $parsers[$ext]->setLangFormat($this->langFormatIn);
            }//foreach
        }
        catch(Exception $e)
        {
            JError::raiseWarning(0, $e->getMessage());
        }//try

        foreach($files as $fileName)
        {
            foreach($this->excludeDirs as $exclude)
            {
                if($exclude
                && strpos($fileName, JPATH_ROOT.DS.$exclude) === 0)
                continue 2;
            }//foreach

            $ext = JFile::getExt($fileName);

            switch($ext)
            {
                case 'php':
                    if($this->subScope == 'js')
                    continue;

                    $fileInfo = $parsers[$ext]->parse($fileName);

                    if($fileInfo->strings)
                    {
//                        #var_dump($fileInfo);
                        foreach($fileInfo->strings as $lineNo => $strings)
                        {
                            foreach($strings as $string)
                            {
                                $this->addString($string, $fileName, $lineNo);
                            }//foreach
                        }//foreach
                    }

                    if($fileInfo->stringsPlural)
                    {
//                        #var_dump($fileInfo);
                        foreach($fileInfo->stringsPlural as $lineNo => $strings)
                        {
                            foreach($strings as $singular => $plural)
                            {
                                $this->addPluralString($singular, $plural, $fileName, $lineNo);
                            }//foreach
                        }//foreach
                    }

                    continue;

                    break;

                case 'js':
                    if($this->subScope != 'js')
                    continue;

                    $fileInfo = $parsers[$ext]->parse($fileName);

                    if($fileInfo->strings)
                    {
//                        #var_dump($fileInfo);
                        foreach($fileInfo->strings as $lineNo => $strings)
                        {
                            foreach($strings as $string)
                            {
                                $this->addString($string, $fileName, $lineNo);
                            }//foreach
                        }//foreach
                    }

                    continue;
                    break;

                case 'xml':
                    //                    continue;//TEMP
                    if($this->subScope == 'js')
                    continue;

                    $fileInfo = $parsers[$ext]->parse($fileName);

                    if($fileInfo->strings)
                    {
                        foreach($fileInfo->strings as $lineNo => $strings)
                        {
                            foreach($strings as $string)
                            {
                                $this->addString($string, $fileName, $lineNo);
                            }//foreach
                        }//foreach
                    }
                    break;

                default:
//                    #      echo $fileName.'<br />';
                    break;
            }//switch
        }//foreach
    }//function
// @codingStandardsIgnoreEnd

    private function addString($string, $fileName, $lineNo)
    {
        if(array_key_exists($string, $this->strings))
        {
            $this->strings[$string]->files[$fileName][] = $lineNo;

            return;
        }

        $s = new stdClass();

        $s->files = array();
        $s->files[$fileName][] = $lineNo;

        $s->isTranslated = false;
        $s->isTranslatedInCore = false;

        $s->display = true;

        $upperKey = strtoupper($string);

        if(array_key_exists($string, $this->translations))
        {
            $translation = $this->translations[$string];

            $translation->isUsed = true;

            $s->isTranslated = true;
            $s->isTranslatedInCore = $translation->isCore;
            $s->iniLines = $translation->lines;
            $s->translation = $translation->string;
        }

        //-- Apply file filter
        if($this->fileFilter
        && JFile::getExt($fileName) != $this->fileFilter)
        {
            $s->display = false;
        }

        $this->strings[$string] = $s;

        return;
    }//function

    private function addPluralString($singular, $plural, $fileName, $lineNo)
    {
        if(array_key_exists($singular, $this->stringsPlural))
        {
            $this->stringsPlural[$singular]->files[$fileName][] = $lineNo;

            return;
        }

        $s = new stdClass();

        $s->files = array();
        $s->files[$fileName][] = $lineNo;

        $s->pluralForms = array();

        $s->isTranslated = false;
        $s->isTranslatedInCore = false;

        $s->display = true;

        //        $upperKey = strtoupper($singular);
        //
        if(array_key_exists($singular, $this->translationsPlural))
        {
            $translation = $this->translationsPlural[$singular];

            $translation->isUsed = true;

            $s->isTranslated = true;
            $s->isTranslatedInCore = $translation->isCore;
            $s->iniLines = $translation->lines;
//            #            $s->translation = $translation->string;
            $s->pluralForms = $translation->forms;
            $s->plural = $translation->plural;
        }
        else
        {
            $s->pluralForms[0] = $singular;
            $s->pluralForms[1] = $plural;
            $s->plural = $plural;
        }

        //-- Apply file filter
        if($this->fileFilter
        && JFile::getExt($fileName) != $this->fileFilter)
        {
            $s->display = false;
        }

        $this->stringsPlural[$singular] = $s;

        return;
    }//function

    private function parseLanguageFile()
    {
        $component = $this->component;
        $scope = $this->scope;
        $lang = $this->lang;

        $paths = array();

        switch($scope)
        {
            case 'site':
                $p = JPATH_SITE;
                break;

            case 'admin':
            case 'sys' :
                $p = JPATH_ADMINISTRATOR;
                break;

            default:
                echo 'Undefined scope: '.$scope;

                return;
                break;
        }//switch

        $paths[] = $p;

        if($component)
        {
            $paths[] = $p.DS.'components'.DS.$component;
        }

        $fileName = '';
        $fileName .= $lang;
        $fileName .=($component) ? '.'.$component : '';
        $fileName .=($scope == 'admin' || $scope == 'site') ? '' : '.'.$scope;
        $fileName .=($this->subScope) ? '.'.$this->subScope : '';
        $fileName .= '.'.$this->langFormatIn;

        if(true)//! JLoader::import('helpers.parsers.'.$this->langFormatIn, JPATH_COMPONENT))
        {
//            #  JError::raiseWarning(0, 'Unknown parser: '.$this->langFormatIn);

            try
            {
                $parser = JALHOO::getParser('language', $this->langFormatIn);
//                echo $parser;
            }
            catch(Exception $e)
            {
                JError::raiseWarning(0, $e->getMessage());

                return;
            }//try
//            #            return;
        }
        else//
        {
            $className = 'JLanguageCheckerParser'.ucfirst($this->langFormatIn);

            if( ! class_exists($className))
            {
                JError::raiseWarning(0, 'Required class not found: '.$className);

                return;
            }

            $parser = new $className;
        }

        foreach($paths as $p)
        {
            if($this->langFormatIn == 'ini')
            {
                $path = $p.DS.'language'.DS.$lang.DS.$fileName;
            }
            else//
            {
                $path = $p.DS.'language'.DS.'sources'.DS.$lang.DS.$fileName;
            }

            if( ! JFile::exists($path))
            {
                echo 'NOT FOUND: '.$path.'<br />';

                continue;
            }
            else//
            {
                echo 'Loaded: '.$path.'<br />';
            }

            $this->loadedFiles[] = $path;

//            #          $translations = $parser->parse($path);

            $fileInfo = $parser->parse($path);

//            #     $this->translationsPlural = $fileInfo->stringsPlural;

//            #       var_dump($translations);

            foreach($fileInfo->stringsPlural as $key => $pS)
            {
                $t = new stdClass();

//                #            $t->string = $value->string;//$this->stripQuotes($value);

                $t->plural = $pS->plural;
                $t->forms = $pS->forms;

                $t->isCore =($component) ? false : true;
                $t->lines = array();
//                #                    $t->lines[] = $lineNo + 1;

                $t->isUsed = false;

                $this->translationsPlural[$key] = $t;
            }//foreach

            foreach($fileInfo->strings as $key => $value)
            {
                $t = new stdClass();

                if(is_object($value))
                {
                    $t->string = $value->string;//$this->stripQuotes($value);

                    $t->isCore =($component) ? false : true;
                    $t->lines = array();
//                    #                    $t->lines[] = $lineNo + 1;

                    $t->isUsed = false;
                }
                else//
                {
                    $t->string = $value;
                    $t->isCore =($component) ? false : true;
                    $t->lines = array();
//                    #                    $t->lines[] = $lineNo + 1;

                    $t->isUsed = false;
                }

                $this->translations[$key] = $t;
            }//foreach

            if($fileInfo->head)
            {
                $this->head = $fileInfo->head;
            }

            return;
        }//foreach

        return;
    }//function

    private function addStrangeTHING($string, $fileName, $lineNo)
    {
        $this->strangeTHINGS[] =
        '<p>Found a <b>strange string in JText</b>....<br />'
        . sprintf('File: %s<br />Line: %d<br />Key: %s'
        , str_replace(JPATH_ROOT, 'J', $fileName), $lineNo + 1, $string)
        .'</p>';
    }//function

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = 'install')
    {
        //        JSubMenuHelper::addEntry(
        //        JText::_('COM_INSTALLER_SUBMENU_INSTALL'),
        //			'index.php?option=com_installer',
        //        $vName == 'install'
        //        );
    }//function

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     */
    public static function getActions()
    {
        //        $user	= JFactory::getUser();
        //        $result	= new JObject;
        //
        //        $assetName = 'com_installer';
        //
        //        $actions = array(
        //			'core.admin', 'core.manage', 'core.edit.state', 'core.delete'
        //			);
        //
        //			foreach ($actions as $action) {
        //			    $result->set($action,	$user->authorise($action, $assetName));
        //			}
        //
        //			return $result;
    }//function

    /**
     * Draw a standard EasyAppFooter (:P)
     *
     * @return string
     */
    public static function footer()
    {
        $xml = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR.DS.'languagechecker.xml');

        if( ! $xml) return '';

        $footer = '';

        $footer[] = '<div class="easyAppFooter">';
        $footer[] = '<div class="a">'.$xml->name.' '.$xml->version.'</div>';
        $footer[] = (string)$xml->creationDate;

        // @codingStandardsIgnoreStart - long lines here..
        $footer[] = '<br /><em>This product is not affiliated with or endorsed by the <a href="http://joomla.org">Joomla!</a> Project. It is not supported or warranted by the <a href="http://joomla.org">Joomla!</a> Project or <a href="http://opensourcematters.org/">Open Source Matters</a>.';
        $footer[] = '<br />The <a href="http://joomla.org">Joomla!</a> logo is used under a limited license granted by <a href="http://opensourcematters.org/">Open Source Matters</a> the trademark holder in the United States and other countries.</em>';
        // @codingStandardsIgnoreEnd

        $footer[] = '</div>';

        return implode("\n", $footer);
    }//function
}//class
