<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 23-Apr-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Temporary class to convert language ini and php files from Joomla! 1.5 to 1.6.
 *
 */
class EcrLanguageConverter
{
    private $forbiddenKeys = array('null', 'yes', 'no', 'true', 'false', 'on', 'off', 'none');

    private $forbiddenChars = array('{', '}', '|', '&', '~', '!', '[', '(', ')', '^', '$');

    //"/JText::_\(\s*\'(.*)\'\s*\)|JText::_\(\s*\"(.*)\"\s*\)
    //|JText::sprintf\(\s*\'(.*)\'[-,$>()\"':\sA-z0-9]+\)
    //|JText::sprintf\(\s*\"(.*)\"[-,$>()\"'\sA-z0-9]+\)
    //|JText::printf\(\s*\'(.*)\'[-,$>()\"'\sA-z0-9]+\)
    //|JText::printf\(\s*\"(.*)\"[-,$>()\"'\sA-z0-9]+\)/iU";
    private $cleanRegEx = '';

    private $options = null;

    public $prefix = '';

    /**
     * Constructor.
     *
     * @param JObject $options Converting options
     * @param EcrProjectBase $project The project
     */
    public function __construct(JObject $options, EcrProjectBase $project)
    {
        $this->cleanRegEx = "/JText::_\(\s*\'(.*)\'\s*\)|JText::_\(\s*\"(.*)\"\s*\)"
            ."|JText::sprintf\(\s*\"(.*)\"|JText::sprintf\(\s*\'(.*)\'"
            ."|JText::printf\(\s*\'(.*)\'|JText::printf\(\s*\"(.*)\"/iU";

        if($options instanceof JObject)
        {
            $this->options = $options;
        }
        else
        {
            JFactory::getApplication()->enqueueMessage(jgettext('Invalid options for lanuage converter'), 'error');

            $this->options = new JObject;
        }

        if($this->options->get('add_prefix'))
        {
            $prefix =($project->extensionPrefix) ? $project->extensionPrefix : $project->comName.'_';

            if($this->options->get('convert_all_caps'))
            $prefix = strtoupper($tprefix);

            $this->prefix = $prefix;
        }
    }//function

    /**
     * Get tags of known languages.
     *
     * @return array
     */
    public static function getKnownLanguageTags()
    {
        $languages = JFactory::getLanguage()->getKnownLanguages();

        if(array_key_exists('xx-XX', $languages))
        {
            //-- Joomla! test language
            unset($languages['xx-XX']);
        }

        //--assure that default 'en-GB' is in first place
        $result = array('en-GB');

        foreach($languages as $key => $language)
        {
            if($key != 'en-GB')
            {
                $result[] = $key;
            }
        }//foreach

        return $result;
    }//function

    /**
     * Checks is the key is adjusted to our settings.
     *
     * @param string $key The key to look up
     * @param string $value The value to compare
     *
     * @return boolean
     */
    public function isKeyAdjusted($key, $value)
    {
        return $key == $this->adjustKey($key, $value);
    }//function

    /**
     * Checks if the key is clean according to our settings.
     *
     * @param string $key The key to test
     *
     * @return boolean
     */
    public function isCleanKey($key)
    {
        return $key == $this->cleanKey($key);
    }//function

    /**
     * Adjust the key according to our settings.
     *
     * @param string $key The key to adjust
     * @param string $value The value to adjust to
     *
     * @return string
     */
    public function adjustKey($key, $value)
    {
        if( ! $this->options->get('convert_case_code'))
        return '';

        if($key == $value
        || strlen($key) != strlen($value))
        {
            //-- strings are equal or with different length
            return $key;
        }

        //-- Adjust the key to the value
        return $value;
    }//function

    /**
     * Cleans a key according to our settings.
     *
     * @param string $key The key to clean
     *
     * @return string
     */
    public function cleanKey($key)
    {
        $key = trim($key);

        if($this->options->get('remove_bads'))
        {
            $key = str_replace($this->forbiddenChars, '', $key);
            $key = trim($key);
        }

        if($this->options->get('convert_white_space'))
        $key = str_replace(' ', '_', $key);

        if($this->options->get('convert_quotes'))
        $key = str_replace('"', '__QQ__', $key);

        if($this->options->get('convert_all_caps'))
        $key = strtoupper($key);

        if($this->options->get('convert_case_code'))
        {
        }

        if($this->options->get('add_prefix'))
        {
            if($this->prefix
            && strpos(strtolower($key), strtolower($this->prefix)) !== 0)
            {
                $key = $this->prefix.$key;
            }
        }

        if($this->options->get('convert_forbidden'))
        {
            $test = strtolower($key);

            if(in_array($test, $this->forbiddenKeys))
            {
                //-- Forbidden key add an underscore
                $key = $key.'_';
            }
            else
            {
                //-- Look if we have a forbidden key inside the key with spaces

                //-- Search forbidden keys at start
                $rex = '%^('.implode(' |', $this->forbiddenKeys).' )%i';
                preg_match_all($rex, $key, $matches);

                foreach($matches[0] as $match)
                {
                    $key = str_replace($match, trim($match).'_', $key);
                }//foreach

                $rex = '%( '.implode(' | ', $this->forbiddenKeys).' )%i';
                preg_match_all($rex, $key, $matches);

                foreach($matches[0] as $match)
                {
                    $key = str_replace($match, ' '.trim($match).'_', $key);
                }//foreach

                $rex = '%( '.implode('| ', $this->forbiddenKeys).')$%i';
                preg_match_all($rex, $key, $matches);

                foreach($matches[0] as $match)
                {
                    $key = str_replace($match, '_'.trim($match), $key);
                }//foreach
            }
        }

        return $key;
    }//function

