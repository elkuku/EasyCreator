<?php
// @codingStandardsIgnoreStart


/**
 * @package    JALHOO
 * @subpackage Parser formats
 * @author     Nikolai Plath {@link http://easy-joomla.org}
 * @author     Created on 19-Sep-2010
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 *
 * Enter description here ...
 *
 * @package    JALHOO
 */
class JLanguageCheckerParserNafuIni
{
    protected $ext = 'nafuini';

    public function getExt()
    {
        return $this->ext;
    }

    /**
     * Parse an ini style language file similar to gettext files
     *
     * key
     * value
     *
     * @param string $fileName Absolute path to the file.
     *
     * @return array
     */
    ###public static function parse($fileName)
    public function parse($fileName)
    {
        $fileName = JPath::clean($fileName);

        if( ! file_exists($fileName))
        {
            return array();//@todo throw exception
        }

        $lines = explode("\n", JFile::read($fileName));

        if( ! $lines)
        {
            return array();//@todo throw exception
        }

        $format = '';
        $header = '';
        $parsing = false;

        $fileInfo = new JObject();
        $fileInfo->fileName = $fileName;
        $fileInfo->format = '';
        $fileInfo->mode = '';
        $fileInfo->strings = array();

        $previous = '';

        $strings = array();

        foreach ($lines as $line)
        {
            $line = trim($line);

            if(strpos($line, '#') === 0)
            {
                if( ! $parsing)
                {
                    preg_match('/#@@@FORMAT:\s(\w+)/', $line, $matches);
                    if($matches)
                    {
                        $fileInfo->format = $matches[1];
                    }

                    preg_match('/#@@@MODE:\s(\w+)/', $line, $matches);
                    if($matches)
                    {
                        $fileInfo->mode = $matches[1];
                    }
                }

                continue;
            }

            if( ! $line)//empty
            {
                $parsing = true;//first run
                $previous = '';

                continue;
            }

            if($previous)
            {
                if($line == "'") continue;

                $t = new stdClass();
                $t->string = $line;
                //-- Found a pair :)
                $strings[$previous] = $t;

                continue;
            }

            $previous = $line;
        }//foreach

        if( ! $strings) JFactory::getApplication()->enqueueMessage('No strings found :(', 'error');

        $fileInfo->strings = $strings;

        return $fileInfo;
    }//function

}//class
