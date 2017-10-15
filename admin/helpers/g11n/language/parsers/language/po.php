<?php
/**
 * @version SVN: $Id: po.php 303 2010-12-18 15:46:56Z elkuku $
 * @package    g11n
 * @subpackage Parsers
 * @author     Nikolai Plath {@link http://nik-it.de}
 * @author     Created on 19-Sep-2010
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Parser for po language files.
 *
 * @package g11n
 */
class g11nParserLanguagePo
{
    /**
     * File extension.
     *
     * @var string
     */
    protected $ext = 'po';

    /**
     * Get the extension.
     *
     * @return string
     */
    public function getExt()
    {
        return $this->ext;
    }//function

    /**
     * Convert to string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)__CLASS__;
    }//function

    /**
     * Parse a po style language file.
     *
     * @param string $fileName Absolute path to the language file.
     *
     * @return g11nFileInfo
     */
    public function parse($fileName)
    {
        //    ###public static function parse($fileName)
        $fileInfo = new g11nFileInfo;

        $fileName = JPath::clean($fileName);

        $fileInfo->fileName = $fileName;

        if( ! file_exists($fileName))
        {
            return $fileInfo;//@todo throw exception
        }

        $lines = explode("\n", JFile::read($fileName));

        if( ! $lines)
        {
            return $fileInfo;//@todo throw exception
        }

        $msgid = '';
        $msgstr = '';
        $msg_plural = '';
        $msg_plurals = array();

        $head = '';

        $info = '';

        $state = -1;

        $stringsPlural = array();

        foreach($lines as $line)
        {
            $line = trim($line);

            if(0 === strpos($line, '#~'))
            continue;

            $match = array();

            switch($state)
            {
                case - 1 : //Start parsing
                    if( ! $line)
                    {
                        //-- First empty line stops header
                        $state = 0;
                    }
                    else
                    {
                        $head .= $line."\n";
                    }
                    break;
                case 0://waiting for msgid
                    if(preg_match('/^msgid "(.*)"$/', $line, $match))
                    {
                        $msgid = stripcslashes($match[1]);
                        $state = 1;
                    }
                    else
                    {
                        $info .= $line."\n";
                    }
                    break;
                case 1: //reading msgid, waiting for msgstr
                    if(preg_match('/^msgstr "(.*)"$/', $line, $match))
                    {
                        $msgstr = stripcslashes($match[1]);
                        $state = 2;
                    }
                    else if(preg_match('/^msgid_plural "(.*)"$/', $line, $match))
                    {
                        $msg_plural = stripcslashes($match[1]);
                        $state = 1;
                    }
                    else if(preg_match('/^msgstr\[(\d+)\] "(.*)"$/', $line, $match))
                    {
                        $msg_plurals[stripcslashes($match[1])] = stripcslashes($match[2]);
                        $state = 1;
                    }
                    else if(preg_match('/^"(.*)"$/', $line, $match))
                    {
                        $msgid = stripcslashes($match[1]);
                    }
                    break;
                case 2: //reading msgstr, waiting for blank
                    if(preg_match('/^"(.*)"$/', $line, $match))
                    {
                        $msgstr = stripcslashes($match[1]);
                    }
                    else if(empty($line))
                    {
                        if($msgstr)
                        {
                            //we have a complete entry
                            $e = new JObject;
                            $e->info = $info;
                            $e->string = $msgstr;
                            $fileInfo->strings[$msgid] = $e;//$msgstr;
                        }

                        $state = 0;
                        $info = '';
                    }
                    break;
            }//switch

            //comment or blank line?
            if(empty($line)
            || preg_match('/^#/', $line))
            {
                if($msg_plural)
                {
                    if($msg_plurals[0])
                    {
                        $t = new stdClass();
                        $t->plural = $msg_plural;
                        $t->forms = $msg_plurals;
                        $t->info = $info;
                        $fileInfo->stringsPlural[$msgid] = $t;//$msg_plurals;
                    }

                    $msg_plural = '';
                    $msg_plurals = array();
                    $state = 0;
                }
            }
        }//foreach

        $fileInfo->head = $head;

        return $fileInfo;
    }//function

