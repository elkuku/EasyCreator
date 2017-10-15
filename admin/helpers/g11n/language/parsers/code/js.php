<?php
/**
 * @version SVN: $Id: js.php 303 2010-12-18 15:46:56Z elkuku $
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
class g11nParserCodeJS
{
    /**
     * Enter description here ...
     *
     * @param string $langFormatIn A
     *
     * @return void
     */
    public function setLangFormat($langFormatIn)
    {
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
        $this->patternPHP0 = '/';
        $this->patternPHP = '/';
        $this->patternPHP2 = '/';
        //-- Regular JText JText_('foo')
        //-- JText with parameters JText_('foo', ...)
        //        	"|".
        //        $this->patternPHP0 .= $cmds['php1']."\(\s*\'(.*[\\'].*)\'";//|".$cmds['php1']."\(\s*\"(.*)\"";
        //        $this->patternPHP0 .= $cmds['php1']."\(\s*\'(.*)\'";//|".$cmds['php1']."\(\s*\"(.*)\"";
        //        $this->patternPHP0 .= $cmds['php1']."\(\s*'((?:[^\\']+|\\.)*)'";
        //        $this->patternPHP0 .= $cmds['php1']."\(\s*'(.|\\(?='))*?[^\\\\]'";
        $this->patternPHP0 .= $cmds['php1']."\('(.*?)'\)\,?";
        $this->patternPHP0 .= '|';
        $this->patternPHP0 .= $cmds['php1']."\('(.*?)'\)";
        //        $this->patternPHP0 .= '|';
        //        $this->patternPHP0 .= $cmds['php1']."\('(.*?)'\)";
        //$this->patternPHP0 .= '|';
        //$this->patternPHP0 .= $cmds['php1']."\(\s*'(.*)'\s*\)";
        //        $this->patternPHP0 .= '|';
        //        $this->patternPHP0 .= $cmds['php1']."\(\s*\'(.*)\'\s*\,|".$cmds['php1']."\(\s*\"(.*)\"\s*\,";
        //        $this->patternPHP0 .= '|';
        //        $this->patternPHP0 .= $cmds['php1']."\(\s*\'(.*)\'\s*\)|".$cmds['php1']."\(\s*\"(.*)\"\s*\)";

        $this->patternPHP2 .= $cmds['php1']."\(\s*\'(.*)\'\)\s*[\,|\.]|".$cmds['php1']."\(\s*\"(.*)\"\)\s*[\,|\.]";
        $this->patternPHP2 .= "|".$cmds['php1']."\(\s*\'(.*)\'\s*\)";//|".$cmds['php1']."\(\s*\"(.*)\"\s*\)";//.
        //$this->patternPHP2 = '//';
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

        $this->patternPHP0 .= '/i';
        $this->patternPHP .= '/iU';
        $this->patternPHP2 .= '/iU';

        //        var_dump($this->patternPHP0);
        //        var_dump($this->patternPHP2);
        $this->patternPHPPlural =
         "/".$cmds['php_plural']."\(\s*\'(.*)\'\s*,\s*\'(.*)\',"
         ."|".$cmds['php_plural']."\(\s*\"(.*)\"\s*,\s*\"(.*)\"/iU";
         //         echo $this->patternPHPPlural.'<br />';
         //--RegEx pattern for Joomla.JText in Javascript files
         $this->patternJs =
         //--In case there is the second parameter (default) set
            "/".$cmds['js']."\(\s*\"(.*)\"|".$cmds['js']."\(\s*\'(.*)\'"
            //--'''normal''' use...
         ."|".$cmds['js']."\(\s*\'(.*)\'\s*\)|".$cmds['js']."\(\s*\"(.*)\"\s*\)/iU";
    }//function

    /**
     * Parse a file.
     *
     * @param string $fileName File to parse
     *
     * @return object g11nFileInfo
     */
    public function parse($fileName)
    {
        $fileName = JPath::clean($fileName);

        $fileInfo = new g11nFileInfo;

        $fileInfo->fileName = $fileName;

        //--Search PHP files
        $contents = JFile::read($fileName);

        $lines = explode("\n", $contents);

        foreach($lines as $lineNo => $line)
        {
            $line = trim($line);

            if( ! $line)
            continue;

            if(preg_match_all($this->patternPHP0, $line, $matches, PREG_SET_ORDER))
            {
                foreach($matches as $match)
                {
                    foreach($match as $i => $string)
                    {
                        if($i == 0 || ! $string)
                        continue;

                        $fileInfo->strings[$lineNo + 1][] = $string;
                    }//foreach
                }//foreach
            }

            //            elseif(preg_match_all($this->patternPHP, $line, $matches, PREG_SET_ORDER))
            //            {
            //                foreach($matches as $match)
            //                {
            //                    foreach($match as $i => $string)
            //                    {
            //                        if($i == 0 || ! $string) continue;
            //
            //                        if(strpos($string, '$') !== false)
            //                        {
            //              #              $this->addStrangeTHING($string, $fileName, $lineNo + 1);
            //echo $string;
            //                            continue;
            //                        }
            //
            //      #                  $fileInfo->strings[$lineNo + 1][] = $string;
            //
            //#                        $this->addString($string, $fileName, $lineNo + 1);
            //                    }//foreach
            //                }//foreach
            //            }
            //            elseif(preg_match_all($this->patternPHP2, $line, $matches, PREG_SET_ORDER))
            //            {
            //                foreach($matches as $match)
            //                {
            //                    foreach($match as $i => $string)
            //                    {
            //                        if($i == 0 || ! $string) continue;
            //
            //                        if(strpos($string, '$') !== false)
            //                        {
            //                            $this->addStrangeTHING($string, $fileName, $lineNo + 1);
            //
            //                            continue;
            //                        }
            //
            //                        #       $this->addString($string, $fileName, $lineNo + 1);
            //                    }//foreach
            //                }//foreach
            //            }

            preg_match_all($this->patternPHPPlural, $line, $matches, PREG_SET_ORDER);

            $s1 = '';

            foreach($matches as $match)
            {
                //                var_dump($matches);

                foreach($match as $i=> $string)
                {
                    if($i == 0 || ! $string)
                    continue;

                    if(strpos($string, '$') !== false)
                    {
                        $this->addStrangeTHING($string, $fileName, $lineNo + 1);

                        continue;
                    }

                    if($i / 2 != (int)($i / 2))
                    {
                        $s1 = $string;

                        continue;
                    }

                    //                    $this->addPluralString($s1, $string, $fileName, $lineNo + 1);
                    if(array_key_exists($match[1], $fileInfo->stringsPlural))
                    {
                        echo sprintf('###### Key %s aready defined in file %s on line %s'
                        , $match[1], $fileName, $lineNo);

                        continue;
                    }

                    $fileInfo->stringsPlural[$lineNo + 1][$match[1]] = $match[2];
                }//foreach
            }//foreach
        }//foreach

        return $fileInfo;
    }//function
}//class
