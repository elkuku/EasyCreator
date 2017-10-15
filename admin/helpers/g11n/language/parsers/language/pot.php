<?php
/**
 * @version SVN: $Id: pot.php 298 2010-12-17 04:41:07Z elkuku $
 * @package    g11n
 * @subpackage Parsers
 * @author     Nikolai Plath {@link http://nik-it.de}
 * @author     Created on 19-Sep-2010
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...
 *
 * @package    g11n
 */
class g11nParserLanguagePot
{
    protected $ext = 'pot';

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
     * Parse a language file - NOT NESSESARY !.
     *
     * @param string $fileName Absolute path to the file.
     *
     * @return void
     * @throws Exception
     */
    public function parse($fileName)
    {
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
                        //we have a complete entry
                        $e = new JObject;
                        $e->info = $info;
                        $e->string = $msgstr;
                        $fileInfo->strings[$msgid] = $e;//$msgstr;

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
                    $t = new stdClass();
                    $t->plural = $msg_plural;
                    $t->forms = $msg_plurals;
                    $t->info = $info;
                    $fileInfo->stringsPlural[$msgid] = $t;//$msg_plurals;

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
     * @return string
     */
    public function generate(g11nFileInfo $checker, JObject $options)
    {
        //        #2010-09-30 23:53-0500
        $dateTimeZone = new DateTimeZone(date_default_timezone_get());
        $dateTime = new DateTime('now', $dateTimeZone);

        $timeOffset = $dateTimeZone->getOffset($dateTime) / 3600;

        $contents = array();
        #$strings = $checker->getStrings();
        $strings = $checker->strings;

        #$stringsPlural = $checker->getStringsPlural();
        $stringsPlural = $checker->stringsPlural;

//        $contents[] = count($strings).' strings found'."\n";

        $contents[] = '# @version SVN: $I'.'d$'."
# SOME DESCRIPTIVE TITLE.
# Copyright (C) YEAR Free Software Foundation, Inc.
# FIRST AUTHOR <EMAIL@ADDRESS>, YEAR.
#
#, fuzzy
msgid \"\"
msgstr \"\"
\"Project-Id-Version: PACKAGE VERSION\\n\"
\"Report-Msgid-Bugs-To: wp-polyglots@lists.automattic.com\\n\"
\"POT-Creation-Date: ".date('Y-m-d H:i ').$timeOffset."00\\n\"
\"PO-Revision-Date: 2010-MO-DA HO:MI+ZONE\\n\"
\"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n\"
\"Language-Team: LANGUAGE <LL@li.org>\\n\"
\"Content-Type: text/plain; charset=CHARSET\\n\"
\"Content-Transfer-Encoding: 8bit\\n\"
\"X-Generator: g11n\\n\"
\"MIME-Version: 1.0\\n\"
\"Plural-Forms: nplurals=INTEGER; plural=EXPRESSION;\\n\"
";

        foreach($strings as $key => $string)
        {
            if($string->info)
            {
                $contents[] = trim($string->info);
            }

            if($options->get('includeLineNumbers'))
            {
                foreach($string->files as $f => $locs)
                {
                    foreach($locs as $loc)
                    {
                        $contents[] = '#: '.str_replace(JPATH_ROOT.DS, '', $f).':'.$loc;
                    }//foreach
                }//foreach
            }

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

            $contents[] = 'msgid "'.htmlentities($key).'"';
            $contents[] = 'msgstr ""';
            $contents[] = '';
        }//foreach

        foreach($stringsPlural as $key => $string)
        {
            if($options->get('includeLineNumbers'))
            {
                foreach($string->files as $f => $locs)
                {
                    foreach($locs as $loc)
                    {
                        $contents[] = '#: '.str_replace(JPATH_ROOT.DS, '', $f).':'.$loc;
                    }//foreach
                }//foreach
            }

            $key = html_entity_decode($key);
            $value = html_entity_decode($string->pluralForms[1]);

            $contents[] = 'msgid "'.htmlspecialchars($key).'"';
            $contents[] = 'msgid_plural "'.htmlspecialchars($value).'"';
            $contents[] = 'msgstr[0] ""';
            $contents[] = 'msgstr[1] ""';
            $contents[] = '';
        }//foreach

        return implode("\n", $contents);
    }//function
}//class