    /**
     * Generate a language file.
     *
     * @param LanguageCheckerHelper $checker LanguageCheckerHelper
     * @param JObject $options JObject
     *
     * @return void
     */
    public function generate(g11nFileInfo $fileInfo, JObject $options)
    {
        $content = array();
        #var_dump($fileInfo);
        $x = true;//...

        $head =($x) ? trim($fileInfo->head) : $checker->getHead();
        #   $head = trim($fileInfo->head);

        if($head)
        {
            $content[] = $head;
        }
        else
        {
            $content[] = '# @version SVN: $I'.'d$';
            $content[] = 'msgid ""';
            $content[] = 'msgstr ""';
        }

        $content[] = '';

        $lang =($x) ?  $fileInfo->langTag : $checker->getLang();

        $pluralStrings =($x) ? $fileInfo->stringsPlural : $checker->getStringsPlural();

        foreach($pluralStrings as $key => $string)
        {
            $value = '';//$key;//@TODO
            $info = '';

            if(array_key_exists($key, $checker->getTranslations()))
            {
                $value = $this->translations[$key]->string;
                $info = $this->translations[$key]->info;
            }
            else
            {
                $test = strtoupper($key);

                if(array_key_exists($test, $checker->getTranslations()))
                {
                    if($this->buildOpts->get('markKeyDiffers'))
                    {
                        $content[] = '# Key is upper cased :(';
                    }

                    $value = $this->translations[$test]->string;
                    $info = $this->translations[$key]->info;
                }
            }

            if($options->get('includeLineNumbers'))
            {
                foreach($string->files as $f => $locs)
                {
                    foreach($locs as $loc)
                    {
                        $content[] = '#: '.str_replace(JPATH_ROOT.DS, '', $f).':'.$loc;
                    }//foreach
                }//foreach
            }

            if( ! $value
            && $options->get('markFuzzy'))
            {
                //echo '#, fuzzy'.NL;
            }

            $content[] = 'msgid "'.htmlspecialchars($key).'"';
            $content[] = 'msgid_plural "'.htmlspecialchars($string->plural).'"';

            foreach($string->pluralForms as $k => $v)
            {
                $content[] = 'msgstr['.$k.'] "'.$v.'"';
            }//foreach

            $content[] = '';
        }//foreach

        //        $translations = $checker->getTranslations();
        //        $strings = $checker->getStrings();

        //        echo '# '.count($translations).' translations'.NL;
        //        echo '# '.count($strings).' strings'.NL;

        $checkStrings =($x) ? $fileInfo->strings : $checker->getStrings();

        foreach($checkStrings as $key => $string)
        #foreach($fileInfo->strings as $key => $string)
        {
            $key = html_entity_decode($key);

            $key = addcslashes($key, '"');

            while(strpos($key, "\\\\") != false)
            {
                $key = str_replace('\\\\', '\\', $key);
            }//while

            while(strpos($key, "\'") != false)
            {
                $key = str_replace("\'", "'", $key);
            }//while

            //            $value = '';
            //            $info = '';

            #$value = $string->string;
            $value =(isset($string->translation) && $string->translation) ? $string->translation : '';

            if( ! $value)//...brrrrrrr
            {
                $value = $string->string;//...right..
            }

            #$info = '';
            $info = trim($string->info);
            //            if(array_key_exists($key, $translations))
            //            {
            //                $value = $translations[$key]->string;
            //                $info = $translations[$key]->info;
            //            }
            //            else
            //            {
            //                $test = strtoupper($key);
            //
            //                if(array_key_exists($test, $translations))
            //                {
            //                    if($options->get('markKeyDiffers'))
            //                    {
            //                        $content[] = '# Key is upper cased :(';
            //                    }
            //
            //                    $value = $translations[$test]->string;
            //                    $info = $translations[$key]->info;
            //                }
            //            }

            if($options->get('includeLineNumbers'))
            {
                if(isset($string->files))
                {
                    foreach($string->files as $f => $locs)
                    {
                        foreach($locs as $loc)
                        {
                            $content[] = '#: '.str_replace(JPATH_ROOT.DS, '', $f).':'.$loc;
                        }//foreach
                    }//foreach
                }
            }

            if( ! $value
           && $options->get('markFuzzy')
            && $lang != 'en-GB')
            {
                $content[] = '#, fuzzy';
            }

            if($info)
            $content[] = $info;

            $content[] = 'msgid "'.htmlspecialchars($key).'"';

            if($lang == 'en-GB')
            {
                $content[] = 'msgstr "'.htmlspecialchars($key).'"';
            }
            else
            {
                $content[] = 'msgstr "'.htmlspecialchars($value).'"';
            }

            $content[] = '';
        }//foreach

        return implode(NL, $content);
    }//function
}//class