    /**
     * Cleans errors in a language file.
     *
     * @param array $lines Lines to clean
     * @param array $errors Errors to correct
     *
     * @return array
     */
    public function cleanLangFileErrors($lines, $errors)
    {
        if( ! $errors)
        return $lines;

        for($i = 0; $i < count($lines); $i++)
        {
            $parts = $this->splitLine($lines[$i]);

            if($parts[0] != '' && $parts[1] != '')
            {
                foreach($errors as $error)
                {
                    if(strtoupper($error) == strtoupper($parts[0]))
                    {
                        $parts[0] = $this->cleanKey($parts[0]);
                        $lines[$i] = $parts[0].'='.$parts[1];
                    }
                }//foreach
            }
        }//for

        return $lines;
    }//function

    /**
     * Clean a value.
     *
     * @param string $value Value to clean
     *
     * @return string
     */
    public function cleanValue($value)
    {
        return $value;
    }//function

    /**
     * Find language key errors in PHP code.
     *
     * @param string $code The code to expect
     * @param array $bads Errors found
     *
     * @return return_type
     */
    public function findPHPErrors($code, $bads = array())
    {
        if( ! count($bads))
        return array(); //good ?

        preg_match_all($this->cleanRegEx, $code, $matches, PREG_SET_ORDER);

        $errors = array();

        foreach($matches as $match)
        {
            foreach($match as $key => $m)
            {
                $m = trim($m);

                if($m == '' || $key == 0)
                continue;

                $value = $m;
            }//foreach

            if($value)
            {
                if(in_array($value, $bads) || ! count($bads))//-count(bads)
                {
                    $errors[$value] = $match[0];
                }
            }
        }//foreach

        return $errors;
    }//function

    /**
     * Clean lines of a language file.
     *
     * @param array $lines Lines to clean
     *
     * @return array
     */
    public function cleanLines($lines)
    {
        $newLines = array();

        foreach($lines as $line)
        {
            $line = trim($line);

            if($line == '')
            {
                //-- Blank
                $newLines[] = $line;
                continue;
            }

            if(strpos($line, '#') === 0)
            {
                //-- Old style comment
                $newLines[] = ';'.substr($line, 1);
            }

            else if(strpos($line, ';') === 0)
            {
                //-- New style comment
                $newLines[] = $line;
            }

            else
            {
                $parts = explode('=', $line);

                if(count($parts) > 1)
                {
                    if(strpos($parts[1], '"') === 0)
                    {
                        //-- It's already quoted
                        $newLines[] = $line;
                    }
                    else
                    {
                        //-- Quote it..
                        $pos = strpos($line, '=');
                        $lastPart = substr($line, $pos + 1);
                        $lastPart = str_replace('"', '"__QQ__"', $lastPart);
                        $newLines[] = $parts[0].'="'.$lastPart.'"';
                    }
                }
                else
                {
                    //-- Just add it..
                    $newLines[] = $line;
                }
            }
        }//foreach

        return $newLines;
    }//function

    /**
     * Split lines of an ini file by the "=" character.
     *
     * @param string $line A single line
     *
     * @return array
     */
    public function splitLine($line)
    {
        $pos = strpos($line, '=');
        $firstPart = substr($line, 0, $pos);
        $lastPart = substr($line, $pos + 1);

        return array($firstPart, $lastPart);
    }//function

    /**
     * Get a diff table.
     *
     * @param string $origCode Original code
     * @param string $newCode New code
     * @param boolean $showAll Set true to show all lines | false to show only changed lines
     *
     * @return return_type
     */
    public function getDiffTable($origCode, $newCode, $showAll = true)
    {
        $codeOrig = explode("\n", htmlentities($origCode));
        $codeNew = explode("\n", htmlentities($newCode));
        ecrLoadHelper('DifferenceEngine');

        //--we are adding a blank line to the end.. this is somewhat 'required' by PHPdiff
        if($codeOrig[count($codeOrig) - 1] != '')
        {
            $codeOrig[] = '';
        }

        if($codeNew[count($codeNew) - 1] != '')
        {
            $codeNew[] = '';
        }

        $dwDiff = new Diff($codeOrig, $codeNew);
        $dwFormatter = new TableDiffFormatter;

        //-- Small hack to display the whole file - :|
        if($showAll)
        {
            $dwFormatter->leading_context_lines = 99999;
            $dwFormatter->trailing_context_lines = 99999;
        }

        return $dwFormatter->format($dwDiff);
    }//function
}//class
